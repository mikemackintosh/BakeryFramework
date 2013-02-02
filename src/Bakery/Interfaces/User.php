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
use \Bakery\Provider\ArrayAccessProvider;
use \Bakery\Provider\LdapPantryProvider;
use \Bakery\Manager\UserEntity;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
interface User {
	
	/**
	 * @param Application $app
	 */
	public function __construct( Application $app );
	
	/**
	 * @return \Bakery\Provider\LdapPantryProvider|boolean
	 */
	public function authenticate( );
	
	
	/**
	 * @return \Bakery\Manager\User
	 */
	public function isAuthed();
	
	/**
	 * 
	 */
	public function checkUserRoles( $role = "USER" );


}