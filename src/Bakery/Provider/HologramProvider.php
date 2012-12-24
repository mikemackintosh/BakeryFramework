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
use \Bakery\Utilities\Hologram;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class HologramProvider implements SilexServiceProviderInterface {

		public function register( Application $app ){

			return $app['hologram'] = new Hologram( $app );
			
		}
		
		public function boot(Application $app){
			
		}
}

?>