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
 * This is page is for Pledge Dashboard
 */
class CRM_Pledge_Page_DashBoard extends CRM_Core_Page 
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
        CRM_Utils_System::setTitle( ts('CiviPledge') );
        
        $startToDate   = array( );
        $yearToDate    = array( );
        $monthToDate   = array( );
        $previousToDate = array( );
        
        $status = array( 'Valid', 'Cancelled' );
        
        $startDate = null;
        $config =& CRM_Core_Config::singleton( );
        $yearDate = $config->fiscalYearStart;
        $year  = array('Y' => date('Y'));
        $this->assign('curYear', $year['Y']);
        $yearDate = array_merge($year,$yearDate);
        $yearDate = CRM_Utils_Date::format( $yearDate );
        
        $monthDate = date('Ym') . '01000000';

        $prefixes = array( 'start', 'month', 'year' , 'previous' );
        $status   = array( 'Valid', 'Cancelled', 'Pending', 'Overdue' );
        
        $yearDate  = $yearDate  . '000000';
        
        $previousDate = CRM_Utils_Date::customFormat(date( "Y-m-d", mktime(0, 0, 0, date("m")-1,01,date("Y"))) , '%Y%m%d').'000000';
        $previousMonth = date( "F Y", mktime(0, 0, 0, date("m")-1,01,date("Y")));
        $this->assign( 'previousMonthYear', $previousMonth );

        $currentMonth = date( "F Y", mktime(0, 0, 0, date("m"),01,date("Y")));
        $this->assign( 'currentMonthYear', $currentMonth );

        $previousDateEnd = CRM_Utils_Date::customFormat(date( "Y-m-t", mktime(0, 0, 0, date("m")-1,01,date("Y"))) , '%Y%m%d').'000000';
        // we are specific since we want all information till this second
        $now       = date( 'YmdHis' );
        
        require_once 'CRM/Pledge/BAO/Pledge.php';
        foreach ( $prefixes as $prefix ) {
            $aName = $prefix . 'ToDate';
            $dName = $prefix . 'Date';
            
            if ( $prefix == 'previous' ) {
                $now  = $previousDateEnd;
            }
            foreach ( $status as $s ) {
                ${$aName}[$s]        =  CRM_Pledge_BAO_Pledge::getTotalAmountAndCount( $s, $$dName, $now );
                ${$aName}[$s]['url'] = CRM_Utils_System::url( 'civicrm/pledge/search',
                                                              "reset=1&force=1&status=1&start={$$dName}&end=$now&test=0");
            }
            $this->assign( $aName, $$aName );
        }
        $admin = CRM_Core_Permission::check( 'access Pledge' );
        $this->assign( 'pledgeAdmin', $admin );
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
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Pledge_Form_Search', 
                                                       ts('Pledge'), 
                                                       null );
        $controller->setEmbedded( true ); 
        $controller->reset( ); 
        $controller->set( 'limit', 10 );
        $controller->set( 'force', 1 );
        $controller->set( 'context', 'dashboard' ); 
        $controller->process( ); 
        $controller->run( ); 
        
        return parent::run( );
    }
}

