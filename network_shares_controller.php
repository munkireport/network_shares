<?php 

/**
 * network shares module class
 *
 * @package munkireport
 * @author tuxudo
 **/
class Network_shares_controller extends Module_controller
{
	
	/*** Protect methods with auth! ****/
	function __construct()
	{
		// Store module path
		$this->module_path = dirname(__FILE__);
	}

	/**
	 * Default method
	 * @author tuxudo
	 *
	 **/
	function index()
	{
		echo "You've loaded the network_shares module!";
	}

	/**
     * Get network shares for widget
     *
     * @return void
     * @author tuxudo
     **/
     public function get_network_shares()
     {
        $sql = "SELECT COUNT(CASE WHEN name <> '' AND name IS NOT NULL THEN 1 END) AS count, name
                    FROM network_shares
                    LEFT JOIN reportdata USING (serial_number)
                    ".get_machine_group_filter()."
                    GROUP BY name
                    ORDER BY count DESC";

        $out = array();
        $network_shares = new Network_shares_model;

        foreach ($network_shares->query($sql) as $obj) {
            if ("$obj->count" !== "0") {
                $obj->name = $obj->name ? $obj->name : 'Unknown';
                $out[] = $obj;
            }
        }

        jsonView($out);
     }

	/**
     * Retrieve data in json format
     *
     **/
     public function get_data($serial_number = '')
     {
        $queryobj = new Network_shares_model;
        $network_shares_tab = array();
        foreach($queryobj->retrieve_records($serial_number) as $shareEntry) {
            $network_shares_tab[] = $shareEntry->rs;
        }

        jsonView($network_shares_tab);
     }

} // END class Network_shares_controller
