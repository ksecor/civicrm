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
require_once 'CRM/Upgrade/Form.php';
require_once 'CRM/Core/BAO/Domain.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Upgrade/TwoOne/Page/Upgrade.php';
class CRM_Upgrade_TwoTwo_Page_Upgrade extends CRM_Core_Page {

    function run( ) {
        $upgrade  =& new CRM_Upgrade_Form( );
        CRM_Utils_System::setTitle(ts('Upgrade CiviCRM to Version %1', 
                                         array( 1 => $upgrade->latestVersion ) ) );
        $template =& CRM_Core_Smarty::singleton( );

        $template->assign( 'pageTitle', ts('Upgrade CiviCRM to Version %1', 
                                           array( 1 => $upgrade->latestVersion ) ) );
        $template->assign( 'menuRebuildURL', 
                           CRM_Utils_System::url( 'civicrm/menu/rebuild', 'reset=1' ) );
        $template->assign( 'cancelURL', 
                          CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );

        //check DB for is already upgraded.
        if ( $upgrade->checkVersion( $upgrade->latestVersion ) ) {
            $message = ts( 'Your database has already been upgraded to CiviCRM %1',
                           array( 1 => $upgrade->latestVersion ) );
            $template->assign( 'upgraded', true );
        } else {
            //get the current version
            $currentVersion = CRM_Core_BAO_Domain::version();
            $message        = ts('CiviCRM upgrade was successful.');

            $template->assign( 'currentVersion',  $currentVersion);
            $template->assign( 'newVersion',      $upgrade->latestVersion);
            $template->assign( 'upgradeTitle',   ts('Upgrade CiviCRM from v %1 To v %2', 
                                                    array( 1=> $currentVersion, 2=> $upgrade->latestVersion ) ) );
            $template->assign( 'pageTitle', ts('Upgrade CiviCRM to Version %1',
                                               array( 1 => $upgrade->latestVersion ) ) );
            $template->assign( 'upgraded', false );

            if ( CRM_Utils_Array::value('upgrade', $_POST) ) {
                if ( $upgrade->checkVersion( '2.1.0' ) ||
                     $upgrade->checkVersion( '2.1' )   || 
                     $upgrade->checkVersion( '2.1.1' ) ) {
                    // 2.1 to 2.1.2
                    $twoOne = new CRM_Upgrade_TwoOne_Page_Upgrade();
                    $twoOne->runTwoOneTwo( );
                }

                // if version is 2.1.2 OR 2.1.3 OR 2.1.4 do normal
                // upgrade, no changes in DB schema
                for ( $i = 1; $i <= 4; $i++ ) {
                    $this->runForm( $i );
                }

                // update the ver in the db
                $upgrade->setVersion( $upgrade->latestVersion );
                $template->assign( 'upgraded', true );
                
                // also cleanup the templates_c directory
                $config =& CRM_Core_Config::singleton( );
                $config->cleanup( 1 );
                
                // clean the session. Note: In case of standalone this makes the user logout. 
                // So skip this step for standalone. 
                if ( $config->userFramework !== 'Standalone' ) {
                    $session =& CRM_Core_Session::singleton( );
                    $session->reset( 2 );
                }
            }
        }

        $template->assign( 'message', $message );
        $content = $template->fetch( 'CRM/common/success.tpl' );
        echo CRM_Utils_System::theme( 'page', $content, true, $this->_print );
    }

    function runForm( $stepID ) {
        require_once "CRM/Upgrade/TwoTwo/Form/Step{$stepID}.php";
        $formName = "CRM_Upgrade_TwoTwo_Form_Step{$stepID}";
        eval( "\$form = new $formName( );" );
        
        $error = null;
        if ( ! $form->verifyPreDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = 'pre-condition failed for current upgrade step $stepID';
            }
            CRM_Core_Error::fatal( $error );
        }

        if ( $stepID == 4 ) {
            return;
        }

        $form->upgrade( );

        if ( ! $form->verifyPostDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = 'post-condition failed for current upgrade step $stepID';
            }
            CRM_Core_Error::fatal( $error );
        }
    }
}
