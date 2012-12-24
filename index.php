<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = include_once 'boot.php';

/**
 * Register Twig
 * 
 * @Sets $app['twig'];
 *
 */
$app->register(new Bakery\Provider\TwigServiceProvider( array(
		'twig.path' => __DIR__.'/views',
)));

/**
 * Initilizes the DB Pantry
 * 
 * @Sets $app['db'];
 *
 */
$app->init('db', function() use ($app){
	return new Bakery\Provider\DatabasePantryProvider( $app );
});


/**
 * Set Default Root
 * 
 */
$app->route("/", function() use ($app){
	return $app['twig']->render("homepage.twig", array(
			
	));
})->method("GET|POST")
  ->bind("homepage");


/**
 * Run the application
 */
$app->run();

/**
 * END FILE;
 */