# Twig Templating for Codeigniter

Want to use the powerful Twig templating library in Codeigniter? How about no Scott, no. Okay, just kidding. This library does what the title suggests, it lets you use Twig templates in your Codeigniter applications.

Lets face it, the in-built parser and view system is crap. It doesn't offer anything signficant or awesome, if you're familiar with Smarty or familiar with the way Django allows you to template everything is a lot simpler.

## Installation

Copy all files from this repo into your Codeigniter application directory. Simple. It is recommended you add the library 'twig' to your config/autoload.php array so you don't have to keep loading it.

## How to use Ci Twig

To use this library load it or better yet, autoload it. Then you can do stuff like this in your controllers.

    // Loading the Ci Twig library manually
    $this->load->library('twig');

    $data['title'] = "Page Title";
    $data['content'] = "<p>Welcome to my sexy page being displayed by Twig inside of Codeigniter</p>";

    $this->twig->parse('yourtemplate.twig', $data);

Then in your templates to print out the value of the variable title, you would do the following:

    {{ title }}

Ci Twig comes with an example controller and template for you to test it out of the box. Simply copy all files into your application/ directory and then visit your app in the browser and load the twigtest controller.