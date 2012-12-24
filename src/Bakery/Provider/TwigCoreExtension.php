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

/**
 * Twig extension.
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 */
class TwigCoreExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'render' => new \Twig_Function_Method($this, 'render', array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    public function render(\Twig_Environment $twig, $uri)
    {
        /*
        $globals = $twig->getGlobals();
        $request = $globals['app']['request'];

        $subRequest = Request::create($uri, 'get', array(), $request->cookies->all(), array(), $request->server->all());
        if ($request->getSession()) {
            $subRequest->setSession($request->getSession());
        }

        $response = $globals['app']->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);

        if (!$response->isSuccessful()) {
            throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $request->getUri(), $response->getStatusCode()));
        }

        return $response->getContent();
        */
    }

    public function getName()
    {
        return 'bakery';
    }
}