<?php

/*
 * This file is part of the Bakery framework.
*
* (c) Mike Mackintosh <mike@bakeryframework.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * b_filter is a helper invoker for custom defined functions
 * 
 * @param unknown_type $type
 * @param unknown_type $this
 * @return mixed
 */
function b_filter( $type , $this ){
	
	return (new \ReflectionFunction( "bfilter_$type"))->invoke($this);

}

/**
 * filter_numeric_keys removes non-string array keys
 * 
 * @param array $array
 * @return array:
 */
function bfilter_numeric_keys( array $array ){
	foreach($array as $key=>$var){
		
		if(is_numeric($key)){
			
			unset($array[$key]);
			
		}
		
	}
	
	return $array;	
}