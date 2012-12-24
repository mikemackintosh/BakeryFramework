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

class Exception extends \Exception{

	public function __construct( $message )	{

		parent::__construct("[Bakery Core] :: Module [Exception Caught]  :: ".$message.PHP_EOL);

	}

}

class UnableToAutoLoadFile extends Exception{

	public function __construct( $class )	{

		parent::__construct("Autoloader unable to load class: ".$class);

	}

}
