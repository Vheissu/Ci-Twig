<?php
/**
 * CI Twig
 *
 * Twig templating for Codeigniter with support for
 * Modular Extensions HMVC
 *
 * @package   CI Twig
 * @author      Dwayne Charrington
 * @copyright  2014 Dwayne Charrington and Github contributors
 * @link           http://ilikekillnerds.com
 * @license     Licenced under MIT
 * @version     1.1
 */

// Twig template extension (default)
$config['twig.extension'] = ".twig";

// Default template locations
$config['twig.locations'] = array(
    APPPATH . "views/" => "../views/",
    FCPATH . "views/"   => "../../views/"
);

$config['twig.functions'] = array(
    // Register functions for use in Twig templates here
);

$config['twig.filters'] = array(
    // Register filters for use in Twig templates here
);

$config['twig.environment'] = array(

    "cache_location" => APPPATH . "cache/twig/",
    "cache_status"    => FALSE,
    "auto_reload"      => NULL,
    "debug_mode"    => FALSE,
    "autoescape"      => FALSE,
    "optimizations"    => -1
);
