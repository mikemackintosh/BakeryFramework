<?php

namespace Bakery\Manager\CLI;

use \Bakery\Utilities\CLIColors as Colors;
use \Bakery\Provider\ArrayAccessProvider;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class OutputManager extends ArrayAccessProvider implements \ArrayAccess {
	
	/**
	 * 
	 */
	function __construct() {
		
		$this['colors'] = new Colors();
		
	}
	
	/**
	 * @param unknown_type $message
	 */
	public function error( $message ){
			
		$response = array();
		
		$response[] = $this['colors']->getColoredString("\t!!! ", "red", null);
		$response[] = $this['colors']->getColoredString("$message\n", "bold");
		
		echo implode("", $response);
	}
	
	/**
	 * @param unknown_type $message
	 */
	public function output( $message ){
		
		if(is_array($message)){
			
			$message = print_r($message, true);
			$message = str_replace(array("[", "]", "=>"), array($this['colors']->getColoredString("[", "green"), $this['colors']->getColoredString("]", "green"), $this['colors']->getColoredString("=>", "white")), $message);
			
		}
			
		$response = array();
			
		$response[] = $this['colors']->getColoredString("$message\n", "bold");
			
		echo implode("", $response);	
		
	}

}

?>