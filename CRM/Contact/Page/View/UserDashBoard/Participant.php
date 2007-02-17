<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View/UserDashBoard.php';

/**
 * This class is for building event(participation) block on user dashboard
 */
class CRM_Contact_Page_View_UserDashBoard_Participant extends CRM_Contact_Page_View_UserDashBoard 
{
    /**
     * Function to list participations for the UF user
     * 
     * return null
     * @access public
     */
    function listParticipations( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Event_Form_Search', ts('Events'), null );
        $controller->setEmbedded( true );
        $controller->reset( );
        $controller->set( 'context', 'user' ); 
        $controller->process( );
        $controller->run( );
    }
    
    /**
     * This function is the main function that is called when the page
     * loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        parent::preProcess( );
        $this->listParticipations( ); 
    }
      
}

?>
