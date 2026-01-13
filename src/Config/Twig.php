<?php

declare(strict_types=1);

namespace Vheissu\CiTwig\Config;

/**
 * Twig Configuration
 *
 * Configure the Twig templating engine for CodeIgniter 4.
 * Users can override these settings by creating app/Config/Twig.php
 * that extends this class.
 */
class Twig
{
    /**
     * Template file extension
     */
    public string $extension = '.twig';

    /**
     * Paths to search for templates.
     * Defaults to APPPATH/Views if empty.
     *
     * @var array<string>
     */
    public array $templatePaths = [];

    /**
     * Enable template caching for production
     */
    public bool $cacheEnabled = false;

    /**
     * Path for cached/compiled templates
     */
    public string $cachePath = '';

    /**
     * Enable debug mode (adds dump() function)
     */
    public bool $debug = false;

    /**
     * Auto-reload templates when source changes.
     * Useful during development.
     */
    public bool $autoReload = true;

    /**
     * Auto-escape strategy.
     * Options: 'html', 'js', 'css', 'url', 'html_attr', or false to disable
     *
     * @var string|bool
     */
    public string|bool $autoescape = 'html';

    /**
     * Optimization level.
     * -1 = all optimizations enabled
     * 0 = no optimizations
     */
    public int $optimizations = -1;

    /**
     * PHP functions to expose in templates.
     * Example: ['base_url', 'site_url', 'lang']
     *
     * @var array<string|array{name: string, callback: callable}>
     */
    public array $functions = [];

    /**
     * PHP functions to expose as Twig filters.
     * Example: ['esc', 'nl2br', 'strtoupper']
     *
     * @var array<string|array{name: string, callback: callable}>
     */
    public array $filters = [];

    /**
     * Strict variables mode.
     * When true, Twig throws an exception for undefined variables.
     */
    public bool $strictVariables = false;

    /**
     * Enable the CodeIgniter extension.
     * Provides CI4 helpers like csrf_field(), old(), session(), etc.
     */
    public bool $enableCiExtension = true;

    /**
     * Module paths for HMVC support.
     * Set to true to auto-detect from CI4's module configuration,
     * or provide an array of module paths.
     *
     * @var bool|array<string>
     */
    public bool|array $modulePaths = true;

    public function __construct()
    {
        // Set default template path if none configured
        if (empty($this->templatePaths) && defined('APPPATH')) {
            $this->templatePaths = [APPPATH . 'Views/'];
        }

        // Set default cache path if not configured
        if (empty($this->cachePath) && defined('WRITEPATH')) {
            $this->cachePath = WRITEPATH . 'cache/twig/';
        }
    }
}
