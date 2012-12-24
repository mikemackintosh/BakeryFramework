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

/**
 * RequestResponseInterface defines common response codes
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *        
 */
interface RequestResponseInterface {
	
	const NONEXISTANT_PAGE = 2;
	const AUTHENTICATION_REQD = 4;
	const PERMISSION_DENIED = 8;
	const UNSUPPORTED_METHOD = 16;
	
	const ERROR2 = "pageDoesNotExist";
	const ERROR4 = "authenticationReqd";
	const ERROR8 = "permissionDenied";
	const ERROR16 = "unsupportedMethod";
	
}

?>