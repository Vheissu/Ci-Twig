<?php
/**
 * CI Twig
 *
 * Twig templating for Codeigniter with support for
 * Modular Extensions HMVC
 *
 * @package   CI Twig
 * @author    Dwayne Charrington
 * @copyright 2014 Dwayne Charrington and Github contributors
 * @link      http://ilikekillnerds.com
 * @license   Licenced under MIT
 * @version   1.1
 */

class Twig {

    protected $CI;

    protected $_twig;
    protected $_twig_loader;

    protected $_template_directories = array();
    protected $_local_vars           = array();
    protected $_global_vars          = array();

    protected $_cache_dir;
    protected $_debug;

    public function __construct()
    {
        if (!DEFINED("EXT")) define("EXT", ".php");

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'third_party/Twig/lib/Twig');
        require_once (string) "Autoloader" . EXT;

        // Fire off the Twig register bootstrap function...
        Twig_Autoloader::register();

        // Get CI instance
        $this->CI = get_instance();

        // Load the Twig config file
        $this->CI->config->load('twig');

        // Add in default Twig locations
        $this->add_template_location($this->CI->config->item('twig.locations'));

        // Get locations
        $this->_set_template_locations();

        $this->_twig_loader = new Twig_Loader_Filesystem($this->_template_directories);

        // Get environment config settings
        $environment = $this->CI->config->item("twig.environment");

        // Set our cache path or status
        $environment["cache"] = ($environment["cache_status"]) ? $environment["cache_location"] : FALSE;

        $twig_environment = array(
            "cache"         => $environment["cache"],
            "debug"         => $environment["debug_mode"],
            "auto_reload"   => $environment["auto_reload"],
            "autoescape"    => $environment["autoescape"],
            "optimizations" => $environment["optimizations"]
        );

        $this->_twig = new Twig_Environment($this->_twig_loader, $twig_environment);

        if ( $this->CI->config->item("twig.functions") )
        {
            foreach ( $this->CI->config->item("twig.functions") AS $function )
            {
                $this->register_function($function);
            }
        }

        if ( $this->CI->config->item("twig.filters") )
        {
            foreach ( $this->CI->config->item("twig.filters") AS $filter )
            {
                $this->register_filter($filter);
            }
        }

    }

    /**
     * Set
     *
     * Allows setting variables (both local and global) for Twig
     * templates to access and use without requiring assignment
     * via the parse method
     *
     * @param mixed $key
     * @param mixed $value
     * @param bool   $global
     * @returns object
     *
     */
    public function set($key, $value = "", $global = FALSE)
    {
        // Do we have an array of values to set?
        if ( is_array($key) )
        {
            foreach ($key AS $k => $v)
            {
                if (!$global)
                {
                    $this->_local_vars[$k] = $v;
                }
                else
                {
                    $this->_twig->addGlobal($k, $v);
                    $this->_global_vars[$k] = $v;
                }
            }
        }
        else
        {
            if ( !$global )
            {
                $this->_local_vars[$key] = $value;
            }
            else
            {
                $this->_twig->addGlobal($key, $value);
                $this->_global_vars[$key] = $value;
            }
        }

        // Return class for chaining
        return $this;
    }

    /**
     * Unset
     *
     * Unset a local or global variable
     *
     * @param mixed $key
     * @param bool $global
     * @returns object
     *
     */
    public function unset_var($key, $global = FALSE)
    {
        if ( !$global )
        {
            if ( array_key_exists($key, $this->_local_vars) )
            {
                unset($this->_local_vars[$key]);
            }
        }
        else
        {
            if ( array_key_exists($key, $this->_global_vars) )
            {
                unset($this->_global_vars[$key]);
            }
        }

        // Return class for chaining
        return $this;
    }

    /**
     * Load the template and return the data
     *
     * @param mixed $template
     * @param mixed $data
     * @returns string
     *
     */
    public function parse($template, $data = array(), $return = FALSE)
    {
        if (stripos($template, '.') === FALSE)
        {
            $template . config_item('twig.extension');
        }

        // Merge supplied data with any local variables
        $data = array_merge($this->_local_vars, $data);

        $template = $this->_twig->loadTemplate($template);

        if ($return === true)
        {
            return $template->render($data);
        }
        else
        {
            return $template->display($data);
        }
    }

    /**
     * Parse String
     * Parse a string and return it as a string or display it
     *
     * @param mixed $string
     * @param mixed $data
     * @param mixed $return
     * @returns void
     *
     */
    public function parse_string($string, $data = array(), $return = false)
    {
        $string = $this->_twig->loadTemplate($string);

        // Merge supplied data with any local variables
        $data = array_merge($this->_local_vars, $data);

        if ( $return === true )
        {
            return $string->render($data);
        }
        else
        {
            return $string->display($data);
        }
    }

    /**
     * Register Function
     *
     * Alllows you to register functions for use within
     * the Twig environment.
     *
     * @param mixed $name
     * @returns object
     *
     */
    public function register_function($name)
    {
        $this->_twig->addFunction($name, new Twig_Function_Function($name));

        // Return class for chaining
        return $this;
    }

    /**
     * Register Filter
     *
     * Alllows you to register filters for use within
     * the Twig environment.
     *
     * @param mixed $name
     * @returns object
     *
     */
    public function register_filter($name)
    {
        $this->_twig->addFilter($name, new Twig_Filter_Function($name));

        // Return class for chaining
        return $this;
    }

    /**
     * __get
     *
     * A PHP magic function that catches all requests for
     * something that might not exist.
     *
     * @param mixed $key
     * @return array or boolean on false
     *
     */
    public function __get($key)
    {
        if ( array_key_exists($key, $this->_global_vars) )
        {
            return $this->_global_vars[$key];
        }
        elseif ( array_key_exists($key, $this->_local_vars) )
        {
            return $this->_local_vars[$key];
        }

        // Not found
        return FALSE;
    }

    /**
     * __set
     *
     * A PHP magic function that catches all requests trying
     * to set a variable that doesn't exist or isn't public
     *
     * @param mixed $key
     * @return array or boolean on false
     *
     */
    public function __set($key, $value)
    {
        if ( ! array_key_exists($key, $this->_local_vars) )
        {
            $this->_local_vars[$key] = $value;
        }
    }

    /**
     * Add Template Location
     *
     * Adds a new template location to the template
     * locations array. Allows adding of template folders
     * before view loading for example.
     *
     * @param mixed $location
     * @returns object
     *
     */
    public function add_template_location($location_var)
    {
        if ( is_array($location_var) )
        {
            foreach ($location_var AS $location => $offset)
            {
                if ( is_dir($location) )
                {
                    $this->_template_directories[] = $location;
                }
            }
        }
        else
        {
            if ( is_dir($location_var) )
            {
                $this->_template_directories[] = $location_var;
            }
        }
    }

    /**
     * Set Template Locations
     *
     * Iterate over all modules and add their paths in
     *
     */
    private function _set_template_locations()
    {
        if ( method_exists($this->CI->router, 'fetch_module') )
        {
            $this->_module = $this->CI->router->fetch_module();

            if ( $this->_module )
            {
                $module_locations = Modules::$locations;

                foreach ($module_locations AS $location => $offset)
                {
                    if ( is_dir($location . $this->_module . '/views') )
                    {
                        $this->_template_directories[] = $location . $this->_module . '/views';
                    }
                }
            }
        }

        if ( $this->_twig_loader )
        {
            $this->_twig_loader->setPaths($this->_template_directories);
        }
    }

}
