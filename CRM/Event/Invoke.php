<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

class CRM_Event_Invoke {

    static function admin( &$args ) {
        if ( $args[1] !== 'admin' && $args[2] !== 'event' ) {
            return;
        }
        $view = null;

        switch ( CRM_Utils_Array::value( 3, $args, '' ) ) {

        case 'manageEvent':
            require_once 'CRM/Event/Page/ManageEvent.php';
            $view =& new CRM_Event_Page_ManageEvent(ts('Manage Event'));
            break;
            
        case 'eventWizard':
            require_once 'CRM/Event/Page/EventWizard.php';
            $view =& new CRM_Event_Page_EventWizard(ts('Event Wizard'));
            break;
            
        case 'participants':
            require_once 'CRM/Event/Page/Participants.php';
            $view =& new CRM_Event_Page_Participants(ts('Event Type, Participant Status, Participant Role'));
            break;
            
        default:
            require_once 'CRM/Event/Page/ManageEvent.php';
            $view =& new CRM_Event_Page_ManageEvent(ts('Eventship Types'));
            break;
        }

        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm' ) );
    }

    /*
     * This function contains the actions for event arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args ) {  
        if ( $args[1] !== 'event' ) {  
            return;  
        }
    }
    
}

?>