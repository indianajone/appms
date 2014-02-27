<?php namespace Spescina\Timthumb;

class TimthumbExt extends \Spescina\Timthumb\lib\timthumb\timthumb {
        
    protected $params;
        
    public function __construct($args)
    {       
     	$this->params = $args;
     	if(!in_array('QUERY_STRING', $_SERVER)) $_SERVER['QUERY_STRING'] = http_build_query($args);
        parent::__construct();
       
    }
        
    public static function start()
    {	
		$tim = new TimthumbExt(func_get_arg(0));
		$tim->handleErrors();
		$tim->securityChecks();
		if($tim->tryBrowserCache()){
			exit(0);
		}
		$tim->handleErrors();
		if(FILE_CACHE_ENABLED && $tim->tryServerCache()){
			exit(0);
		}
		$tim->handleErrors();
		$tim->run();
		$tim->handleErrors();
		exit(0);
	}
        
	protected function param($property, $default = ''){
		if (isset ($this->params[$property])) {
			return $this->params[$property];
		} else {
			return $default;
		}
	}
}