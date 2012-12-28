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

use Bakery\Provider\ArrayAccessProvider;
use Bakery\Interfaces\RequestResponseInterface;

/**
 * Response Manager handles the output of all requests
 * 
 * @author mackmi4
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
		
		echo $response . "\n";
	
	}
	
	
}