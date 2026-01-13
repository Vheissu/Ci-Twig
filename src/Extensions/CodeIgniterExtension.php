<?php

declare(strict_types=1);

namespace Vheissu\CiTwig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * CodeIgniter Twig Extension
 *
 * Provides CodeIgniter 4 helper functions and globals for use in Twig templates.
 */
class CodeIgniterExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Get extension name
     */
    public function getName(): string
    {
        return 'codeigniter';
    }

    /**
     * Get global variables available in all templates
     *
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        return [
            'CI_VERSION' => \CodeIgniter\CodeIgniter::CI_VERSION,
        ];
    }

    /**
     * Get functions available in templates
     *
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            // URL Helpers
            new TwigFunction('base_url', $this->safeCall('base_url')),
            new TwigFunction('site_url', $this->safeCall('site_url')),
            new TwigFunction('current_url', $this->safeCall('current_url')),
            new TwigFunction('previous_url', $this->safeCall('previous_url')),
            new TwigFunction('uri_string', $this->safeCall('uri_string')),
            new TwigFunction('anchor', $this->safeCall('anchor'), ['is_safe' => ['html']]),

            // Security Helpers
            new TwigFunction('csrf_token', fn () => csrf_token()),
            new TwigFunction('csrf_hash', fn () => csrf_hash()),
            new TwigFunction('csrf_field', fn () => csrf_field(), ['is_safe' => ['html']]),

            // Form Helpers
            new TwigFunction('old', fn (?string $key = null, $default = null) => old($key, $default)),
            new TwigFunction('set_value', $this->safeCall('set_value')),
            new TwigFunction('form_open', $this->safeCall('form_open'), ['is_safe' => ['html']]),
            new TwigFunction('form_close', $this->safeCall('form_close'), ['is_safe' => ['html']]),
            new TwigFunction('form_hidden', $this->safeCall('form_hidden'), ['is_safe' => ['html']]),
            new TwigFunction('form_input', $this->safeCall('form_input'), ['is_safe' => ['html']]),
            new TwigFunction('form_password', $this->safeCall('form_password'), ['is_safe' => ['html']]),
            new TwigFunction('form_textarea', $this->safeCall('form_textarea'), ['is_safe' => ['html']]),
            new TwigFunction('form_dropdown', $this->safeCall('form_dropdown'), ['is_safe' => ['html']]),
            new TwigFunction('form_checkbox', $this->safeCall('form_checkbox'), ['is_safe' => ['html']]),
            new TwigFunction('form_radio', $this->safeCall('form_radio'), ['is_safe' => ['html']]),
            new TwigFunction('form_submit', $this->safeCall('form_submit'), ['is_safe' => ['html']]),
            new TwigFunction('form_button', $this->safeCall('form_button'), ['is_safe' => ['html']]),
            new TwigFunction('form_label', $this->safeCall('form_label'), ['is_safe' => ['html']]),

            // Session
            new TwigFunction('session', fn (?string $key = null) => session($key)),

            // Language
            new TwigFunction('lang', fn (string $line, array $args = []) => lang($line, $args)),

            // Services
            new TwigFunction('service', fn (string $name, ...$args) => service($name, ...$args)),

            // Config
            new TwigFunction('config', fn (string $name) => config($name)),

            // Validation errors
            new TwigFunction('validation_errors', $this->safeCall('validation_errors'), ['is_safe' => ['html']]),
            new TwigFunction('validation_list_errors', $this->safeCall('validation_list_errors'), ['is_safe' => ['html']]),

            // Miscellaneous
            new TwigFunction('env', fn (string $key, $default = null) => env($key, $default)),
            new TwigFunction('esc', fn ($data, string $context = 'html', ?string $encoding = null) => esc($data, $context, $encoding)),
            new TwigFunction('route_to', $this->safeCall('route_to')),
        ];
    }

    /**
     * Create a safe callable that returns empty string if function doesn't exist
     *
     * @return callable
     */
    protected function safeCall(string $functionName): callable
    {
        return static function (...$args) use ($functionName) {
            if (function_exists($functionName)) {
                return $functionName(...$args);
            }

            return '';
        };
    }
}
