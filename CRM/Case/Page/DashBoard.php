<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * This page is for Case Dashboard
 */
class CRM_Case_Page_DashBoard extends CRM_Core_Page 
{
    /**
     * Open Case activity type id
     */
    protected $_openCaseId = null;

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
        CRM_Utils_System::setTitle( ts('CiviCase Dashboard') );
        $allCases = CRM_Utils_Request::retrieve( 'all', 'Positive',
                                                   CRM_Core_DAO::$_nullObject );
        $session = & CRM_Core_Session::singleton();
        $userID  = $session->get('userID');
        if ( ! $allCases ) {
            $this->assign('myCases', true );
            $allCases = false;
        } else {
            $this->assign('myCases', false );
            $allCases = true;
        }
        require_once 'CRM/Case/BAO/Case.php';
        $summary  = CRM_Case_BAO_Case::getCasesSummary( );
        $upcoming = CRM_Case_BAO_Case::getCases( $allCases, $userID, $type = 'upcoming');
        $recent   = CRM_Case_BAO_Case::getCases( $allCases, $userID, $type = 'recent');
        
        $this->assign('casesSummary',  $summary);
        if( !empty( $upcoming ) ) {
            $this->assign('upcomingCases', $upcoming);
        }
        if( !empty( $recent ) ) {
            $this->assign('recentCases',   $recent);
        }
        
        // Retrieve the activity type id for "Open Case" so we can use it for New Case for New Client link
        $this->_openCaseId         = CRM_Core_OptionGroup::getValue( 'activity_type', 'Open Case', 'name' );
        $this->assign( 'openCaseId', $this->_openCaseId);
    }
    
    /** 
     * This function is the main function that is called when the page loads, 
     * it decides the which action has to be taken for the page. 
     *                                                          
     * return null        
     * @access public 
     */                                                          
    function run( ) 
    {
        $this->preProcess( );
        
        return parent::run( );
    }

}