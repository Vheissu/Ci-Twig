<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific
| PHPUnit test case class. By default, that class is "PHPUnit\Framework\TestCase".
| You can change it by using the "pest()->extend()" function.
|
*/

pest()->extend(Tests\TestCase::class)->in('Unit', 'Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain
| conditions. The "expect()" function provides access to a set of "expectations"
| methods that you can use to assert different things.
|
*/

expect()->extend('toBeRenderedTemplate', function () {
    return $this->toBeString()->not->toBeEmpty();
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code
| specific to your project that you don't want to repeat in every file. Here
| you can also expose helpers as global functions to help you reduce the
| number of lines of code in your test files.
|
*/

function createTwigConfig(array $overrides = []): \Vheissu\CiTwig\Config\Twig
{
    $config = new \Vheissu\CiTwig\Config\Twig();
    $config->templatePaths = [__DIR__ . '/_support/Views/'];
    $config->enableCiExtension = false;
    $config->modulePaths = false;

    foreach ($overrides as $key => $value) {
        $config->{$key} = $value;
    }

    return $config;
}

function createTwig(array $configOverrides = []): \Vheissu\CiTwig\Twig
{
    return new \Vheissu\CiTwig\Twig(createTwigConfig($configOverrides));
}
