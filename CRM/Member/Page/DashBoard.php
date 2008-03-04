<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

require_once 'CRM/Member/Page/DashBoard.php';

/**
 * Page for displaying list of Payment-Instrument
 */
class CRM_Member_Page_DashBoard extends CRM_Core_Page 
{
    /** 
     * Heart of the viewing process. The runner gets all the meta data for 
     * the contact and calls the appropriate type of page to view. 
     * 
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) 
    {
        require_once "CRM/Member/BAO/MembershipType.php";
        require_once "CRM/Member/BAO/Membership.php";
        $membershipSummary = array();
        
        
        $membershipTypes = CRM_Member_BAO_MembershipType::getMembershipTypes(false);
        foreach ( $membershipTypes as $key => $value ) {
            $membershipSummary[$key] = CRM_Member_BAO_Membership::getMembershipSummary($key ,$value);
        }
        // need to build url
         $currentMonth    = date("Ym01");
         $currentMonthEnd = date("Ymt");
         $currentYear     = date("Y0101");
         $currentYearEnd  = date("Y1231");
         
         require_once "CRM/Member/BAO/MembershipStatus.php";
         $status = CRM_Member_BAO_MembershipStatus::getMembershipStatusCurrent();
         $status = implode(',' , $status );
         
         foreach( $membershipSummary as $typeID => $details) {
             foreach ( $details as $key => $value ) {
                 switch ($key) {
                 case 'month':
                     $membershipSummary[$typeID][$key]['url'] = CRM_Utils_System::url( 'civicrm/member/search',"reset=1&force=1&type=$typeID&start=$currentMonth&end=$currentMonthEnd" );
                     break;
                 case 'year':
                     $membershipSummary[$typeID][$key]['url'] = CRM_Utils_System::url( 'civicrm/member/search',"reset=1&force=1&type=$typeID&start=$currentYear&end=$currentYearEnd" );
                     break;
                 case 'current':
                     $membershipSummary[$typeID][$key]['url'] = CRM_Utils_System::url( 'civicrm/member/search',"reset=1&force=1&status=$status&type=$typeID" );
                }
            }
        }
        

        $totalCount = array();
        $totalCountMonth = $totalCountYear = $totalCountCurrent = 0;
        foreach( $membershipSummary as $key => $value ) {
            $totalCountMonth   = $totalCountMonth   +  $value['month']['count'];
            $totalCountYear    = $totalCountYear    +  $value['year']['count'];
            $totalCountCurrent = $totalCountCurrent +  $value['current']['count'];
            
        }
        
        $totalCount['month'] = array("count" => $totalCountMonth,
                                     "url"   => CRM_Utils_System::url( 'civicrm/member/search',
                                                                       "reset=1&force=1&start=$currentMonth&end=$currentMonthEnd" ),
                                    );
        $totalCount['year'] = array("count" => $totalCountYear,
                                     "url"   => CRM_Utils_System::url( 'civicrm/member/search',
                                                                       "reset=1&force=1&start=$currentYear&end=$currentYearEnd" ),
                                    );
        $totalCount['current'] = array("count" => $totalCountCurrent,
                                     "url"   => CRM_Utils_System::url( 'civicrm/member/search',
                                                                       "reset=1&force=1&status=$status" ),
                                    );
        
        $this->assign('membershipSummary' , $membershipSummary);
        $this->assign('totalCount' , $totalCount);
        $this->assign('currentMonth' , date('F'));
        $this->assign('currentYear' ,  date('Y'));
        
    }

    /** 
     * This function is the main function that is called when the page loads, 
     * it decides the which action has to be taken for the page. 
     *                                                          
     * return null        
     * @access public 
     */                                                          
    function run( ) { 
        $this->preProcess( );
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_Search', ts('Member'), null ); 
        $controller->setEmbedded( true ); 
        $controller->reset( ); 
        $controller->set( 'limit', 20 );
        $controller->set( 'force', 1 );
        $controller->set( 'context', 'dashboard' ); 
        $controller->process( ); 
        $controller->run( ); 
        
        return parent::run( );
    }

}


