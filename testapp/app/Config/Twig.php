<?php

namespace Config;

use Vheissu\CiTwig\Config\Twig as BaseTwig;

/**
 * Twig Configuration
 *
 * Customize Twig settings for your application.
 */
class Twig extends BaseTwig
{
    /**
     * Enable debug mode during development
     */
    public bool $debug = true;

    /**
     * Auto-reload templates when they change
     */
    public bool $autoReload = true;

    /**
     * PHP functions available in templates
     */
    public array $functions = [
        'base_url',
        'site_url',
    ];

    /**
     * PHP functions available as filters
     */
    public array $filters = [
        'esc',
    ];
}
