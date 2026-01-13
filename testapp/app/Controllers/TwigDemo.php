<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TwigDemo extends Controller
{
    public function __construct()
    {
        // Load URL helper for base_url(), site_url() etc.
        helper('url');
    }

    public function index()
    {
        $twig = service('twig');

        return $twig->render('demo/index', [
            'title'   => 'CI-Twig Demo',
            'message' => 'Twig templating is working in CodeIgniter 4!',
            'items'   => ['PHP 8.1+', 'CodeIgniter 4.6+', 'Twig 3.22+'],
        ]);
    }

    public function variables()
    {
        $twig = service('twig');

        // Set variables using the fluent API
        $twig->set('page_title', 'Variable Demo')
             ->set('author', 'Dwayne Charrington')
             ->set('year', date('Y'));

        // Set a global variable
        $twig->set('site_name', 'CI-Twig Test App', global: true);

        return $twig->render('demo/variables', [
            'extra' => 'This was passed directly to render()',
        ]);
    }

    public function functions()
    {
        $twig = service('twig');

        // Register a custom function
        $twig->registerFunction('greet', fn($name) => "Hello, {$name}!");

        // Register a custom filter
        $twig->registerFilter('reverse', fn($str) => strrev($str));

        return $twig->render('demo/functions');
    }
}
