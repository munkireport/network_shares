<?php

use CFPropertyList\CFPropertyList;

class Network_shares_model extends \Model {

	function __construct($serial='')
	{
		parent::__construct('id', 'network_shares'); //primary key, tablename
		$this->rs['id'] = '';
		$this->rs['serial_number'] = $serial;
		$this->rs['name'] = '';
		$this->rs['mntfromname'] = '';
		$this->rs['fstypename'] = '';
		$this->rs['fsmtnonname'] = '';
		$this->rs['automounted'] = 0; // True or False

		$this->serial_number = $serial;
	}

    // -----------------------------------------------------------------------------------------------------------

	/**
	 * Process data sent by postflight
	 *
	 * @param string data
	 * @author tuxudo
	 **/
	function process($plist)
	{
        // Check if we have data
		if ( ! $plist){
			throw new Exception("Error Processing Request: No property list found", 1);
		}

		// Delete previous set
		$this->deleteWhere('serial_number=?', $this->serial_number);

		$parser = new CFPropertyList();
		$parser->parse($plist, CFPropertyList::FORMAT_XML);
		$myList = $parser->toArray();

		foreach ($myList as $device) {
			// Check if we have a name
			if( ! array_key_exists("name", $device)){
				continue;
			}

			// Network shares to exclude
			$excludeshares = array("/net","/home","/Volumes/MobileBackups","/Volumes/MobileBackups 1","/Volumes/MobileBackups 2","/Network/Servers","/System/Volumes/Data/home","/System/Volumes/Data/Network/Servers");
			if (in_array($device['fsmtnonname'], $excludeshares)) {
				continue;
			}

			foreach ($this->rs as $key => $value) {
				$this->rs[$key] = $value;
				if(array_key_exists($key, $device))
				{
					$this->rs[$key] = $device[$key];
				}
			}

			// Save network share
			$this->id = '';
			$this->save();
		}
	}
}
