<?php

/*
 * This file is part of the Bakery framework.
 *
 * (c) Mike Mackintosh <mike@bakeryframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakery\Utilities;

/**
 * @author Mike Mackintosh <mike@bakeryframework.com>
 *
 */
class SystemIO{
	
	/**
	 * @return \Bakery\Library\SystemIO
	 */
	public function __construct( ){
		
		return $this;
	}
	

	/**
	 * @param unknown_type $what
	 * @return NULL
	 */
	static public function findExec( $what ){

		$response = self::exec( "which $what" )[0];
		
		return $response;
		
	}
	
	/**
	 * @param unknown_type $command
	 * @return NULL
	 */
	static public function exec( $command ){

		$response = NULL;

		exec( $command , $response);
		
		return $response;
		
	}
	
	/**
	 * @param unknown_type $result
	 * @param unknown_type $processes
	 * @return multitype:unknown mixed Ambigous <> 
	 */
	static public function parsePS( $result, $processes = array() ) {
	
		if(!is_array( $result) ){
			$tmp = $result;
			$result = array();
			$result[0] = $tmp;
		}
		
		foreach($result as $ps ){
			
			$ps = preg_split("/[\s]+/", $ps);

			$process = array();
			$process['user'] = $ps[0];
			$process['pid'] = $ps[1];
			$process['cpu'] = $ps[2];
			$process['memory'] = $ps[3];
			$process['start_date'] = $ps[8];
			$process['cpu_time'] = $ps[9];
			$process['command'] = $ps[10];
			$process['flags'] = preg_replace("~^{$ps[10]}[\s+]~", "", implode(" ", array_splice($ps, 10) ));
			$processes[] = $process;		
		}
		
		return $processes;
		
	}
	
	/**
	 * @param unknown_type $result
	 * @param unknown_type $interfaces
	 * @return multitype:
	 */
	static public function parseInterface( $result, $interfaces = array() ) {
	
		$ints = explode("\n\n", implode("\n", $result));
				
		foreach($ints as $int){
			
			preg_match("/^([A-z]*\d)\s+Link\s+encap:([A-z]*)\s+HWaddr\s+([A-z0-9:]*).*".
						"inet addr:([0-9.]+).*Bcast:([0-9.]+).*Mask:([0-9.]+).*".
						"MTU:([0-9.]+).*Metric:([0-9.]+).*".
						"RX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*frame:([0-9.]+).*".
						"TX packets:([0-9.]+).*errors:([0-9.]+).*dropped:([0-9.]+).*overruns:([0-9.]+).*carrier:([0-9.]+).*".
						"RX bytes:([0-9.]+).*\((.*)\).*TX bytes:([0-9.]+).*\((.*)\)".
						"/ims", $int, $regex);
						
			if( !empty($regex) ){
				
				$interface = array();
				$interface['name'] = $regex[1];
				$interface['type'] = $regex[2];
				$interface['mac'] = $regex[3];
				$interface['ip'] = $regex[4];
				$interface['broadcast'] = $regex[5];
				$interface['netmask'] = $regex[6];
				$interface['mtu'] = $regex[7];
				$interface['metric'] = $regex[8];
				
				$interface['rx']['packets'] = $regex[9];
				$interface['rx']['errors'] = $regex[10];
				$interface['rx']['dropped'] = $regex[11];
				$interface['rx']['overruns'] = $regex[12];
				$interface['rx']['frame'] = $regex[13];
				$interface['rx']['bytes'] = $regex[19];
				$interface['rx']['hbytes'] = $regex[20];
					
				$interface['tx']['packets'] = $regex[14];
				$interface['tx']['errors'] = $regex[15];
				$interface['tx']['dropped'] = $regex[16];
				$interface['tx']['overruns'] = $regex[17];
				$interface['tx']['carrier'] = $regex[18];
				$interface['tx']['bytes'] = $regex[21];
				$interface['tx']['hbytes'] = $regex[22];
					
				$interfaces[] = $interface;
				
			}
		
		}

		return $interfaces;
	
	}

}
