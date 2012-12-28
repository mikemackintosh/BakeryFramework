<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Manager\CLI;


use Bakery\Route;
use Bakery\Application;
use Bakery\Manager\CLI\ResponseManager;
use Bakery\Provider\ArrayAccessProvider;
use Bakery\Interfaces\RequestResponseInterface;

/**
 * @author sixeightzero
 *
 */
class RequestManager extends ArrayAccessProvider implements \ArrayAccess,RequestResponseInterface {
	
	/**
	 * @return \Bakery\Manager\RequestManager
	 */
	public function __construct( Application $app ){

		$this['app'] = $app;
		$this['longopts'] = array();
		$this['options'] = array();
		
		array_shift($_SERVER['argv']);

		$this['arguments'] = $_SERVER['argv'];
		$this['uri'] = "";
				
		foreach( $this['arguments'] as $arg ){

			if( preg_match("/^[-]+([A-Za-z0-9-_]+)(=?(.*))$/im", $arg, $match)){	
				
				$this['options'] = array_merge($this['options'], array($match[1] => $match[3]));
				
			}
			else{
				
				$this['uri'] .= "$arg ";
				
			}
		
		}
				
		return $this;
		
	}
	
	/**
	 * @param Application $app
	 * @return function/class instance or false on error
	 */
	public function response( ){
		
		try{
			
			foreach($this['app']->routes as $route){
				
				$this['app']['hologram']->setModule("DBG:MGR")->log(\Bakery\Utilities\Hologram::DEBUG, "Checking Route $route->prefix");

				// If last character is trailing slash, make optional
				if(substr($route->prefix, -1) == "\/"){
					$route->prefix .= "?";
				}

				if(preg_match("`^{$route->prefix}[\s]+$`", $this['uri'])){
					
					$this['app']['hologram']->setModule("ROUTING")->log(\Bakery\Utilities\Hologram::NORMAL, "Route Match Found");

					$invoker = (new \ReflectionFunction( $route->callback ));
					
					$_invokerArgs = array();
					
					foreach($invoker->getParameters() as $arg){
						
						if($arg->getName() == "request"){
							$_invokerArgs[] = $this;
						}
						
						else if($arg->getName() == "response"){
							$_invokerArgs[] = new OutputManager();
						
						}
						
						
					}
					/*96257/87-5/*/
					return $this['instance'] = new ResponseManager($this, $invoker->invokeArgs($_invokerArgs));
					
				}
				
			}
			
			return new ResponseManager($this, self::NONEXISTANT_PAGE);
			
		}
		catch(\Exception $e){
				
			$this['app']['hologram']->setModule("EXCEPTION")->log(\Bakery\Utilities\Hologram::DEBUG, $e->getMessage() );
		
			$this['instance'] = new ResponseManager($this);
			
			$this['instance'] = (new OutputManager())->error( $e->getMessage() );
			
			return $this['instance'];				
				
		}
		
	}
	
	public function getOption( $option = NULL ){
		
		if(array_key_exists( $option, $this['options'] ) && !is_null($option)){
			return $this['options'][$option];
		}
		
		return false;
	}
	
	public function getOptions(){
		
		return $this['options'];
		
	}
	
}