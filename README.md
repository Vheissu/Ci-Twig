# CI Twig — Twig Templating For Codeigniter

The default Codeigniter view and parsing libraries leave a lot to be desired (mainly proper templating, better caching, better syntax..). Twig is a tried and tested templating library for PHP, so it makes sense to use it for your Codeigniter applications.

## Installation

Copy all files from this repo into your Codeigniter application directory. Simple. It is recommended you add the library 'twig' to your config/autoload.php array so you don't have to keep loading it.

## Does this support Modular Extensions HMVC?

Yes. Yes it does. Support for module view folders comes standard with CI Twig.

## Does this work with Codeigniter 3.0?

Yes. This library is tried and tested with both the stable version of Codeigniter as well as the forever not complete Codeigniter 3.0.

## How can I use Codeigniter helper functions in my templates?

In the config/twig.php file you will see two array values. One allows you to define functions for use in Twig templates and the other allows you to define custom filters.

The following example allows you to use base_url in your Twig templates, provided you have loaded the "url" helper somewhere in your app.

    $config['twig.functions'] = array(
        "base_url"
    );

## How to use CI Twig in your projects

To use this library load it or better yet, autoload it. Then you can do stuff like this in your controllers.

    // Loading the Ci Twig library manually
    $this->load->library('twig');

    $data['title'] = "Page Title";
    $data['content'] = "<p>Welcome to my sexy page being displayed by Twig inside of Codeigniter</p>";

    $this->twig->parse('yourtemplate.twig', $data);

Then in your templates to print out the value of the variable title, you would do the following:

    {{ title }}

Ci Twig comes with an example controller and template for you to test it out of the box. Simply copy all files into your application/ directory and then visit your app in the browser and load the twigtest controller.
