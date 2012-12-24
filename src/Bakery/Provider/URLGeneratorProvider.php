<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. 
 */

namespace Bakery\Provider;

use \Bakery\Application;
use \Bakery\Interfaces\SilexServiceProviderInterface;
use \Bakery\Utilities\URLGenerator;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class URLGeneratorProvider implements SilexServiceProviderInterface {

		public function register( Application $app ){

			return $app['url_generator'] = new URLGenerator( $app );
			
		}
		
		public function boot(Application $app){
			
		}
}

?>