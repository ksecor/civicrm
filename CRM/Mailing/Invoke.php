<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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

class CRM_Mailing_Invoke {

    /*
     * This function contains the actions for mailing arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args ) {  
        if ( $args[1] !== 'mailing' ) {
            return;
        }
        
        require_once 'CRM/Mailing/Controller/Send.php';
        require_once 'CRM/Mailing/Page/Browse.php';
        require_once 'CRM/Mailing/BAO/Job.php';

        if ( $args[2] == 'forward' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::baseURL());
            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Profile_Form_ForwardMailing', ts('Forward Mailing'),  null );
        }
        
        if ( $args[2] == 'retry' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(
                CRM_Utils_System::url('civicrm/mailing/browse'));
            CRM_Utils_System::appendBreadCrumb(
                '<a href="' . CRM_Utils_System::url('civicrm/mailing/browse') . '">' . ts('Mailings') . '</a>'
            );
            CRM_Utils_System::appendBreadCrumb(
                '<a href="' . CRM_Utils_System::url('civicrm/mailing/report') . '">' . ts('Report') . '</a>'
            );
            $wrapper =& new CRM_Utils_Wrapper();
            return $wrapper->run( 'CRM_Mailing_Form_Retry', 
                                    ts('Retry Mailing'), null);
        }

        if ( $args[2] == 'component' ) {
            require_once 'CRM/Mailing/Page/Component.php';
            $view =& new CRM_Mailing_Page_Component( );
            return $view->run( );
        }
        if ( $args[2] == 'browse' ) {
            require_once 'CRM/Mailing/Page/Browse.php';
            $view =& new CRM_Mailing_Page_Browse( );
            return $view->run( );
        }
        if ( $args[2] == 'event' ) {
            CRM_Utils_System::appendBreadCrumb(
                '<a href="' . CRM_Utils_System::url('civicrm/mailing/browse') . '">' . ts('Mailings') . '</a>'
            );
            CRM_Utils_System::appendBreadCrumb(
                '<a href="' . CRM_Utils_System::url('civicrm/mailing/report') . '">' . ts('Report') . '</a>'
            );
            require_once 'CRM/Mailing/Page/Event.php';
            $view =& new CRM_Mailing_Page_Event( );
            return $view->run( );
        }
        if ( $args[2] == 'report' ) {
            CRM_Utils_System::appendBreadCrumb(
                '<a href="' . CRM_Utils_System::url('civicrm/mailing/browse') . '">' . ts('Mailings') . '</a>'
            );
            require_once 'CRM/Mailing/Page/Report.php';
            $view =& new CRM_Mailing_Page_Report( );
            return $view->run();
        }

        if ( $args[2] == 'send' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::url('civicrm/mailing/queue', 'reset=1'));
            require_once 'CRM/Mailing/Controller/Send.php';
            $controller =& new CRM_Mailing_Controller_Send( ts( 'Send Mailing' ) );
            return $controller->run( );
        }

        if ( $args[2] == 'queue' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::url('civicrm/mailing/browse', 'reset=1'));
            require_once 'CRM/Mailing/BAO/Job.php';
            CRM_Mailing_BAO_Job::runJobs();
            CRM_Core_Session::setStatus( ts('The mailing queue has been processed.') );
        }

        require_once 'CRM/Mailing/Page/Browse.php';
        $view =& new CRM_Mailing_Page_Browse( );
        return $view->run( );
    }

}

?>
