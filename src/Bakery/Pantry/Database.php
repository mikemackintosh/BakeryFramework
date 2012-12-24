<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Pantry;

use \Bakery\Application;
use \Bakery\Config;
use \Bakery\Exceptions\DatabaseFailedToConnectException;
use \PDO;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class Database extends PDO{
	public 
		$results,
		$query,
		$query_count = 0;
		
	private
		$config,
		$databases;

//      private $this->config = array();



	/**
	 * @param unknown_type $config
	 * @throws DatabaseFailedToConnectException
	 * @return \Bakery\Pantry\Database
	 */
	public function __construct( $config )
	{

		$database = (sizeof(array_keys($config)) > 1) ? array_keys($config)[1] : "mysql";

		if( @!array_key_exists( 'host', $config[$database] ) || ($host = $config[$database]['host'])  === NULL ){
		
			$host = $config['global']['host'];
		
		}

		if( $database === NULL ){
		
			$database = $config['global']['database'];
		
		}

		if( @!array_key_exists( 'user', $config[$database] ) || ($user = $config[$database]['user'])  === NULL ){
		
			$user = $config['global']['user'];
		
		}

		if( @!array_key_exists( 'password', $config[$database] ) || ($pass = $config[$database]['password'])  === NULL ){
		
			$pass = $config['global']['password'];
		
		}
		
		if( @!array_key_exists( 'driver', $config[$database] ) || ($driver = $config[$database]['driver'])  === NULL ){
		
			$driver = $config['global']['driver'];
		
		}
		
		try{
			
			parent::__construct("{$driver}:dbname={$database};host={$host};", $user, $pass) /*, array(PDO::ATTR_PERSISTENT => true)*/;	
			
			// Set attributes
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// Set MySQL specific attributes
			if($driver == "mysql"){
				$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); 
				$this->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
			}
			
			if(!strstr($this->getAttribute(PDO::ATTR_SERVER_INFO), 'Uptime')){
				//\Bakery\Ledger::message('Database failed to connect');
				throw new DatabaseFailedToConnectException($config['host'], $config['database']);		
			}
			
		}
		catch(DatabaseFailedToConnectException $e){
			
			print_r($e);
		
		}
		catch (PDOException $e) {
		
			print_r($e);
//			ErrorHandler::display($e);
		
		}
		
		return $this;
		
	}

}