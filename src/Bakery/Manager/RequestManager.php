<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Manager;

use Bakery\Provider\ArrayAccessProvider;

use Bakery\Application;
use Bakery\Route;
use Bakery\Interfaces\RequestResponseInterface;
use Bakery\Manager\ResponseManager;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class RequestManager extends ArrayAccessProvider implements \ArrayAccess,RequestResponseInterface {
	
	/**
	 * @return \Bakery\Manager\RequestManager
	 */
	public function __construct( Application $app ){

		/**
		 * Store $app locally
		 */
		$this['app'] = $app;
		
		/**
		 * HTTP request method
		 */
		$this['method'] = strtolower($_SERVER['REQUEST_METHOD']); // GET, PUT, TRACE, POST, DELETE 
		
		/**
		 * Check if HTTPS is set or not
		 */
		$this['isSecure'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? true : false);
		
		/**
		 * The requested URI
		 */
		$this['uri'] = $_SERVER['REQUEST_URI'];
		
		/**
		 * Lets grab the HTTP_HOST and parse it out
		 */
		$this['subdomain'] = $tld = "";
		
		// If a registered TLD is found, add it to the domain 
		// @TODO: Needs to be refactored and simplified
		$_domainParts = preg_split('/\./', strtolower($_SERVER['HTTP_HOST']));
		if(preg_match('/com|net|org|biz|tv|uk|me|mobi|travel|gov|us/', $_domainParts[sizeof($_domainParts)-1])){
			$tld = ".".array_pop($_domainParts);
		}
		
		/**
		 * 
		 */
		$this['domain'] = $_domainParts[sizeof($_domainParts)-1].$tld;
		
		if(sizeof($_domainParts) > 1){
			$this['subdomain'] = $_domainParts[sizeof($_domainParts)-2];
		}

		// If .json ext exists, set json mode to true
		$this['json'] = (strstr($_SERVER['REQUEST_URI'], ".json") ? true : false);
		
		if(($strpos = strpos($this['uri'], "?"))){
			$this['uri'] = substr($this['uri'], 0 , $strpos);
			$this['get_str'] = substr($this['uri'], $strpos);
		}
		
		$this['hostname'] = $_SERVER['HTTP_HOST'];
		$this['requestTime'] = $_SERVER['REQUEST_TIME_FLOAT'];
		
		/**
		 * Detect if the HTTP REFERER is set
		 */
		$this['referer'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);
		
		/**
		 * Accessing host and port
		 */
		$this['remoteHost'] = $_SERVER['REMOTE_ADDR'];
		$this['remotePort'] = $_SERVER['REMOTE_PORT'];
		
		/**
		 * Accessed host and port
		 */
		$this['localHost'] = $_SERVER['SERVER_ADDR'];
		$this['localPort'] = $_SERVER['SERVER_PORT'];
		
		/**
		 * Pointer to $_REQUEST superglobal
		 */
		$this['request'] = $_REQUEST;
				
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::DEBUG, "Request Received: {$this['uri']}");
		
		return $this;
		
	}
	
	/**
	 * Serves the request. Handles the routing request
	 * 
	 * @return ResponseManager Instance
	 */
	public function response( ){
		
		try{
			
			foreach($this['app']->routes as $route){
				print_r($route);
				die();
				echo $route->getDomain()."<br />";
				
				if($route->getDomain() != "" && $route->getDomain() == $this['domain']){
					echo "matched!";
				}
				
				$this['app']['hologram']->setModule("DBG:MGR")->log(\Bakery\Utilities\Hologram::DEBUG, "Checking Route $route->prefix");
				
				// If last character is trailing slash, make optional
				if(substr($route->prefix, -1) == "/"){
					$route->prefix .= "?";
				}
				
				if(preg_match("`^{$route->prefix}?$`", $this['uri'])){
					
					$this['app']['hologram']->setModule("ROUTING")->log(\Bakery\Utilities\Hologram::NORMAL, "Route Match Found");
					
					if( ( $response = $this->_validateRouteRequirenments( $route ) ) !== false ){
						
						$this['app']['hologram']->setModule("DBG:MGR")->log(\Bakery\Utilities\Hologram::DEBUG, "Matched Route!");
						
						return new ResponseManager($this, $response);
											
					}
					
					$invoker = (new \ReflectionFunction( $route->callback ));
					return $this['instance'] = new ResponseManager($this, $invoker->invoke($this));
					
				}
				else if(array_key_exists("_uriPattern", $route)){
	
					foreach($route->_uriPattern as $var => $pattern){
							
						$route->prefix = str_replace("{".$var."}", "(?P<$var>$pattern)", $route->prefix);
					}
	
					// If last character is trailing slash, make optional
					if(substr($this['uri'], -1) == "/"){
						$route->prefix .= "/?";
					}
	
					// Check to see if a match exists
					if(preg_match("`^{$route->prefix}$`", $this['uri'], $matches)){
						
						$this['app']['hologram']->setModule("ROUTING")->log(\Bakery\Utilities\Hologram::NORMAL, "Route Match Found");
							
						// Remove all numeric array indicies
						$matches = b_filter("numeric_keys", $matches);
						
						// Validate route requirements passed from the route statement
						if( ( $response = $this->_validateRouteRequirenments( $route )) !== false ){
													
							return new ResponseManager($this, $response);
	
						}
						
						$invoker = (new \ReflectionFunction( $route->callback ));
						
						if($invoker->getParameters()[0]->getName() == 'request'){
							
							return $this['instance'] = new ResponseManager($this, (new \ReflectionFunction( $route->callback ))->invokeArgs( array($this) + $matches ));
						
						}
						
						return $this['instance'] = new ResponseManager($this, (new \ReflectionFunction( $route->callback ))->invokeArgs( $matches ));
												
					}
					
				}
				
			}
			
			return new ResponseManager($this, self::NONEXISTANT_PAGE);
		}
		catch(\Exception $e){
				
			$this['app']['hologram']->setModule("EXCEPTION")->log(\Bakery\Utilities\Hologram::DEBUG, $e->getMessage() );
		
			$this['instance'] = new ResponseManager($this);
			$this['instance']->error( $e->getMessage() );
			
			return $this['instance'];				
				
		}
		
		
	}

	/*
	 * @TODO: FInish implementing verification methods
	 */
	private function _validateRouteRequirenments( Route $routePackage ){
	
		// Requested URI
		$route = $this['uri'];

		if( !in_array($this['method'], $routePackage->getMethod())){
			
			$this->_requires['method'] = implode("/", $routePackage->getMethod());
			return self::UNSUPPORTED_METHOD;
						
		}
		
		// Make sure this is not the login or login handler
		if( in_array($route, array( $this['app']['security.login']['handler'], $this['app']['security.login']['check_path'], $this['app']['security.logout']['handler'])) === false){
			
			if( is_array($this['app']['security.require_admin'])){
					foreach( $this['app']['security.require_admin'] as $_adminProtectedPattern ){
						
					if(preg_match("`$_adminProtectedPattern`i", $route)){
	
						$this->_requires['authentication'] = true;
						$this->_requires['role'] = "ADMIN";
						
						// Not logged in
						if( $this['app']['security.user']->isAuthed() === false){
							
							return self::AUTHENTICATION_REQD;
							
						}
						
						// Permission denied
						if( $this['app']['security.user']->checkUserRoles( $this->_requires['role'] ) === false ){
							
							return self::PERMISSION_DENIED;
							
						}
					
					}
						
				}
				
			}	
			
			if( is_array($this['app']['security.require_user'])){
				foreach( $this['app']['security.require_user'] as $_userProtectedPattern ){
						
					if(preg_match("`$_userProtectedPattern`i", $route)){
						
						$this->_requires['authentication'] = true;
						$this->_requires['role'] = "USER";
						
						// Not logged in
						if( $this['app']['security.user']->isAuthed() === false){
							
							return self::AUTHENTICATION_REQD;
							
						}
						
						// Permission denied
						if( $this['app']['security.user']->checkUserRoles( $this->_requires['role'] ) === false ){
							
							return self::PERMISSION_DENIED;
							
						}				
					}
						
				}
				
			}
			
		}
		
		return false;
		
	}
	
	public function getRequestURI(){
		
		$protocol = "http://";
		
		if($this['isSecure']){
			$protocol = "https://";
		}
		
		return $protocol.$this['hostname'].$this['uri'];
		
	}

	public function requestall( ){
	
		return $_REQUEST;
			
	}

	public function get( $attr ){
	
		return (!is_array($_GET[$attr]) ? htmlspecialchars( $_GET[$attr] ) : $_GET[$attr]);
					
	}
	

	public function request( $attr ){
	
		return (!is_array($_REQUEST[$attr]) ? htmlspecialchars( $_REQUEST[$attr] ) : $_REQUEST[$attr]);
			
	}

	public function post( $attr ){
	
		return htmlspecialchars( $_POST[$attr] );
	
	}	
}