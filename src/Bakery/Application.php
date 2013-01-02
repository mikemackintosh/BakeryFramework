<?php

/*
 * This file is part of the Bakery framework.
*
* (c) Mike Mackintosh <mike@bakeryframework.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Bakery;

use Bakery\Provider\ArrayAccessProvider;
use \Closure as Closure;
use \Bakery\Route;
use \Bakery\Manager\HTTP\RequestManager as HTTPRequestManager;
use \Bakery\Manager\CLI\RequestManager as CLIRequestManager;
use \Bakery\Interfaces\ControllerProviderInterface;
use \Bakery\Interfaces\SilexServiceProviderInterface;

include_once(__DIR__ . "/HelperFunctions".EXT);

session_start();

/**
 * Core Application
 * 
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class Application extends ArrayAccessProvider implements \ArrayAccess{

	public
		$routes = array();
	
	/**
	 * @param unknown_type $config
	 * @return \Bakery\Application
	 */
	public function __construct( $config ){
		
		$this['config'] = $config;
		
		$this['error'] = $this['request_error'];
		
		$this['debug'] = false;
		$this['charset'] = 'UTF-8';
		$this['locale'] = 'en';
		$this['prefix'] = null;
		$this['shared'] = new \stdClass;
		
		$this->register(new \Bakery\Provider\HologramProvider());

		$this['hologram']->setModule("CORE")->log(\Bakery\Utilities\Hologram::DEBUG, "Formulating Recipe");
		
		return $this;
		
	}

	/**
	 * @param unknown_type $object
	 * @param unknown_type $instance
	 */
	public function init(  $object, $instance ){

		$this['shared']->$object = $instance();
		
		if(method_exists( $this['shared']->$object, "init" )){
			$this['shared']->$object->init( $this, $object );
		}	
		
	}

	/**
	 * @param unknown_type $prefix
	 * @param unknown_type $app
	 * @throws \LogicException
	 * @return \Bakery\Application
	 */
	public function mount( $prefix, $app ){
		//	
		$this['prefix'] = $prefix;
		
		if ($app instanceof ControllerProviderInterface) {
            $app = $app->connect( $this , $prefix );
        }
        else{
			throw new \LogicException('The "mount" method requires an instance of the ControllerProviderInterface.');
        }
        
		return $this;
        	
	}
	
	/**
	 * @param unknown_type $prefix
	 * @param unknown_type $callback
	 * @return \Bakery\Pantry\Route
	 */
	public function route( $prefix, $callback){

		//	
		if(!is_null($this['prefix'])){	
			$_bt = debug_backtrace();
			
			if(array_key_exists("2", $_bt) && $_bt[2]['function'] != "mount"){
				$this['prefix'] = null;
			}
		}
		
		return $this->routes[$this['prefix'].$prefix] = new Route( $this['prefix'].$prefix, $callback);
		
	}
	
	public function security( $securityOptions ){
		
		$this['security.options'] = $securityOptions;
		$this['security.login'] = $securityOptions['login'];
		$this['security.logout'] = $securityOptions['logout'];
		
		
		$this['security.anonymous'] = (array_key_exists("anonymous", $securityOptions) ? $securityOptions['anonymous'] : null);
		$this['security.require_admin'] = (array_key_exists("admin_protected", $securityOptions) ? $securityOptions['admin_protected'] : null);
		$this['security.require_user'] = (array_key_exists("user_protected", $securityOptions) ? $securityOptions['user_protected'] : null);
		$this['security.require_custom'] = (array_key_exists("custom_protected", $securityOptions) ? $securityOptions['custom_protected'] : null);
		
		$this['security.user'] = new $securityOptions['user']( $this );
		
		$this['user'] = $this['security.user']['security.user.data'];
		
		$this['security.isauthed'] = $this['security.user']->isAuthed( );

		// Set app to pass to login/logout/etc
		$app = &$this;
		
		// Logout
		$app->route("{$this['security.logout']['handler']}", function( HTTPRequestManager $request ) use ($app){
			
			session_destroy();
			
			header("Location: /");
			
		});
		
		// Login
		$app->route("{$this['security.login']['handler']}", function( HTTPRequestManager $request ) use ($app){
			return $app['twig']->render('login.twig', array(
      			'login_validator' => $app['security.login']['check_path'],
				'redirect_to' => (isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : "/"),
  			));
			
		});		
		
		// Login and validate session
		$app->route("{$this['security.login']['check_path']}", function( HTTPRequestManager $request ) use ($app){
			if(($app['security.user']->authenticate())){
				header("Location: {$_POST['redirect_to']}");
			}
			else{
				
				return $app['twig']->render('login.twig', array(
						'login_validator' => $app['security.login']['check_path'],
						'error' => $app['security.user.error'],
						'redirect_to' => $_POST['redirect_to'],
				));
				
			}
		});
	}

	/**
	 * 
	 */
	public function run(){
		
		$this['hologram']->setModule("CORE")->log(\Bakery\Utilities\Hologram::DEBUG, "Baking Request");
		
		$this['request'] = new HTTPRequestManager( $this );
		
		$this['response'] = $this['request']->response( );
			
		return $this['response'];
		
	}


	/**
	 *
	 */
	public function cli(){
	
		// map request to routes
		//$this->Log("Accessed ". __METHOD__);
	
		$this['hologram']->setModule("CORE")->log(\Bakery\Utilities\Hologram::DEBUG, "Baking Request");
	
		$this['request'] = new CLIRequestManager( $this );
	
		$this['response'] = $this['request']->response( );
			
		return $this['response'];
	
	}
	
	public function redirect( $uri, $route ){
		
		return $this->routes[$uri] = $this->routes[$this['prefix'].$route];

	}

	/**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     */
    public function register(SilexServiceProviderInterface $provider, array $values = array())
    {
        $this->providers[] = $provider;

        $provider->register( $this );

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
	 * Returns a closure that stores the result of the given closure for
	 * uniqueness in the scope of this instance of Application.
	 *
	 * @param Closure $callable A closure to wrap for uniqueness
	 *
	 * @return Closure The wrapped closure
	 */
	public function share(Closure $callable )
	{
			
		return $callable( null );
		
	}
	
}
