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
 * Interface for controller providers.
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 */
interface ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app);
}