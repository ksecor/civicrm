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
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Member_Invoke {

    static function admin( &$args ) {
        if ( $args[1] !== 'admin' && $args[2] !== 'member' ) {
            return;
        }
        $view = null;

        switch ( CRM_Utils_Array::value( 3, $args, '' ) ) {

        case 'membershipType':
            require_once 'CRM/Member/Page/MembershipType.php';
            $view =& new CRM_Member_Page_MembershipType(ts('Membership Types'));
            break;
            
        case 'membershipStatus':
            require_once 'CRM/Member/Page/MembershipStatus.php';
            $view =& new CRM_Member_Page_MembershipStatus(ts('Membership Status'));
            break;
            
        case 'messageTemplates':
            require_once 'CRM/Member/Page/MessageTemplates.php';
            $view =& new CRM_Member_Page_MessageTemplates(ts('Message Templates'));
            break;
            
        default:
            require_once 'CRM/Member/Page/MembershipType.php';
            $view =& new CRM_Member_Page_MembershipType(ts('Membership Types'));
            break;
        }

        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm' ) );
    }

    /*
     * This function contains the actions for member arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args ) {  
        if ( $args[1] !== 'member' ) {  
            return;  
        }
        
        $session =& CRM_Core_Session::singleton( );
        $config  =& CRM_Core_Config::singleton ( );
        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 

        if ($secondArg == 'search') {
            require_once 'CRM/Member/Controller/Search.php';
            $controller =& new CRM_Member_Controller_Search($title, $mode); 
            $url = 'civicrm/member/search';
            $session->pushUserContext(CRM_Utils_System::url($url, 'force=1')); 
            $controller->set( 'context', 'search' );
            return $controller->run();
            
            
        } elseif ($secondArg == 'import') {
            require_once 'CRM/Member/Import/Controller.php';
            $controller =& new CRM_Member_Import_Controller(ts('Import Members'));
            return $controller->run();
        } else {
            require_once 'CRM/Member/Page/DashBoard.php';
            $view =& new CRM_Member_Page_DashBoard( ts('DashBoard') );
            return $view->run( );
        }
    }
    
}

?>
