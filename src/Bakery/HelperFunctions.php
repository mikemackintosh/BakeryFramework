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
	$filter = new \ReflectionFunction( "bfilter_$type");
	return $filter->invoke($this);

}

/**
 * filter_numeric_keys removes non-string array keys
 * 
 * @param array $array
 * @return array
 */
function bfilter_numeric_keys( array $array ){
	foreach($array as $key=>$var){
		
		if(is_numeric($key)){
			
			unset($array[$key]);
			
		}
		
	}
	
	return $array;	
}

/**
 * bfilter_create_slug - converts string into slug
 * 
 * @param string $slug
 * @param boolean $hyphenate
 * @return string
 */
function bfilter_create_slug($slug, $hyphenate = true){
	$slug = str_replace(array("'", '"'), "", strtolower($slug));

	if($hyphenate){
		$slug = preg_replace("/[-\s\W]/","-",$slug);
	}

	return preg_replace("/[^a-z0-9-_]/", "", $slug);
}
