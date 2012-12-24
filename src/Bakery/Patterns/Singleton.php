<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. 
 */

namespace Bakery\Patterns;

/**
 * 
 * @author Mike Mackintosh <mike@bakeryframework.com>
 * 
 */
class Singleton{
		
	/**
	 * @var static Object
	 */
	static private
		$instance;
	
	/**
	 * @return static::$instance
	 */
	static public function instance(){
		if( is_null( self::$instance ) ){
			/**
			 *
			 */
			$class = \get_called_class();
			self::$instance = new $class ;
				
		}
		
		return self::$instance;	
	}
}
