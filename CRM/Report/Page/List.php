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
 * Page for displaying list of Reprots available
 */
class CRM_Report_Page_List extends CRM_Core_Page 
{
    /** 
     * Heart of the viewing process. The runner gets all the meta data for 
     * the reports and calls the appropriate type of page to view. 
     * 
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) 
    {
        CRM_Utils_System::setTitle( ts('Reports') );

        $list = array('1'=> array('Contribution Summary',
                                  CRM_Utils_System::url('civicrm/report/contribute/summary','reset=1') ),
                      
                      '2'=> array('Contribution Details',
                                  CRM_Utils_System::url('civicrm/report/contribute/details','reset=1') ),
                      
                      '3'=> array('Contribution Repeat Summary',
                                  CRM_Utils_System::url('civicrm/report/contribute/repeatSummary','reset=1') ),
                      
                      '4'=> array('Contribution Repeat Details',
                                  CRM_Utils_System::url('civicrm/report/contribute/repeatDetail','reset=1') ),
                      
                      '5'=> array('Contribution Summary Count',
                                  CRM_Utils_System::url('civicrm/report/contribute/summaryCount','reset=1') ),
                      
                      '6'=> array('Contact Summary',
                                  CRM_Utils_System::url('civicrm/report/contact/summary','reset=1') ),
                      
                      '7'=> array('Activity',
                                  CRM_Utils_System::url('civicrm/report/activity','reset=1') ),
                      
                      '8'=> array('Walk List',
                                  CRM_Utils_System::url('civicrm/report/walklist','reset=1') ),
                      
                      );
        $this->assign( 'list', $list );
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
        return parent::run( );
    }

}
?>