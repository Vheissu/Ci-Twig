<?php

declare(strict_types=1);

namespace Vheissu\CiTwig;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vheissu\CiTwig\Config\Twig as TwigConfig;
use Vheissu\CiTwig\Extensions\CodeIgniterExtension;

/**
 * CI-Twig
 *
 * Twig templating integration for CodeIgniter 4.
 *
 * @package   CI-Twig
 * @author    Dwayne Charrington
 * @copyright 2014-2025 Dwayne Charrington
 * @license   MIT
 */
class Twig
{
    protected Environment $environment;
    protected FilesystemLoader $loader;
    protected TwigConfig $config;

    /**
     * Local variables (cleared after each render)
     *
     * @var array<string, mixed>
     */
    protected array $localVars = [];

    /**
     * Global variables (persist across renders)
     *
     * @var array<string, mixed>
     */
    protected array $globalVars = [];

    public function __construct(?TwigConfig $config = null)
    {
        $this->config = $config ?? config('Twig');
        $this->initializeLoader();
        $this->initializeEnvironment();
        $this->registerConfiguredFunctions();
        $this->registerConfiguredFilters();
        $this->registerExtensions();
    }

    /**
     * Initialize the filesystem loader with configured paths
     */
    protected function initializeLoader(): void
    {
        $paths = array_filter($this->config->templatePaths, 'is_dir');

        // Add module paths if HMVC support is enabled
        if ($this->config->modulePaths !== false) {
            $modulePaths = $this->discoverModulePaths();
            $paths = array_merge($paths, $modulePaths);
        }

        $this->loader = new FilesystemLoader($paths);
    }

