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

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class Hologram {
	
	const DEBUG = 9;
	const ERROR = 5;
	const NORMAL = 2;
	const OFF = 0;
	
	private
		$_module,
		$_verbosity;
	
	public function __construct( Application $app ){
		
		$this->config = $app['config']->getSection('logging');
		
		$r = new \ReflectionClass($this);
		$this->_verbosity = $r->getConstant(strtoupper($this->config['verbosity']));
		
		$this->_module = "CORE";
				
	}
	
	public function setModule( $module ){
		
		$this->_module = $module;
		
		return $this;
		
	}
	
	public function log( $verbosity, $msg ){
				
		if( $verbosity <= $this->_verbosity ){
				
			file_put_contents("dev.log", "[ ".date("m/d/y H:i:s")." ] -- M: [ $this->_module ] -- Message: ".ucwords($msg)."\n", FILE_APPEND);
				
		}
					
		return $this;
		
	}
}