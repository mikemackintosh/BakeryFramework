<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// set current working directory to below web root
chdir(__DIR__);

// Define some FW vars
define("EXT", ".php");

// Include composer autloader
include_once 'vendor/autoload.php';

$config = Bakery\Config::load('config.ini');

$app = new Bakery\Application( $config );

return $app;