<?php

declare(strict_types=1);

namespace Vheissu\CiTwig\Config;

use CodeIgniter\Config\BaseService;
use Vheissu\CiTwig\Twig;

/**
 * Services Configuration
 *
 * Provides the Twig service for CodeIgniter 4's service locator.
 *
 * To use this, add the following to your app/Config/Services.php:
 *
 *     use Vheissu\CiTwig\Config\Services as TwigServices;
 *
 *     public static function twig(bool $getShared = true): \Vheissu\CiTwig\Twig
 *     {
 *         return TwigServices::twig($getShared);
 *     }
 *
 * Or simply copy the twig() method to your Services.php file.
 */
class Services extends BaseService
{
    /**
     * Get the Twig service
     *
     * @param bool $getShared Whether to return a shared instance
     */
    public static function twig(bool $getShared = true): Twig
    {
        if ($getShared) {
            return static::getSharedInstance('twig');
        }

        // Try to get user's config first, fall back to package default
        $config = config('Twig');

        if (! $config instanceof Twig) {
            $config = new Twig();
        }

        return new \Vheissu\CiTwig\Twig($config);
    }
}
