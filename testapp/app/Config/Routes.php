<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Twig Demo routes
$routes->get('twigdemo', 'TwigDemo::index');
$routes->get('twigdemo/variables', 'TwigDemo::variables');
$routes->get('twigdemo/functions', 'TwigDemo::functions');
