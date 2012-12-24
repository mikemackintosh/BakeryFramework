<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Utilities;

use \Bakery\Application;
use \Bakery\Provider\ArrayAccessProvider;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class URLGenerator extends ArrayAccessProvider implements \ArrayAccess{
	
	private
		$required = array();
	
	/**
	 * @param Application $app
	 */
	public function __construct( Application $app ){
		
		$this['app'] = $app;
		
		foreach($app->routes as $route){
			
			$classes = new \ReflectionFunction($route->callback);
			$params = $classes->getParameters();
				
			$this[$route->_routeName] = $route->prefix;
			
			$this->required[$route->_routeName] = $params;
				
		}

	}
	
	/**
	 * @param unknown_type $bind
	 * @param unknown_type $params
	 * @return string
	 */
	public function generate( $bind, $params = array() ){
			
		$protocol = "http://";
		
		if($this['app']['request']['isSecure']){
			$protocol = "https://";
		}
		
		if(isset($this[$bind])){
			$out = $this[$bind];
				
			if(!empty($this->required[$bind])){
				
				$i = 0;
				foreach($this->required[$bind] as $parm){
					
					if($parm->getName() != "request"){
						
						$out = str_replace("{{$parm->getName()}}", $params[$i++], $out);
					}
					
				}
				
			}
			
			return $protocol.$this['app']['request']['hostname'].$out;
			
		}
				
	}
	
}

?>