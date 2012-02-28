# Twig Templating for Codeigniter

Want to use the powerful Twig templating library in Codeigniter? How about no Scott, no. Okay, just kidding. This library does what the title suggests, it lets you use Twig templates in your Codeigniter applications.

Lets face it, the in-built parser and view system is crap. It doesn't offer anything signficant or awesome, if you're familiar with Smarty or familiar with the way Django allows you to template everything is a lot simpler.

## How to use Ci Twig

To use this library load it or better yet, autoload it. Then you can do stuff like this in your controllers.

    $data['title'] = "Page Title";
    $data['content'] = "<p>Welcome to my sexy page being displayed by Twig inside of Codeigniter</p>";

    $this->twig->parse('yourtemplate.twig', $data);