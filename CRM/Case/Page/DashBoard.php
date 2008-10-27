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
 * This page is for Case Dashboard
 */
class CRM_Case_Page_DashBoard extends CRM_Core_Page 
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
        CRM_Utils_System::setTitle( ts('CiviCase') );
        $this->_caseType = array();
        $statuses   = array( 'Active', 'Closed', 'New', 'Inactive' );
        require_once 'CRM/Core/OptionGroup.php';    
        $caseTypes  = CRM_Core_OptionGroup::values('case_type');
        require_once 'CRM/Case/BAO/Case.php';
        foreach ( $caseTypes as $typeId => $caseType ) {
            $typeId = '%'.$typeId.'%';
            foreach ( $statuses as $status ) {
                $this->_caseType[$caseType][$status]  =  CRM_Case_BAO_Case::getCaseSummary( $status, $typeId );
            }
        }
        $this->assign('caseType', $this->_caseType);
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
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Search', 
                                                        ts('Case'), 
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