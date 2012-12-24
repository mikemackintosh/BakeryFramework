<?php 

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. 
 */

namespace Bakery;

use \Bakery\Interfaces\ControllerProviderInterface;

/**
 * Route
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class Route{
	
	public
		$prefix, 
		$callback,
		$_routeName;
	
	private
		$_accessProtocol = array("http", "https"),
		$_accessMethods = array("get", "post");
	
	public function __construct( $prefix, $callback){
		
		$this->_routeName = md5(microtime());
		
		$this->prefix = $prefix;
		$this->callback = $callback;
	}
	
	public function method( $method ){
		$this->_accessMethods = explode("|", strtolower($method));
		
		return $this;
	}
	
	public function requireHttps(){
		$this->_accessProtocol = array('https');
		
		return $this;
	}
	
	public function requireHttp(){
		$this->_accessProtocol = array('http');
		
		return $this;
	}
	
	public function regex( $variable, $pattern){
		$this->_uriPattern[$variable] = $pattern;
		
		return $this;
	}
	
	public function bind( $name ){
		$this->_routeName = $name;
	}
	
	
	/*
	 * @TODO: FInish implementing verification methods
	 */
	
	public function getMethod(){
		return $this->_accessMethods;
	}

	public function allowGet(){
		return in_array( "get", $this->_accessMethods );
	}

	public function allowPost(){
		return in_array( "post", $this->_accessMethods );
	}
}