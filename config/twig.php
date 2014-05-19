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

// Twig template caching location
$config['twig.cache_location'] = APPPATH . "cache/twig/";

// Debug mode turned on or off for Twig?
$config['twig.debug'] = false;
