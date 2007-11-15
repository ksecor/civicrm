<?php

/*
 * Copyright (C) 2007 Jacob Singh, Sam Lerner
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * Modified and improved upon by CiviCRM LLC (c) 2007
 */

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
	public function getCampaignData($campaignId,$widgetId) {
        $this->registerRequest($campaignId,$widgetId,__FUNCTION__);
        $data = new stdClass();
        $data->title = "My CiviWidget";
        $data->logo = "images/logo.png";
        $data->button_title = "Contribute!";
        $data->button_url = "http://en.wikipedia.org/wiki/Donate";
        $data->about = "<p><b>About This</b></p><p>Lorem ipsum dolor sit amet, vulca nuncae sibutus. Lorem ipsum dolor sit amet, vulca nuncae sibutus.</p><p><a href='http://en.wikipedia.org/wiki/Widget'>A Link</a></p>";
        $data->num_donors = "53";
        $data->money_raised = "65.83";
        $data->money_target = "195.80";
        $data->campaign_start = "June 5, 2007";
        $data->campaign_end = "August 12, 2008";
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