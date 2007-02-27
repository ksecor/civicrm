<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Contribute_Invoke {

    static function admin( &$args ) {
        if ( $args[1] !== 'admin' && $args[2] !== 'contribute' ) {
            return;
        }

        if ( ! CRM_Core_Permission::check( 'administer CiviCRM' ) ||
             ! CRM_Core_Permission::check('access CiviContribute') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page' );
        }

        
        $view = null;

        switch ( CRM_Utils_Array::value( 3, $args, '' ) ) {

        case 'contributionType':
            require_once 'CRM/Contribute/Page/ContributionType.php';
            $view =& new CRM_Contribute_Page_ContributionType(ts('Contribution Types'));
            break;
            
        case 'paymentInstrument':
            require_once 'CRM/Contribute/Page/PaymentInstrument.php';
            $view =& new CRM_Contribute_Page_PaymentInstrument(ts('Payment Instruments'));
            break;
            
        case 'acceptCreditCard':
            require_once 'CRM/Contribute/Page/AcceptCreditCard.php';
            $view =& new CRM_Contribute_Page_AcceptCreditCard(ts('Accept Credit Cards'));
            break;

        case 'managePremiums':
            require_once 'CRM/Contribute/Page/ManagePremiums.php';
            $view =& new CRM_Contribute_Page_ManagePremiums(ts('Manage Premiums'));
            break;
            
        case 'createPPD':
            $session =& CRM_Core_Session::singleton( );
            $session->pushUserContext( CRM_Utils_System::url( 'civicrm/admin', 'reset=1' ) );
            $wrapper =& new CRM_Utils_Wrapper( ); 
            return $wrapper->run( 'CRM_Contribute_Form_CreatePPD', ts( 'Create PPD' ), null );
            break;
            
        default:
            require_once 'CRM/Contribute/Page/ContributionPage.php'; 
            $view =& new CRM_Contribute_Page_ContributionPage(ts('Contribution Page'));  
            break;
        }

        if ( $view ) {
            return $view->run( );
        }

        return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm' ) );
    }

    /*
     * This function contains the actions for contribute arguments  
     *  
     * @param $args array this array contains the arguments of the url  
     *  
     * @static  
     * @access public  
     */  
    static function main( &$args ) {  
        if ( $args[1] !== 'contribute' ) {  
            return;  
        }  

        $session =& CRM_Core_Session::singleton( );
        $config  =& CRM_Core_Config::singleton ( );

        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 

        if ( $secondArg == 'transact' ) { 
            if ( $config->enableSSL     &&
                 CRM_Core_Invoke::onlySSL( $args ) ) {
                if ( !isset($_SERVER['HTTPS'] ) ) {
                    CRM_Utils_System::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
                } else {
                    CRM_Utils_System::mapConfigToSSL( );
                }
            }

            // also reset the bread crumb
            CRM_Utils_System::resetBreadCrumb( );

            require_once 'CRM/Contribute/Controller/Contribution.php'; 
            $controller =& new CRM_Contribute_Controller_Contribution( );
            return $controller->run(); 
        } 

        if ( ! CRM_Core_Permission::check('access CiviContribute') ) {
            CRM_Core_Error::fatal( 'You do not have access to this page' );
        }

        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 
        if ( $secondArg == 'search') {
            require_once 'CRM/Contribute/Controller/Search.php'; 
            $controller =& new CRM_Contribute_Controller_Search( );
            $url = 'civicrm/contribute/search';
            $session->pushUserContext(CRM_Utils_System::url($url, 'force=1')); 
            $controller->set( 'context', 'search' );
            return $controller->run();
        } elseif ($secondArg == 'import') {
            if ( ! CRM_Core_Permission::check('administer CiviCRM') ) {
                CRM_Core_Error::fatal( 'You do not have access to this page' );
            }
            require_once 'CRM/Contribute/Import/Controller.php';
            $controller =& new CRM_Contribute_Import_Controller(ts('Import Contributions'));
            return $controller->run();
        } else if ( $secondArg == 'add' ) {
            $session =& CRM_Core_Session::singleton( );  
            $session->pushUserContext( CRM_Utils_System::url('civicrm/contribute', 'action=browse&reset=1' ) ); 

            require_once 'CRM/Contribute/Controller/ContributionPage.php'; 
            $controller =& new CRM_Contribute_Controller_ContributionPage( ); 
            return $controller->run( ); 
        } else if ( $secondArg == 'contribution' ) {
            require_once 'CRM/Contribute/Page/Contribution.php';
            $page =& new CRM_Contribute_Page_Contribution( );
            return $page->run( );
        } else {
            require_once 'CRM/Contribute/Page/DashBoard.php';
            $view =& new CRM_Contribute_Page_DashBoard( ts('DashBoard') );
            return $view->run( );
        }
    }

}

?>