<?php

/*
 * Copyright (C) 2007 Jacob Singh, Sam Lerner
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * Modified and improved upon by CiviCRM LLC (c) 2007
 */

class CRM_Widget_Widget {

    static $_methodTable;

	function initialize( ) {
        if ( ! self::$_methodTable ) {
            self::$_methodTable =
                array(
                  'getContributionPageData' =>
                  array(
                        'description' => 'Gets all campaign related data and returns it as a std class.',
                        'access'      => 'remote',
                        'arguments'   => array( 'contributionPageID',
                                                'widgetID' ),
                        ),
                  'getEmbedCode' => 
                  array(
                        'description' => 'Gets embed code.  Perhaps overkill, but we can track dropoffs in this case. by # of people reqeusting emebed code / number of unique instances.',
                        'access'      => 'remote',
                        'arguments'   => array( 'contributionPageID',
                                                'widgetID'  ,
                                                'format' ),
                        )
                  );
        }
	}

    function &methodTable( ) {
        self::initialize( );

        return self::$_methodTable;
    }

	/**
	 * Not implemented - registers an action and unique widget ID.  Useful for stats and debugging
	 *
	 * @param int $contributionPageID
	 * @param string $widgetID
	 * @param string $action
	 * @return string
	 */
	function registerRequest($contributionPageID,$widgetID,$action) {
        return "I registered a request to $action on $contributionPageID from $widgetID";
	}

	/**
	 * Gets all campaign related data and returns it as a std class.
	 *
	 * @param int $contributionPageID
	 * @param string $widgetID
	 * @return stdClass
	 */
	public function getContributionPageData( $contributionPageID, $widgetID ) {
        $config =& CRM_Core_Config::singleton( );

        self::registerRequest( $contributionPageID, $widgetID, __FUNCTION__ );

        $data = new stdClass();

        require_once 'CRM/Contribute/DAO/Widget.php';
        $widget = new CRM_Contribute_DAO_Widget( );
        $data->is_active = true;
        if ( ! $widget->find( true ) ||
             ! $widget->is_active ) {
            $data->is_active = false;
        }

        $data->title = $widget->title;
        $data->logo = $widget->url_logo;
        $data->button_title = $widget->button_title;
        $data->button_url = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                   "reset=1&id=$contributionPageID",
                                                   true, null, false );
        $data->about = $widget->about;

        $query = "
SELECT count( id ) as count,
       sum( total_amount) as amount
FROM   civicrm_contribution
WHERE  is_test = 0
AND    contribution_status_id = 1
AND    contribution_page_id = %1";
        $params = array( 1 => array( $contributionPageID, 'Integer' ) ) ;
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            $data->num_donors   = $dao->count;
            $data->money_raised = $dao->amount;
        }

        $query = "
SELECT goal_amount, start_date, end_date, is_active
FROM   civicrm_contribution_page
WHERE  id = %1";
        $params = array( 1 => array( $contributionPageID, 'Integer' ) ) ;
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        if ( $dao->fetch( ) ) {
            require_once 'CRM/Utils/Date.php';
            $data->money_target   = $dao->goal_amount; 
            $data->campaign_start = CRM_Utils_Date::customFormat( $dao->start_date, $config->dateformatFull );
            $data->campaign_end   = CRM_Utils_Date::customFormat( $dao->end_date  , $config->dateformatFull );

            // check for time being between start and end date
            $now = time( );
            if ( $dao->start_date ) {
                $startDate = CRM_Utils_Date::unixTime( $dao->start_date );
                if ( $startDate &&
                     $startDate >= $now ) {
                    $data->is_active = false;
                }
            }
            
            if ( $dao->end_date ) {
                $endDate = CRM_Utils_Date::unixTime( $dao->end_date );
                if ( $endDate &&
                     $endDate < $now ) {
                    $data->is_active = false;
                }
            }
        } else {
            $data->is_active = false;
        }

        // if is_active is false, show this link and hide the contribute button
        $data->homepage_link = $widget->url_homepage;

        // movie clip colors, must be in '0xRRGGBB' format
        $data->colors = array();

        $data->colors["title"]     = $widget->color_title;
        $data->colors["button"]    = $widget->color_button;
        $data->colors["bar"]       = $widget->color_bar;
        $data->colors["main_text"] = $widget->color_main_text;
        $data->colors["main"]      = $widget->color_main;
        $data->colors["main_bg"]   = $widget->color_main_bg;
        $data->colors["bg"]        = $widget->color_bg;

        // these two have colors as normal hex format
        // because they're being used in a CSS object
        $data->colors["about_link"]    = $widget->color_about_link;
        $data->colors["homepage_link"] = $widget->color_homepage_link;

        // +++ this can be removed at some point
        // $data->debug = print_r(func_get_args(),1);
    
        require_once 'CRM/Core/Error.php';
        return $data;
	}

	/**
	 * Gets embed code.  Perhaps overkill, but we can track dropoffs in this case.
     * by # of people reqeusting emebed code / number of unique instances.
	 *
     * @param int $contributionPageID
	 * @param string $widgetID
	 * @param string $format - either myspace or normal
	 * @return string
	 */
	public function getEmbedCode($contributionPageID, $widgetID, $format = "normal") {
        self::registerRequest($contributionPageID,$widgetID,__FUNCTION__);
        return "<embed>.......................</embed>" . print_r(func_get_args(),1);
	}

}

?>