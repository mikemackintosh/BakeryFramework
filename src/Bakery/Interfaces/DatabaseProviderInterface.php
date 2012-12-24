<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Interfaces;

use \Bakery\Application;

/**
 * DatabaseProviderInterface
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
interface DatabaseProviderInterface{
	
	public function init( Application $app, $object, $database = NULL );
	
}