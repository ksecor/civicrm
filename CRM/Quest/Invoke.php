<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * Given an argument list, invoke the appropriate CRM function
 * Serves as a wrapper between the UserFrameWork and Core CRM
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

class CRM_Quest_Invoke {

    /*
     * This function contains the actions for mailing arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args ) {  
        if ( $args[1] !== 'quest' ) {
            return;
        }

        $controller =  null;
        $session    =& CRM_Core_Session::singleton( );

        switch ( $args[2] ) {
        case 'preapp':
            require_once 'CRM/Quest/Controller/PreApp.php';
            $controller =& new CRM_Quest_Controller_PreApp( null, null, false );
            break;

        case 'matchapp':
            
            switch( $args[3] ) {

            case 'household':
                require_once 'CRM/Quest/Controller/MatchApp/Household.php';
                $controller =& new CRM_Quest_Controller_MatchApp_Household( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/quest/matchapp/school', 'reset=1' ) );
                break;


            case 'school':
                require_once 'CRM/Quest/Controller/MatchApp/School.php';
                $controller =& new CRM_Quest_Controller_MatchApp_School( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/quest/matchapp/essay', 'reset=1' ) );
                break;


            case 'essay':
                require_once 'CRM/Quest/Controller/MatchApp/Essay.php';
                $controller =& new CRM_Quest_Controller_MatchApp_Essay( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/quest/matchapp/college', 'reset=1' ) );
                break;

            case 'college':
                require_once 'CRM/Quest/Controller/MatchApp/College.php';
                $controller =& new CRM_Quest_Controller_MatchApp_College( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/quest/matchapp/partner', 'reset=1' ) );
                break;

            case 'partner':
                require_once 'CRM/Quest/Controller/MatchApp/Partner.php';
                $controller =& new CRM_Quest_Controller_MatchApp_Partner( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'locker', 'reset=1&status=1' ) );
                break;

            default        :
                require_once 'CRM/Quest/Controller/MatchApp/Personal.php';
                $controller =& new CRM_Quest_Controller_MatchApp_Personal( null, null, false );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/quest/matchapp/household', 'reset=1' ) );
                break;
            }
            break;

        case 'teacher':
            require_once 'CRM/Quest/Controller/Recommender/Teacher.php';
            $controller =& new CRM_Quest_Controller_Recommender_Teacher( null, null, false );
            $session->pushUserContext( CRM_Utils_System::url( 'locker', 'reset=1&status=1' ) );
            break;

        case 'counselor':
            require_once 'CRM/Quest/Controller/Recommender/Counselor.php';
            $controller =& new CRM_Quest_Controller_Recommender_Counselor( null, null, false );
            $session->pushUserContext( CRM_Utils_System::url( 'locker', 'reset=1&status=1' ) );
            break;
            
        case 'schoolsearch':
            require_once 'CRM/Core/Controller/Simple.php';
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Quest_Form_SchoolSearch', ts( 'CEEB School Search' ), null );
            break;

        case 'verify':
            require_once 'CRM/Core/Controller/Simple.php';
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Quest_Form_Verify', ts( 'Verify your Registration' ), null );
            return $controller->run( );

        }

        if ( $controller ) {
            return $controller->run( );
        }

    }

    static function admin( &$args ) {
        return;
    }

}

?>
