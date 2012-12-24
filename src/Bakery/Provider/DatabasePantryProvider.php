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
use \Bakery\Config;
use \Bakery\Pantry\Database;
use \Bakery\Interfaces\DatabaseProviderInterface;

/**
 * Provider for Database Storage Engine access
 *
 * @author Mike Mackintosh <mike@bakeryframework.com>
 */
class DatabasePantryProvider implements DatabaseProviderInterface{
	
	/* (non-PHPdoc)
	 * @see \Bakery\Interfaces\DatabaseProviderInterface::init()
	 */
	public function init( Application $app, $object, $database = NULL ){
		
		$config = Config::load('database.ini');
		
		foreach($config->all() as $database => $options){
			$this->databases[$database] = new Database( array("global" => $config->getSection("global"), $database => $options));
		}
		
		$app['db'] = $this;
		
		return $app;
		
	}
	
	/**
	 * @param String $db
	 */
	public function database( $db ){
		
		if(!is_null($this->databases[$db])){
			
			return $this->databases[$db];
			
		}
		
	}
	
	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function fetchAll( $query, $values = array() ){
		
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
		
		return $stmt->fetchAll(\PDO::FETCH_BOTH);
		
	}

	/**
	 * @param String $query
	 * @param Arrau $values
	 */
	public function fetchArray( $query, $values = array()  ){
	
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
	
		return $stmt->fetch(\PDO::FETCH_BOTH);
	
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function fetchAssoc( $query, $values = array()  ){
	
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
	
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	
	}
	
	/**
	 * @param String $table
	 * @param Array $fields
	 * @param Array $values
	 */
	public function insert( $table, $fields, $values){

		$query = "INSERT INTO $table SET ". implode(",", $fields);
		
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
				
		return $this->databases['global']->lastInsertId();
		
	}
	
	/**
	 * @param String $table
	 * @param Array $fields
	 * @param Array $values
	 * @param String $constraint
	 */
	public function update( $table, $fields, $values, $constraint){

		$query = "UPDATE $table SET ". implode(",", $fields)." WHERE $constraint";
		
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
		
		return;
		
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function executeQuery( $query, $values = array() ){
	
		$stmt = $this->databases['global']->prepare($query);
		$stmt->execute($values);
	
		return;
	}


	/**
	 *
	 */
	public function lastInsertId(){
	
		return $this->databases['global']->lastInsertId();
	
	}
	

	/**
	 *
	 */
	public function beginTransaction(){
	
		return $this->databases['global']->beginTransaction();
	
	}
	

	/**
	 *
	 */
	public function commit(){
	
		return $this->databases['global']->commit();
	
	}
	
	/**
	 * @param unknown_type $query
	 * @param unknown_type $values
	 * @param unknown_type $keys
	 * @param unknown_type $many_to_one
	 * @param unknown_type $merge_classifier
	 * @param unknown_type $array
	 * @param unknown_type $return_array
	 * @return unknown
	 */
	public function format($query, $values, $keys, $many_to_one = false, $merge_classifier = NULL, $array = NULL, $return_array = array())
	{
		
		$array = $this->fetchAll( $query, $values);
	
		if(is_string($keys)){
			$tmp = $keys;
			$keys = array();
			$keys[] = $tmp;
		}
	
		$key_size = sizeof(array_intersect_key($array[0], array_flip($keys)));
		$rows = sizeof($array);
	
		foreach($array as $result) {
			$object = &$return_array;
	
			$i = 0;
			foreach($keys as $index){
				$i++;
				if(!array_key_exists($result[$index], $object)) {
					$object[$result[$index]] = array();
				}
	
				$object = &$object[$result[$index]];
			}
				
			if(!is_null($merge_classifier)){
				$object = array_merge($result, array($merge_classifier => ($result[$merge_classifier] + $object[$merge_classifier])));
			}
			else{
				if( $i == $key_size && !$many_to_one){
					$object = $result;
				}
				else{
					$object[] = $result;
				}
			}
		}
	
		return $return_array;
	}
}