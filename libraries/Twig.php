<?php

class Twig {

	protected $CI;
	
	protected $_template_dir;
	protected $_cache_dir;
	protected $_debug;
	
	public function __construct()
	{
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'third_party/Twig');
        require_once (string) "Autoloader" . EXT;
        
        Twig_Autoloader::register();
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
	    $loader = new Twig_Loader_Filesystem($this->_template_dir);
	    $twig = new Twig_Environment($loader, array('cache' => $this->_cache_dir,'debug' => $this->_debug));
	     
	    $template = $twig->loadTemplate($template);
	    
	    if (is_array($data))
	    {
	        $data = array_merge($data, $this->ci->load->_ci_cached_vars);
	    }
	    
	    if ($return === true)
	    {
	        return $template->render($data);
	    }
	    else
	    {
	        return $template->display($data);
	    }
	}

}