    /**
     * Discover module view paths for HMVC support
     *
     * @return array<string>
     */
    protected function discoverModulePaths(): array
    {
        $paths = [];

        if (is_array($this->config->modulePaths)) {
            // User provided explicit module paths
            foreach ($this->config->modulePaths as $modulePath) {
                if (is_dir($modulePath)) {
                    $paths[] = $modulePath;
                }
            }
        } elseif ($this->config->modulePaths === true) {
            // Auto-discover from CI4's module configuration
            $modules = config('Modules');

            if ($modules !== null && property_exists($modules, 'discoverInNamespaces') && $modules->discoverInNamespaces) {
                $autoloader = service('autoloader');
                $namespaces = $autoloader->getNamespace();

                foreach ($namespaces as $namespace => $nsPaths) {
                    foreach ($nsPaths as $nsPath) {
                        $viewPath = rtrim($nsPath, '/\\') . '/Views';
                        if (is_dir($viewPath) && ! in_array($viewPath, $paths, true)) {
                            $paths[] = $viewPath;
                        }
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * Initialize the Twig environment with configuration
     */
    protected function initializeEnvironment(): void
    {
        $this->environment = new Environment($this->loader, [
            'cache'            => $this->config->cacheEnabled ? $this->config->cachePath : false,
            'debug'            => $this->config->debug,
            'auto_reload'      => $this->config->autoReload,
            'autoescape'       => $this->config->autoescape,
            'optimizations'    => $this->config->optimizations,
            'strict_variables' => $this->config->strictVariables,
        ]);

        if ($this->config->debug) {
            $this->environment->addExtension(new DebugExtension());
        }
    }

    /**
     * Register extensions
     */
    protected function registerExtensions(): void
    {
        if ($this->config->enableCiExtension) {
            $this->environment->addExtension(new CodeIgniterExtension());
        }
    }

    /**
     * Register functions from configuration
     */
    protected function registerConfiguredFunctions(): void
    {
        foreach ($this->config->functions as $function) {
            if (is_array($function) && isset($function['name'], $function['callback'])) {
                $this->registerFunction($function['name'], $function['callback']);
            } elseif (is_string($function)) {
                $this->registerFunction($function);
            }
        }
    }

    /**
     * Register filters from configuration
     */
    protected function registerConfiguredFilters(): void
    {
        foreach ($this->config->filters as $filter) {
            if (is_array($filter) && isset($filter['name'], $filter['callback'])) {
                $this->registerFilter($filter['name'], $filter['callback']);
            } elseif (is_string($filter)) {
                $this->registerFilter($filter);
            }
        }
    }

    /**
     * Set template variables
     *
     * @param string|array<string, mixed> $key   Variable name or array of key-value pairs
     * @param mixed                       $value Variable value (ignored if $key is array)
     * @param bool                        $global Whether to set as global variable
     */
    public function set(string|array $key, mixed $value = null, bool $global = false): static
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setVariable($k, $v, $global);
            }
        } else {
            $this->setVariable($key, $value, $global);
        }

        return $this;
    }

    /**
     * Set a single variable
     */
    protected function setVariable(string $key, mixed $value, bool $global): void
    {
        if ($global) {
            $this->environment->addGlobal($key, $value);
            $this->globalVars[$key] = $value;
        } else {
            $this->localVars[$key] = $value;
        }
    }

    /**
     * Unset a variable
     *
     * @param string $key    Variable name
     * @param bool   $global Whether to unset from global variables
     */
    public function unset(string $key, bool $global = false): static
    {
        if ($global) {
            unset($this->globalVars[$key]);
        } else {
            unset($this->localVars[$key]);
        }

        return $this;
    }

    /**
     * Clear all local variables
     */
    public function clearLocals(): static
    {
        $this->localVars = [];

        return $this;
    }

    /**
     * Render a template and return the output
     *
     * @param string               $template Template name (with or without extension)
     * @param array<string, mixed> $data     Template variables
     */
    public function render(string $template, array $data = []): string
    {
        $template = $this->normalizeTemplateName($template);
        $data = array_merge($this->localVars, $data);

        return $this->environment->load($template)->render($data);
    }

    /**
     * Render a template and output directly
     *
     * @param string               $template Template name (with or without extension)
     * @param array<string, mixed> $data     Template variables
     */
    public function display(string $template, array $data = []): void
    {
        $template = $this->normalizeTemplateName($template);
        $data = array_merge($this->localVars, $data);

        $this->environment->load($template)->display($data);
    }

    /**
     * Parse a template (backwards compatibility)
     *
     * @param string               $template Template name
     * @param array<string, mixed> $data     Template variables
     * @param bool                 $return   Whether to return the output
     *
     * @deprecated Use render() or display() instead
     */
    public function parse(string $template, array $data = [], bool $return = false): ?string
    {
        if ($return) {
            return $this->render($template, $data);
        }

        $this->display($template, $data);

        return null;
    }

    /**
     * Render a string template
     *
     * @param string               $templateString Template content as string
     * @param array<string, mixed> $data           Template variables
     */
    public function renderString(string $templateString, array $data = []): string
    {
        $template = $this->environment->createTemplate($templateString);
        $data = array_merge($this->localVars, $data);

        return $template->render($data);
    }

    /**
     * Register a PHP function for use in templates
     *
     * @param string        $name     Function name in templates
     * @param callable|null $callback Callback (defaults to function with same name)
     */
    public function registerFunction(string $name, ?callable $callback = null): static
    {
        $callback ??= $name;

        $this->environment->addFunction(new TwigFunction($name, $callback));

        return $this;
    }

    /**
     * Register a PHP function as a Twig filter
     *
     * @param string        $name     Filter name in templates
     * @param callable|null $callback Callback (defaults to function with same name)
     */
    public function registerFilter(string $name, ?callable $callback = null): static
    {
        $callback ??= $name;

        $this->environment->addFilter(new TwigFilter($name, $callback));

        return $this;
    }

    /**
     * Add a template path at runtime
     *
     * @param string $path      Directory path
     * @param string $namespace Optional namespace for the path
     */
    public function addPath(string $path, string $namespace = FilesystemLoader::MAIN_NAMESPACE): static
    {
        if (is_dir($path)) {
            $this->loader->addPath($path, $namespace);
        }

        return $this;
    }

    /**
     * Prepend a template path (higher priority)
     *
     * @param string $path      Directory path
     * @param string $namespace Optional namespace for the path
     */
    public function prependPath(string $path, string $namespace = FilesystemLoader::MAIN_NAMESPACE): static
    {
        if (is_dir($path)) {
            $this->loader->prependPath($path, $namespace);
        }

        return $this;
    }

    /**
     * Get the underlying Twig Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * Get the filesystem loader
     */
    public function getLoader(): FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * Get the configuration
     */
    public function getConfig(): TwigConfig
    {
        return $this->config;
    }

    /**
     * Normalize template name by adding extension if missing
     */
    protected function normalizeTemplateName(string $template): string
    {
        if (! str_contains($template, '.')) {
            $template .= $this->config->extension;
        }

        return $template;
    }

    /**
     * Magic getter for variables
     */
    public function __get(string $key): mixed
    {
        return $this->globalVars[$key]
            ?? $this->localVars[$key]
            ?? null;
    }

    /**
     * Magic setter for local variables
     */
    public function __set(string $key, mixed $value): void
    {
        $this->localVars[$key] = $value;
    }

    /**
     * Magic isset check
     */
    public function __isset(string $key): bool
    {
        return isset($this->globalVars[$key]) || isset($this->localVars[$key]);
    }
}
