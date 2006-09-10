<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

require_once 'CRM/Contribute/Page/DashBoard.php';

/**
 * Page for displaying list of Payment-Instrument
 */
class CRM_Contribute_Page_DashBoard extends CRM_Core_Page 
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
        $startToDate = array( );
        $yearToDate  = array( );
        $monthToDate = array( );

        $status = array( 'Valid', 'Cancelled' );
        
        $startDate = null;
        $yearDate  = date('Y') . '0101000000';
        $monthDate = date('Ym') . '01000000';

        // we are specific since we want all information till this second
        $now       = date( 'YmdHis' );

        $prefixes = array( 'start', 'year', 'month' );
        $status   = array( 'Valid', 'Cancelled' );

        foreach ( $prefixes as $prefix ) {
            $aName = $prefix . 'ToDate';
            $dName = $prefix . 'Date';
            foreach ( $status as $s ) {
                ${$aName}[$s]        = CRM_Contribute_BAO_Contribution::getTotalAmountAndCount( $s, $$dName, $now );
                ${$aName}[$s]['url'] = CRM_Utils_System::url( 'civicrm/contribute/search',
                                                              "reset=1&force=1&status=$s&start={$$dName}&end=$now" );
            }
            $this->assign( $aName, $$aName );
        }
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
        
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), $this->_action ); 
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

?>
