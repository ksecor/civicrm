<?php

/*
 * Copyright (C) 2007 Jacob Singh, Sam Lerner
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * Modified and improved upon by CiviCRM LLC (c) 2007
 */

require_once '../../../civicrm.config.php';
require_once 'CRM/Widget/Widget.php';
require_once 'CRM/Core/Error.php';

class Widget {

    public $methodTable;

	function __construct( ) {
		$this->methodTable =& CRM_Widget_Widget::methodTable( );
        CRM_Core_Error::debug_var( 'mt', $this->methodTable );

    }

	/**
	 * Not implemented - registers an action and unique widget ID.  Useful for stats and debugging
	 *
	 * @param int $campaignId
	 * @param string $widgetId
	 * @param string $action
	 * @return string
	 */
	private function registerRequest( $campaignId, $widgetId, $action) {
        return CRM_Widget_Widget::registerRequest( $campaignId,
                                                   $widgetId,
                                                   $action );
	}

	/**
	 * Gets all campaign related data and returns it as a std class.
	 *
	 * @param int $campaignId
	 * @param string $widgetId
	 * @return stdClass
	 */
	public function getCampaignData( $campaignId, $widgetId ) {
        $data = CRM_Widget_Widget::getCampaignData( $campaignId, $widgetId );
        CRM_Core_Error::debug_var( $campaignId, $data );
        return $data;
    }

	/**
	 * Gets embed code.  Perhaps overkill, but we can track dropoffs in this case.
     * by # of people reqeusting emebed code / number of unique instances.
	 *
     * @param int $campaignId
	 * @param string $widgetId
	 * @param string $format - either myspace or normal
	 * @return string
	 */
	public function getEmbedCode( $campaignId,
                                  $widgetId,
                                  $format = "normal" ) {
        return CRM_Widget_Widget::getEmbedCode( $campaignId, $widgetId, $format );
	}

}

?>