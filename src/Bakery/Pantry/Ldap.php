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

/**
 * LDAP Pantry class provides a helper class 
 * into the control of the LDAP protocol.
 * 
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class Ldap{
	
	// Used for getErrors
	const ERRNO = 1;
	const ERRSTR = 2;
	
	public
		$_availableHosts = array(),
		$_host = "",
		$results = array();
	
	private 	
		$_resource,
		$_defaultFilter = "(&(samaccountname={user}))";
		

	public function __construct( Application $app ){
				
		$this->config = Config::load('ldap.ini')->all();

		$this->_availableHosts = array_unique(array_keys($this->config));
		
		if( $this->connect() === false ){

			return false;
			
		}
				
	}
	
	public function connect(){

		/*try
		{*/
			$this->_resource = ldap_connect( $this->config[$this->_host]['hostname'], $this->config[$this->_host]['port'] );
			ldap_set_option($this->_resource, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($this->_resource, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($this->_resource, LDAP_OPT_DEBUG_LEVEL, 7);
		
			if( $this->_resource === false){
								
				return false;
			}
			
		/*}
		catch(LDAPFailedToConnectException $e)
		{
			$this->errno = ldap_errno($ldap);
			$this->errstr = ldap_error($ldap);
			$this->error = $e->getMessage();
		}
		catch(LDAPFailedToBind $e)
		{
			$this->errno = ldap_errno($ldap);
			$this->errstr = ldap_error($ldap);
			$this->error = $e->getMessage();
		}*/		
			
		return true;
		
	}
	
	public function authenticate($username, $password){
		
		if( @ldap_bind($this->_resource, "$username{$this->config[$this->_host]['tld']}", $password) === false ){
			
			return false;
		}
				
		$_searchString = ldap_search( $this->_resource, $this->config[$this->_host]['distinguished_name'], str_replace("{user}", "$username", $this->_defaultFilter) );
		
		$entries = ldap_get_entries( $this->_resource, $_searchString );
		
		if($entries['count'] == 1){
							
			foreach($entries[0] as $object => $value){
			
				if( is_array( $value ) === true ){
					
					$this->results[$object] = (sizeof($value) > 2) ? $value : $value[0];
				
				}
					
			}
			
		}
		
		return true;
		
	}
	
	public function setHost( $host ){
		if( in_array( $host, $this->_availableHosts) === false ){
			die("The requested host is not available");	
		}
		
		$this->_host = $host;	
		
	}

	/**
	 * @return string
	 */
	public function getError( $verbosity = NULL ){
		
		if( is_null( $verbosity )){
			$this->_error = ldap_error( $this->_resource ) . " (". ldap_errno( $this->_resource ) .")";
		}
		else if( $verbosity == self::ERRNO){
			$this->_error = ldap_errno( $this->_resource );	
		}
		else if( $verbosity == self::ERRSTR ){
			$this->_error = ldap_error( $this->_resource );
		}
		
		return $this->_error;
		
	}
	
	
	/**
	 * @param unknown_type $object
	 * @return multitype:
	 */
	public function get( $object ){
		
		return $this->results[ $object ];
		
	}
	
	/**
	 * @return multitype:
	 */
	public function all(){
		
		return $this->results;
		
	}
}