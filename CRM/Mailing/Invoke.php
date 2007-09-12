<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
        
        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 
        
        $pages = array( 'unsubscribe' => 'Unsubscribe',
                        'optout'      => 'Optout',
                        'confirm'     => 'Confirm',
                        'component'   => 'Component',
                        'mailcomp'    => 'Component',
                        'browse'      => 'Browse',
                        'preview'     => 'Preview',
                        );

        if ( isset( $pages[$secondArg] ) ) {
            require_once "CRM/Mailing/Page/{$pages[$secondArg]}.php";
            eval( '$view = new CRM_Mailing_Page_' . $pages[$secondArg] . '( );' );
            return $view->run( );
        }

        if ( $secondArg == 'forward' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::baseURL());
            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Profile_Form_ForwardMailing', ts('Forward Mailing'),  null );
        }

        if ( $secondArg == 'subscribe' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::baseURL());
            $wrapper =& new CRM_Utils_Wrapper( );
            return $wrapper->run( 'CRM_Mailing_Form_Subscribe', ts( 'Subscribe' ), null );
        }

        if ( $secondArg == 'retry' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(
                CRM_Utils_System::url('civicrm/mailing/browse'));
            CRM_Utils_System::appendBreadCrumb( ts( 'Mailings' ),
                                                CRM_Utils_System::url( 'civicrm/mailing/browse' ) );
            CRM_Utils_System::appendBreadCrumb( ts( 'Report' ),
                                                CRM_Utils_System::url( 'civicrm/mailing/report' ) );
            $wrapper =& new CRM_Utils_Wrapper();
            return $wrapper->run( 'CRM_Mailing_Form_Retry', 
                                  ts('Retry Mailing'), null);
        }

		if ( $secondArg == 'composer' ) {
            require_once 'CRM/Mailing/Form/Composer/Compose.php';
            $wrapper =& new CRM_Utils_Wrapper();
            return $wrapper->run( 'CRM_Mailing_Form_Composer_Compose', 
                                  ts('Composer'), null);
        }
		
        
        if ( $secondArg == 'report' ) {
            $thirdArg  = CRM_Utils_Array::value( 3, $args, '' ); 
            if  ( $thirdArg  == 'event' ) {
                CRM_Utils_System::appendBreadCrumb( ts( 'Mailings' ),
                                                    CRM_Utils_System::url( 'civicrm/mailing/browse' ) );
                CRM_Utils_System::appendBreadCrumb( ts( 'Report' ),
                                                    CRM_Utils_System::url( 'civicrm/mailing/report' ) );
                require_once 'CRM/Mailing/Page/Event.php';
                $view =& new CRM_Mailing_Page_Event( );
            } else {
                CRM_Utils_System::appendBreadCrumb( ts( 'Mailings' ),
                                                    CRM_Utils_System::url( 'civicrm/mailing/browse' ) );
                require_once 'CRM/Mailing/Page/Report.php';
                $view =& new CRM_Mailing_Page_Report( );
            }
            return $view->run();
        }
        
        if ( $secondArg == 'send' ) {
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext(CRM_Utils_System::url('civicrm/mailing/browse', 'reset=1'));
            require_once 'CRM/Mailing/Controller/Send.php';
            $controller =& new CRM_Mailing_Controller_Send( ts( 'Send Mailing' ) );
            return $controller->run( );
        }
        
        if ( $secondArg == 'queue' ) {
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
    
    static function admin( &$args ) {
        return;
    }
    
}

?>
