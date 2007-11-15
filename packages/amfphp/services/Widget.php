<?php

/*
 * Copyright (C) 2007 Jacob Singh, Sam Lerner
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * Modified and improved upon by CiviCRM LLC (c) 2007
 */

require_once '../../../civicrm.config.php';
require_once 'CRM/Core/Config.php';

class Widget {

    public $methodTable;

	function __construct( ) {
		$this->methodTable =
            array(
                  "getCampaignData" =>
                  array(
                        "description" => "Gets all campaign related data and returns it as a std class.",
                        "access" => "remote",
                        "arguments" => array( 'campaignId',
                                              'widgetId' ),
                        ),
                  "getEmbedCode" => 
                  array(
                        "description" => "Gets embed code.  Perhaps overkill, but we can track dropoffs in this case. by # of people reqeusting emebed code / number of unique instances.",
                        "access" => "remote",
                        "arguments" => array( 'campaignId',
                                              'widgetId'  ,
                                              'format' ),
                        )
                  );
	}

	/**
	 * Not implemented - registers an action and unique widget ID.  Useful for stats and debugging
	 *
	 * @param int $campaignId
	 * @param string $widgetId
	 * @param string $action
	 * @return string
	 */
	private function registerRequest($campaignId,$widgetId,$action) {
        return "I registered a request to $action on $campaignId from $widgetId";
	}

	/**
	 * Gets all campaign related data and returns it as a std class.
	 *
	 * @param int $campaignId
	 * @param string $widgetId
	 * @return stdClass
	 */
	public function getCampaignData( $campaignId, $widgetId ) {
        $config =& CRM_Core_Config::singleton( );

        CRM_Core_Error::debug_var( $campaignId, $widgetId );
        $campaignId = 1;

        $this->registerRequest( $campaignId, $widgetId, __FUNCTION__ );

        $data = new stdClass();
        $data->title = "CiviWidget";
        $data->logo = $config->resourceBase . "i/widget/logo.png";
        $data->button_title = "Contribute!";
        $data->button_url = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                   "reset=1&id=$campaignId",
                                                   true, null, false );
        $data->about = "<p><b>About This</b></p><p>This widget is all about us. Give us money";

        $query = "
SELECT count( id ) as count,
       sum( total_amount) as amount
FROM   civicrm_contribution
WHERE  is_test = 0
AND    contribution_status_id = 1
AND    contribution_page_id = %1";
        $params = array( 1 => array( $campaignId, 'Integer' ) ) ;
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            $data->num_donors   = $dao->count;
            $data->money_raised = $dao->amount;
        }

        $query = "
SELECT goal_amount, start_date, end_date
FROM   civicrm_contribution_page
WHERE  id = %1";
        $params = array( 1 => array( $campaignId, 'Integer' ) ) ;
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            require_once 'CRM/Utils/Date.php';
            $data->money_target   = $dao->goal_amount; 
            $data->campaign_start = CRM_Utils_Date::customFormat( $dao->start_date );
            $data->campaign_end   = CRM_Utils_Date::customFormat( $dao->end_date   );
        }

        $data->is_active = true;

        // if is_active is false, show this link and hide the contribute button
        //$data->is_active = false;
        $data->homepage_link = "<a href='http://en.wikipedia.org/wiki/Widget'>Go to the Campaign Homepage</a>";

        // movie clip colors, must be in '0xRRGGBB' format
        $data->colors = array();
        $data->colors["title"] = "0x000000";          // top title text
        $data->colors["button"] = "0xCC9900";         // contribute button
        $data->colors["bar"] = "0xCC9900";            // progress bar
        $data->colors["main_text"] = "0x000000";      // all text in info/about sections
        $data->colors["main"] = "0x96E0E0";           // main inner section gradient from bottom
        $data->colors["main_bg"] = "0xFFFFFF";        // main inner section background
        $data->colors["bg"] = "0x66CCCC";             // widget background

        // these two have colors as normal hex format
        // because they're being used in a CSS object
        $data->colors["about_link"] = "#336699";      // links in the about section (if any)
        $data->colors["homepage_link"] = "#336699";   // homepage link, shown instead of 'contribute' button when is_active = false

        // +++ this can be removed at some point
        // $data->debug = print_r(func_get_args(),1);
    
        return $data;
	}

	/**
	 * Gets embed code.  Perhaps overkill, but we can track dropoffs in this case. by # of people reqeusting emebed code / number of unique instances.
	 *
     * @param int $campaignId
	 * @param string $widgetId
	 * @param string $format - either myspace or normal
	 * @return string
	 */
	public function getEmbedCode($campaignId, $widgetId, $format = "normal") {
        $this->registerRequest($campaignId,$widgetId,__FUNCTION__);
        return "<embed>.......................</embed>" . print_r(func_get_args(),1);
	}

}

?>