<?php

class Twig {

	protected $CI;
	
	protected $_twig;
	
	protected $_template_dir;
	protected $_cache_dir;
	protected $_debug;
	
	public function __construct()
	{
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'third_party/Twig');
        require_once (string) "Autoloader" . EXT;
		
		// Get CI instance
		$this->CI =& get_instance();

        // Load the Twig config file
        $this->CI->config->load('twig');

        // Required Twig paths and variables, modify in config/twig.php
        $this->_template_dir = config_item('twig.location');
        $this->_cache_dir    = config_item('twig.cache_location');
        $this->_debug        = config_item('twig.debug');
		
		Twig_Autoloader::register();
		
		$loader = new Twig_Loader_Filesystem($this->_template_dir);
		
		$this->_twig = new Twig_Environment($loader, array(
                'cache' => $this->_cache_dir,
                'debug' => $this->_debug,
        ));
        
	}

    /**
     * Override the default template location
     *
     * @param mixed $location
     * @returns void
     */
    public function set_location($location)
    {
        $this->_template_dir = $location;
    }
	
	/**
	* Load the template and return the data
	*
	* @param mixed $template
	* @param mixed $data
	* @returns string
	*/
	public function parse($template, $data = array(), $return = false)
	{
        if (stripos($template, '.') === false) {
            $template . config_item('twig.extension');
        }
	     
	    $template = $this->_twig->loadTemplate($template);
	    
	    if ( is_array($data) )
	    {
	        $data = array_merge($data, $this->CI->load->get_vars());
	    }
	    
	    if ( $return === true )
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
     */
    public function parse_string($string, $data = array(), $return = false)
    {
        $string = $this->_twig->loadTemplate($string);

        if ( is_array($data) )
        {
            $data = array_merge($data, $this->CI->load->get_vars());
        }

        if ( $return === true )
        {
            return $string->render($data);
        }
        else
        {
            return $string->display($data);
        }

    }

}