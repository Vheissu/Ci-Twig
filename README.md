# CI-Twig

Twig templating integration for CodeIgniter 4.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vheissu/ci-twig.svg)](https://packagist.org/packages/vheissu/ci-twig)
[![PHP Version](https://img.shields.io/packagist/php-v/vheissu/ci-twig.svg)](https://php.net/)
[![License: MIT](https://img.shields.io/packagist/l/vheissu/ci-twig.svg)](https://opensource.org/licenses/MIT)
[![Total Downloads](https://img.shields.io/packagist/dt/vheissu/ci-twig.svg)](https://packagist.org/packages/vheissu/ci-twig)

## Requirements

- PHP 8.1+
- CodeIgniter 4.6+

## Installation

### Step 1: Install via Composer

```bash
composer require vheissu/ci-twig
```

### Step 2: Register the Service

Add the Twig service to your `app/Config/Services.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function twig(bool $getShared = true): \Vheissu\CiTwig\Twig
    {
        if ($getShared) {
            return static::getSharedInstance('twig');
        }

        return new \Vheissu\CiTwig\Twig(config('Twig'));
    }
}
```

### Step 3: Create Configuration (Optional)

Create `app/Config/Twig.php` to customize settings:

```php
<?php

namespace Config;

use Vheissu\CiTwig\Config\Twig as BaseTwig;

class Twig extends BaseTwig
{
    public bool $debug = true;

    public array $functions = [
        'base_url',
        'site_url',
    ];
}
```

That's it! You're ready to use Twig templates.

## Quick Start

### In Your Controller

```php
<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Load URL helper if using base_url(), site_url(), etc.
        helper('url');

        $twig = service('twig');

        return $twig->render('home', [
            'title' => 'Welcome',
            'user'  => $this->getUser(),
        ]);
    }
}
```

### Create a Template

Create `app/Views/home.twig`:

```twig
<!DOCTYPE html>
<html>
<head>
    <title>{{ title }}</title>
</head>
<body>
    <h1>{{ title }}</h1>
    <p>Hello, {{ user.name }}!</p>
</body>
</html>
```

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `extension` | string | `.twig` | Template file extension |
| `templatePaths` | array | `[APPPATH . 'Views/']` | Directories to search for templates |
| `cacheEnabled` | bool | `false` | Enable compiled template caching |
| `cachePath` | string | `WRITEPATH . 'cache/twig/'` | Cache directory |
| `debug` | bool | `false` | Enable debug mode (enables `dump()`) |
| `autoReload` | bool | `true` | Recompile templates when source changes |
| `autoescape` | string\|bool | `'html'` | Auto-escape strategy (`'html'`, `'js'`, `false`) |
| `functions` | array | `[]` | PHP functions to expose in templates |
| `filters` | array | `[]` | PHP functions to use as filters |
| `strictVariables` | bool | `false` | Throw exception on undefined variables |
| `enableCiExtension` | bool | `true` | Enable built-in CI4 helper functions |
| `modulePaths` | bool\|array | `true` | Auto-discover HMVC module view paths |

### Production Configuration Example

```php
<?php

namespace Config;

use Vheissu\CiTwig\Config\Twig as BaseTwig;

class Twig extends BaseTwig
{
    public bool $debug = false;
    public bool $cacheEnabled = true;
    public bool $autoReload = false;

    public array $functions = [
        'base_url',
        'site_url',
        'lang',
    ];

    public array $filters = [
        'esc',
    ];
}
```

## Usage

### Rendering Templates

```php
$twig = service('twig');

// Render and return as string
$html = $twig->render('pages/about', ['title' => 'About Us']);

// Render and output directly
$twig->display('pages/about', ['title' => 'About Us']);

// Extension is optional
$twig->render('pages/about');      // Looks for pages/about.twig
$twig->render('pages/about.twig'); // Same result

// Render a string as a template
$html = $twig->renderString('Hello, {{ name }}!', ['name' => 'World']);
```

### Setting Variables

```php
$twig = service('twig');

// Set a single variable
$twig->set('title', 'My Page');

// Set multiple variables
$twig->set([
    'title' => 'My Page',
    'items' => ['one', 'two', 'three'],
]);

// Set a global variable (persists across all renders)
$twig->set('site_name', 'My Website', global: true);

// Variables can also be passed directly to render()
$twig->render('page', ['title' => 'Passed directly']);
```

### Registering Custom Functions

```php
$twig = service('twig');

// Register an existing PHP function
$twig->registerFunction('strtoupper');

// Register with a custom callback
$twig->registerFunction('asset', fn($path) => base_url("assets/{$path}"));

// Use in template: {{ asset('css/style.css') }}
```

### Registering Custom Filters

```php
$twig = service('twig');

// Register an existing PHP function as a filter
$twig->registerFilter('ucfirst');

// Register with a custom callback
$twig->registerFilter('money', fn($n) => '$' . number_format($n, 2));

// Use in template: {{ price|money }}
```

### Method Chaining

All methods return `$this` for fluent chaining:

```php
return service('twig')
    ->set('title', 'Dashboard')
    ->set('user', $user)
    ->registerFunction('format_date', fn($d) => $d->format('M j, Y'))
    ->render('dashboard');
```

## Template Syntax

CI-Twig uses standard [Twig syntax](https://twig.symfony.com/doc/3.x/templates.html).

### Variables

```twig
{{ title }}
{{ user.name }}
{{ user.getEmail() }}
{{ items[0] }}
```

### Control Structures

```twig
{% if user.isAdmin %}
    <span class="badge">Admin</span>
{% endif %}

{% for item in items %}
    <li>{{ item.name }}</li>
{% else %}
    <li>No items found</li>
{% endfor %}
```

### Template Inheritance

**base.twig:**
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Default{% endblock %}</title>
</head>
<body>
    {% block content %}{% endblock %}
</body>
</html>
```

**page.twig:**
```twig
{% extends 'base.twig' %}

{% block title %}{{ page_title }}{% endblock %}

{% block content %}
    <h1>{{ page_title }}</h1>
    <p>{{ content }}</p>
{% endblock %}
```

### Including Partials

```twig
{% include 'partials/header.twig' %}
{% include 'partials/nav.twig' with {'active': 'home'} %}
```

## Built-in CI4 Functions

When `enableCiExtension` is `true` (default), these functions are available:

```twig
{# URLs - requires url helper loaded #}
{{ base_url('assets/style.css') }}
{{ site_url('users/profile') }}
{{ current_url() }}

{# Security #}
{{ csrf_field() }}
{{ csrf_token() }}
{{ csrf_hash() }}

{# Forms - requires form helper loaded #}
{{ form_open('users/save') }}
{{ form_input('email', old('email')) }}
{{ form_close() }}

{# Form validation #}
{{ old('field_name', 'default') }}
{{ validation_errors() }}

{# Language #}
{{ lang('App.welcome') }}

{# Session #}
{{ session('user_id') }}

{# Environment #}
{{ env('app.baseURL') }}

{# Escaping #}
{{ esc(user_input, 'html') }}

{# Routing #}
{{ route_to('users.show', user.id) }}
```

### Debug Mode

When `debug` is `true`:

```twig
{{ dump(variable) }}
{{ dump() }}  {# Dumps all available variables #}
```

## HMVC / Module Support

CI-Twig automatically discovers module view paths. Templates in your modules are available without configuration:

```
app/
├── Modules/
│   └── Blog/
│       └── Views/
│           └── posts/
│               └── index.twig
```

```php
// This works automatically
$twig->render('posts/index');
```

Or manually specify module paths:

```php
public array|bool $modulePaths = [
    APPPATH . 'Modules/Blog/Views/',
    APPPATH . 'Modules/Shop/Views/',
];
```

## Managing Template Paths

```php
$twig = service('twig');

// Add a path to the search list
$twig->addPath(APPPATH . 'Themes/default/');

// Prepend a path (searched first - useful for theme overrides)
$twig->prependPath(APPPATH . 'Themes/custom/');

// Add a namespaced path
$twig->addPath(APPPATH . 'Modules/Blog/Views/', 'blog');
```

Use namespaced templates:

```twig
{% include '@blog/sidebar.twig' %}
{% extends '@blog/layout.twig' %}
```

## Advanced: Accessing Twig Directly

```php
$twig = service('twig');

// Get the Twig\Environment instance
$env = $twig->getEnvironment();
$env->addExtension(new MyCustomExtension());

// Get the Twig\Loader\FilesystemLoader
$loader = $twig->getLoader();
```

## Migrating from CI3

| CodeIgniter 3 | CodeIgniter 4 |
|---------------|---------------|
| `$this->load->library('twig')` | `service('twig')` |
| `$this->twig->parse('view', $data)` | `service('twig')->render('view', $data)` |
| `$this->twig->parse('view', $data, TRUE)` | `service('twig')->render('view', $data)` |
| `$this->twig->parse('view', $data, FALSE)` | `service('twig')->display('view', $data)` |
| `config/twig.php` (array) | `app/Config/Twig.php` (class) |

## Development

The repository includes a test application for development:

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run the test application
cd testapp
composer install
php spark serve
# Visit http://localhost:8080/twigdemo
```

## License

MIT License. See [LICENSE](LICENSE) for details.

## Credits

- [Dwayne Charrington](https://github.com/Vheissu)
- [Twig](https://twig.symfony.com/) by Symfony
