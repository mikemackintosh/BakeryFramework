<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Exceptions;

class SystemIOExceptions extends \Bakery\Exceptions\Exception{
	
	public function __construct($message){
		
		$this->message = "$message\n";
		
		parent::__construct($message);
		
	}
}
