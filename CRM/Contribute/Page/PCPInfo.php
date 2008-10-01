<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * PCP Info Page - Summmary about the PCP
 */
class CRM_Contribute_Page_PCPInfo extends CRM_Core_Page
{

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        $config =& CRM_Core_Config::singleton();
        $currencySymbol = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Currency', $config->defaultCurrency, 'symbol', 'name');
        $this->assign('currencySymbol', $currencySymbol);
        $this->assign('config', $config);
        //get the pcp id.
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this, true );

        $action  = CRM_Utils_Request::retrieve( 'action', 'String'  , $this, false );

        $prms = array( 'id' => $this->_id );
        
        CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_PCP', $prms, $pcpInfo );
        $this->assign('pcp', $pcpInfo );
        
        if ( ! $pcpInfo['is_active'] ) {
            // form is inactive, die a fatal death
            CRM_Core_Error::fatal( ts( 'The page you requested is currently unavailable.' ) );
        }      
        $default = array();
        
        CRM_Core_DAO::commonRetrieveAll( 'CRM_Contribute_DAO_ContributionPage', 'id', 
                                         $pcpInfo['contribution_page_id'], $default, array( 'start_date', 'end_date' ) );


        $query="
SELECT CONCAT_WS(' $currencySymbol ', pcp_roll_nickname,  total_amount ) as honor
FROM civicrm_contribution
WHERE pcp_made_through_id = $this->_id AND pcp_display_in_roll = 1
";
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $honor = array();
        
        while( $dao->fetch() ) {
            $honor[] = $dao->honor;
        }
        
        if( $file_id = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_EntityFile', $this->_id , 'file_id', 'entity_id') ) {
            $image = '<img align="middle" src="'.CRM_Utils_System::url( 'civicrm/file', 
                                                         "reset=1&id=$file_id&eid=$this->_id" ).'"width=300 height=300/>';
        }

        require_once 'CRM/Contribute/BAO/PCP.php';
        $totalAmount = CRM_Contribute_BAO_PCP::thermoMeter( $this->_id );
        $achieved = $totalAmount/$pcpInfo['goal_amount'] *100;
        
        $this->assign('image', $image);
        $this->assign('honor', $honor );
        $this->assign('pcpDate', $default['1'] );
        $this->assign('total', $totalAmount);
        $this->assign('achieved', $achieved);
        
        if ( $achieved <= 100 ) {
            $this->assign('remaining', 100- $achieved );
        }
        // make sure that we are between  registration start date and registration end date
        $startDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'start_date',
                                                                       $default['1'] ) );
        $endDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value( 'end_date',
                                                                     $default['1'] ) );
        $now = time( );
        $validDate = true;
        if ( $startDate && $startDate >= $now ) {
            $validDate = false;
            
        }
        if ( $endDate && $endDate < $now ) {
            $validDate = false;
        }
        
        $this->assign( 'validDate', true  );
        if ( $validDate ) {
            
            $contributionText = ts('Contribute Now');
            if ( CRM_Utils_Array::value('donate_link_text',$pcpInfo ) ) {
                $contributionText = $pcpInfo['donate_link_text'];
            }
                
            $this->assign( 'contributionText', $contributionText );
            
            // we always generate urls for the front end in joomla
            if ( $action ==  CRM_Core_Action::PREVIEW ) {
                $url    = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                 "id={$pcpInfo['contribution_page_id']}&pcpId={$this->_id}reset=1&action=preview",
                                                 true, null, true,
                                                 true );
            } else {
                $url = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                              "id={$pcpInfo['contribution_page_id']}&pcpId={$this->_id}&reset=1",
                                              true, null, true,
                                              true );
            }
            $this->assign( 'contributeURL', $url    );
        }
        
        // we do not want to display recently viewed items, so turn off
        $this->assign('displayRecent' , false );
        parent::run();
    }
    
    function getTemplateFileName() 
    {
        if ( $this->_id ) {
            $templateFile = "CRM/Contribute/Page/{$this->_id}/PCPInfo.tpl";
            $template     =& CRM_Core_Page::getTemplate( );
            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }


}

