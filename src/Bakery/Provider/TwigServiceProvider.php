<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * Modified from it's original format by Fabian Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. 
 */

namespace Bakery\Provider;

use \Bakery\Application;
use \Bakery\Provider\TwigCoreExtension;
use \Bakery\Provider\ArrayAccessProvider;
use \Bakery\Interfaces\SilexServiceProviderInterface;

use \Symfony\Bridge\Twig\Extension\RoutingExtension;
use \Symfony\Bridge\Twig\Extension\TranslationExtension;
use \Symfony\Bridge\Twig\Extension\FormExtension;
use \Symfony\Bridge\Twig\Extension\SecurityExtension;
use \Symfony\Bridge\Twig\Form\TwigRendererEngine;
use \Symfony\Bridge\Twig\Form\TwigRenderer;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class TwigServiceProvider extends ArrayAccessProvider implements SilexServiceProviderInterface, \ArrayAccess
{
	
	public function __construct($values){
		
		foreach($values as $key => $value){
			$this[$key] = $value;
		}
	}
	
    public function register(Application $app)
    {
        $app['twig.options'] = array();
        $app['twig.form.templates'] = array('form_div_layout.html.twig');
        $app['twig.path'] = $this['twig.path'];
        $app['twig.templates'] = array();

        $app['twig.options'] = array_replace(
        		array(
        				'charset'          => $app['charset'],
        				'debug'            => $app['debug'],
        				'strict_variables' => $app['debug'],
        		), $app['twig.options']
        );
        
        $app['twig.loader.filesystem'] = new \Twig_Loader_Filesystem($app['twig.path']);
        
       	$app['twig.loader.array'] = new \Twig_Loader_Array($app['twig.templates']);
        
        $app['twig.loader'] = new \Twig_Loader_Chain(array(
        			$app['twig.loader.filesystem'],
        			$app['twig.loader.array'],
        	));

        $twig = new \Twig_Environment($app['twig.loader'], $app['twig.options']);
		
		$twig->addGlobal('app', $app);
        $twig->addExtension(new TwigCoreExtension());
        $twig->addExtension(new \Twig_Extension_Debug());
                    
            if ($app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            if (class_exists('Symfony\Bridge\Twig\Extension\RoutingExtension')) {
                if (isset($app['url_generator'])) {
                    $twig->addExtension(new RoutingExtension($app['url_generator']));
                }

                if (isset($app['translator'])) {
                    $twig->addExtension(new TranslationExtension($app['translator']));
                }

                if (isset($app['security'])) {
                    $twig->addExtension(new SecurityExtension($app['security']));
                }

                if (isset($app['form.factory'])) {
                    $app['twig.form.engine'] = $app->share(function ($app) {
                        return new TwigRendererEngine($app['twig.form.templates']);
                    });

                    $app['twig.form.renderer'] = $app->share(function ($app) {
                        return new TwigRenderer($app['twig.form.engine'], $app['form.csrf_provider']);
                    });

                    $twig->addExtension(new FormExtension($app['twig.form.renderer']));

                    // add loader for Symfony built-in form templates
                    $reflected = new \ReflectionClass('Symfony\Bridge\Twig\Extension\FormExtension');
                    $path = dirname($reflected->getFileName()).'/../Resources/views/Form';
                    $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));
                }
            }

            // BC: to be removed before 1.0
            if (isset($app['twig.configure'])) {
                throw new \RuntimeException('The twig.configure service has been removed. Read the changelog to learn how you can upgrade your code.');
            }

            return $app['twig'] = $twig;
        

    }

    public function boot(Application $app)
    {
        // BC: to be removed before 1.0
        if (isset($app['twig.class_path'])) {
            throw new \RuntimeException('You have provided the twig.class_path parameter. The autoloader has been removed from Silex. It is recommended that you use Composer to manage your dependencies and handle your autoloading. If you are already using Composer, you can remove the parameter. See http://getcomposer.org for more information.');
        }
    }
}