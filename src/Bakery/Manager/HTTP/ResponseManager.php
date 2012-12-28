<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Manager\HTTP;

use \Bakery\Provider\ArrayAccessProvider;
use \Bakery\Interfaces\RequestResponseInterface;

/**
 * Response Manager handles the output of all HTTP requests
 * 
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class ResponseManager extends ArrayAccessProvider implements \ArrayAccess, RequestResponseInterface{
	
	/**
	 * @param RequestManager $request
	 * @param Array $response
	 */
	public function __construct( RequestManager &$request, $response = NULL ){
				
		$this['app'] = $request['app'];
		$this['request'] = $request;
		$this['response'] = $response;
		
		if(is_numeric($response)){
			
			$fn = new \ReflectionObject( $this );
			$response = $this->{$fn->getConstant("ERROR$response")}();
										
		}
		
		if($this->isJson()){
			
			if( is_array( $response )){

				$this->json( $response );
				
			}
			
		}
		
		echo $response;
	
	}
	
	/**
	 * Returns true/false if the request is JSON
	 * 
	 */
	public function isJson(){
		
		return $this['request']['json'];
		
	}

	/**
	 * Error Response
	 *
	 */	
	public function error( $error ){
		header('HTTP/1.0 417 Exception Failed');
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::NORMAL, "Response: Exception Occured");
		
		$requested_page = $this['request']['uri'];

		echo $this['app']['twig']->render('errors/417.twig', array(
			"requested_page" => $requested_page,
			"error" => $error,
		));	
	}
	
	/**
	 * PageDoesNotExist Response
	 * 
	 */	
	public function pageDoesNotExist(){
		header('HTTP/1.0 404 Not Found');
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::NORMAL, "Response: Page does not exist");
		
		$requested_page = $this['request']['uri'];

		if($this->isJson()){

			return array("response" => "invalid request");
			
		}
		
		return $this['app']['twig']->render('errors/404.twig', array(
			"requested_page" => $requested_page,
		));
	}
	
	/**
	 * AuthenticationRequired Response
	 * 
	 * If authentication is required, redirect to the security.login
	 * handler defined earlier in execution.
	 * 
	 */
	public function authenticationReqd(){
		header('HTTP/1.0 401 Unauthorized');
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::NORMAL, "Response: Page requires authentication");
		
		$_SESSION['redirect_to'] = $this['request']['uri'];
		
		if($this->isJson()){

			return array("response" => "authentication required");
			
		}
		
		header("Location: {$this['app']['security.login']['handler']}");
		
	}
	
	/**
	 * @return multitype:string 
	 */
	public function permissionDenied(){
		header('HTTP/1.0 403 Forbidden');
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::NORMAL, "Response: User does not have access to this page");
		
		$requested_page = $this['request']['uri'];
		
		if($this->isJson()){
		
			return array("response" => "permission denied");
				
		}
		
		return $this['app']['twig']->render('errors/403.twig', array(
				"requested_page" => $requested_page,
		));
	}
	
	/**
	 * @return multitype:string 
	 */
	public function unsupportedMethod(){
		header('HTTP/1.0 405 Method Not Allowed');
		$this['app']['hologram']->setModule("WWW")->log(\Bakery\Utilities\Hologram::NORMAL, "Response: Unsupported request method");

		if($this->isJson()){
		
			return array("response" => "unsupported method");
		
		}
		
		return $this['app']['twig']->render('errors/404.twig', array(
				"requested_page" => $requested_page,
		));
	}

}