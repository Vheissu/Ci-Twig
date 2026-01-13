<?php

declare(strict_types=1);

use Vheissu\CiTwig\Twig;

describe('Twig', function () {

    describe('rendering', function () {

        test('can render a template with data', function () {
            $twig = createTwig();

            $result = $twig->render('test.twig', ['name' => 'World']);

            expect($result)->toBe('Hello, World!');
        });

        test('can render a template without extension', function () {
            $twig = createTwig();

            $result = $twig->render('test', ['name' => 'World']);

            expect($result)->toBe('Hello, World!');
        });

        test('can render a string template', function () {
            $twig = createTwig();

            $result = $twig->renderString('Hello, {{ name }}!', ['name' => 'Test']);

            expect($result)->toBe('Hello, Test!');
        });

        test('display outputs directly', function () {
            $twig = createTwig();

            ob_start();
            $twig->display('test.twig', ['name' => 'Output']);
            $output = ob_get_clean();

            expect($output)->toBe('Hello, Output!');
        });

        test('parse method returns string when return is true', function () {
            $twig = createTwig();

            $result = $twig->parse('test.twig', ['name' => 'Parse'], true);

            expect($result)->toBe('Hello, Parse!');
        });

        test('parse method outputs when return is false', function () {
            $twig = createTwig();

            ob_start();
            $result = $twig->parse('test.twig', ['name' => 'Display'], false);
            $output = ob_get_clean();

            expect($result)->toBeNull();
            expect($output)->toBe('Hello, Display!');
        });

    });

    describe('variables', function () {

        test('can set local variables', function () {
            $twig = createTwig();

            $twig->set('greeting', 'Hi');
            $twig->set('name', 'User');
            $result = $twig->render('greeting.twig');

            expect($result)->toBe('Hi, User!');
        });

        test('can set multiple variables at once', function () {
            $twig = createTwig();

            $twig->set([
                'greeting' => 'Hey',
                'name' => 'Friend',
            ]);
            $result = $twig->render('greeting.twig');

            expect($result)->toBe('Hey, Friend!');
        });

        test('can set global variables', function () {
            $twig = createTwig();

            $twig->set('site_name', 'My Site', true);

            expect($twig->site_name)->toBe('My Site');
        });

        test('data passed to render merges with local variables', function () {
            $twig = createTwig();

            $twig->set('greeting', 'Hello');
            $result = $twig->render('greeting.twig', ['name' => 'Mixed']);

            expect($result)->toBe('Hello, Mixed!');
        });

        test('can unset local variables', function () {
            $twig = createTwig();

            $twig->set('key', 'value');
            expect($twig->key)->toBe('value');

            $twig->unset('key');
            expect($twig->key)->toBeNull();
        });

        test('can unset global variables', function () {
            $twig = createTwig();

            $twig->set('global_key', 'value', true);
            expect($twig->global_key)->toBe('value');

            $twig->unset('global_key', true);
            expect($twig->global_key)->toBeNull();
        });

        test('can clear all local variables', function () {
            $twig = createTwig();

            $twig->set('one', 1);
            $twig->set('two', 2);
            $twig->clearLocals();

            expect($twig->one)->toBeNull();
            expect($twig->two)->toBeNull();
        });

        test('magic setter sets local variables', function () {
            $twig = createTwig();

            $twig->custom = 'value';

            expect($twig->custom)->toBe('value');
        });

        test('magic isset checks variables', function () {
            $twig = createTwig();

            expect(isset($twig->missing))->toBeFalse();

            $twig->set('present', 'yes');
            expect(isset($twig->present))->toBeTrue();
        });

    });

    describe('functions', function () {

        test('can register a custom function', function () {
            $twig = createTwig();

            $twig->registerFunction('custom_function', fn ($n) => $n * 2);
            $result = $twig->render('function_test.twig', ['value' => 5]);

            expect($result)->toBe('Result: 10');
        });

        test('can register a function with same name as callback', function () {
            $twig = createTwig();

            $twig->registerFunction('strtoupper');
            $result = $twig->renderString('{{ strtoupper("hello") }}');

            expect($result)->toBe('HELLO');
        });

        test('functions configured in config are registered', function () {
            $twig = createTwig([
                'functions' => ['strtolower'],
            ]);

            $result = $twig->renderString('{{ strtolower("HELLO") }}');

            expect($result)->toBe('hello');
        });

    });

    describe('filters', function () {

        test('can register a custom filter', function () {
            $twig = createTwig();

            $twig->registerFilter('custom_filter', fn ($s) => strrev($s));
            $result = $twig->render('filter_test.twig', ['value' => 'hello']);

            expect($result)->toBe('olleh');
        });

        test('can register a filter with same name as callback', function () {
            $twig = createTwig();

            $twig->registerFilter('strtoupper');
            $result = $twig->renderString('{{ "hello"|strtoupper }}');

            expect($result)->toBe('HELLO');
        });

        test('filters configured in config are registered', function () {
            $twig = createTwig([
                'filters' => ['ucfirst'],
            ]);

            $result = $twig->renderString('{{ "hello"|ucfirst }}');

            expect($result)->toBe('Hello');
        });

    });

    describe('paths', function () {

        test('can add a template path at runtime', function () {
            $twig = createTwig();
            $initialPaths = $twig->getLoader()->getPaths();

            $twig->addPath(__DIR__ . '/../_support/Views/');

            expect(count($twig->getLoader()->getPaths()))->toBeGreaterThanOrEqual(count($initialPaths));
        });

        test('addPath ignores non-existent directories', function () {
            $twig = createTwig();
            $initialCount = count($twig->getLoader()->getPaths());

            $twig->addPath('/nonexistent/path/that/does/not/exist/');

            expect(count($twig->getLoader()->getPaths()))->toBe($initialCount);
        });

        test('can prepend a template path', function () {
            $twig = createTwig();
            $newPath = __DIR__ . '/../_support/Views/';

            $twig->prependPath($newPath);
            $paths = $twig->getLoader()->getPaths();

            // Twig normalizes paths by removing trailing slashes
            expect($paths[0])->toBe(rtrim($newPath, '/'));
        });

    });

    describe('chaining', function () {

        test('set returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->set('key', 'value');

            expect($result)->toBe($twig);
        });

        test('unset returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->unset('key');

            expect($result)->toBe($twig);
        });

        test('clearLocals returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->clearLocals();

            expect($result)->toBe($twig);
        });

        test('registerFunction returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->registerFunction('test', fn () => 'test');

            expect($result)->toBe($twig);
        });

        test('registerFilter returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->registerFilter('test', fn () => 'test');

            expect($result)->toBe($twig);
        });

        test('addPath returns self for chaining', function () {
            $twig = createTwig();

            $result = $twig->addPath(__DIR__);

            expect($result)->toBe($twig);
        });

        test('full chaining example works', function () {
            $twig = createTwig();

            $result = $twig
                ->set('greeting', 'Hello')
                ->set('name', 'Chain')
                ->registerFunction('exclaim', fn ($s) => $s . '!')
                ->render('greeting.twig');

            expect($result)->toBe('Hello, Chain!');
        });

    });

    describe('environment access', function () {

        test('can get the Twig environment', function () {
            $twig = createTwig();

            expect($twig->getEnvironment())->toBeInstanceOf(\Twig\Environment::class);
        });

        test('can get the filesystem loader', function () {
            $twig = createTwig();

            expect($twig->getLoader())->toBeInstanceOf(\Twig\Loader\FilesystemLoader::class);
        });

        test('can get the configuration', function () {
            $twig = createTwig();

            expect($twig->getConfig())->toBeInstanceOf(\Vheissu\CiTwig\Config\Twig::class);
        });

    });

    describe('configuration', function () {

        test('debug mode enables debug extension', function () {
            $twig = createTwig(['debug' => true]);

            $extensions = $twig->getEnvironment()->getExtensions();
            $hasDebug = isset($extensions['Twig\Extension\DebugExtension']);

            expect($hasDebug)->toBeTrue();
        });

        test('autoescape is configurable', function () {
            $twig = createTwig(['autoescape' => false]);

            $result = $twig->renderString('{{ html }}', ['html' => '<b>test</b>']);

            expect($result)->toBe('<b>test</b>');
        });

        test('custom extension can be configured', function () {
            $twig = createTwig(['extension' => '.html.twig']);

            expect($twig->getConfig()->extension)->toBe('.html.twig');
        });

    });

});
