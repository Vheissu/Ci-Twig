<?php

class MY_Loader extends CI_Loader {

    public function get_vars()
    {
        return $this->_ci_cached_vars;
    }

}