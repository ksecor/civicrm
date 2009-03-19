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

class CRM_Upgrade_Page_Upgrade extends CRM_Core_Page {
    function preProcess( ) {
        parent::preProcess( );
    }

    function run( ) {
        $latestVer  = CRM_Utils_System::version();
        $currentVer = CRM_Core_BAO_Domain::version();

        // hack to make past ver compatible /w new incremental upgrade process
        $convertVer = array( '2.1'      => '2.1.0',
                             '2.2'      => '2.2.alpha1',
                             '2.2.alph' => '2.2.alpha3',
                             );
        if ( isset($convertVer[$currentVer]) ) {
            $currentVer = $convertVer[$currentVer];
        }
        
        $config =& CRM_Core_Config::singleton( );
        // This could be removed in later rev
        if ( $currentVer == '2.1.6' ) {
            // also cleanup the templates_c directory
            $config->cleanup( 1 );
            
            if ( $config->userFramework !== 'Standalone' ) {
                // clean the session
                $session =& CRM_Core_Session::singleton( );
                $session->reset( 2 );
            }
        }
        // end of hack
        
        CRM_Utils_System::setTitle(ts('Upgrade CiviCRM to Version %1', 
                                      array( 1 => $latestVer )));
        
        $upgrade  =& new CRM_Upgrade_Form( );

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'pageTitle', ts('Upgrade CiviCRM to Version %1', 
                                           array( 1 => $latestVer )));
        $template->assign( 'menuRebuildURL', 
                           CRM_Utils_System::url( 'civicrm/menu/rebuild', 'reset=1' ) );
        $template->assign( 'cancelURL', 
                          CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );

        if ( version_compare($currentVer, $latestVer) >= 0 ) {
            $message = ts( 'Your database has already been upgraded to CiviCRM %1',
                           array( 1 => $latestVer ) );
            $template->assign( 'upgraded', true );
        } else {
            $message   = ts('CiviCRM upgrade was successful.');
            $template->assign( 'currentVersion',  $currentVer);
            $template->assign( 'newVersion',      $latestVer );
            $template->assign( 'upgradeTitle',   ts('Upgrade CiviCRM from v %1 To v %2', 
                                                    array( 1=> $currentVer, 2=> $latestVer ) ) );
            $template->assign( 'upgraded', false );

            if ( CRM_Utils_Array::value('upgrade', $_POST) ) {
                $revisions = $upgrade->getRevisionSequence();
                foreach ( $revisions as $rev ) {
                    // proceed only if $currentVer < $rev
                    if ( version_compare($currentVer, $rev) < 0 ) {
                        
                        $phpFunctionName = 'upgrade_' . str_replace( '.', '_', $rev );
                        if ( is_callable(array($this, $phpFunctionName)) ) {
                            eval("\$this->{$phpFunctionName}('$rev');");
                        } else {
                            $upgrade->processSQL( $rev );
                        }
                        $upgrade->setVersion( $rev );
                    }
                }
                $upgrade->setVersion( $latestVer );
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
        echo CRM_Utils_System::theme( 'page', $content, true, $this->_print, false, true );
    }

    function upgrade_2_2_alpha1( $rev ) {
        for ( $stepID = 1; $stepID <= 4; $stepID++ ) {
            require_once "CRM/Upgrade/TwoTwo/Form/Step{$stepID}.php";
            $formName = "CRM_Upgrade_TwoTwo_Form_Step{$stepID}";
            eval( "\$form = new $formName( );" );
            
            $error = null;
            if ( ! $form->verifyPreDBState( $error ) ) {
                if ( ! isset( $error ) ) {
                    $error = "pre-condition failed for current upgrade step $stepID, rev $rev";
                }
                CRM_Core_Error::fatal( $error );
            }
            
            if ( $stepID == 4 ) {
                return;
            }

            $template =& CRM_Core_Smarty::singleton( );

            $eventFees = array( );
            $query = "SELECT og.id ogid FROM civicrm_option_group og WHERE og.name LIKE  %1";
            $params = array( 1 => array(  'civicrm_event_page.amount%', 'String' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
            while ( $dao->fetch( ) ) { 
                $eventFees[$dao->ogid] = $dao->ogid;  
            }
            $template->assign( 'eventFees', $eventFees );    
            
            $form->upgrade( );
            
            if ( ! $form->verifyPostDBState( $error ) ) {
                if ( ! isset( $error ) ) {
                    $error = "post-condition failed for current upgrade step $stepID, rev $rev";
                }
                CRM_Core_Error::fatal( $error );
            }
        }
    }

    function upgrade_2_1_2( $rev ) {
        require_once "CRM/Upgrade/TwoOne/Form/TwoOneTwo.php";
        $formName = "CRM_Upgrade_TwoOne_Form_TwoOneTwo";
        eval( "\$form = new $formName( '$rev' );" );
        
        $error = null;
        if ( ! $form->verifyPreDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = "pre-condition failed for current upgrade for $rev";
            }
            CRM_Core_Error::fatal( $error );
        }

        $form->upgrade( );

        if ( ! $form->verifyPostDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = "post-condition failed for current upgrade for $rev";
            }
            CRM_Core_Error::fatal( $error );
        }
    }

    /**
     * This function should check if if need to skip current sql file
     * Name of this function will change according to the latest release 
     *   
     */
    function upgrade_2_2_alpha3( $rev ) {
        // skip processing sql file, if fresh install -
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup','mail_protocol','id','name' ) ) {
            $upgrade  =& new CRM_Upgrade_Form( );
            $upgrade->processSQL( $rev );
        }
        return true;
    }

    function upgrade_2_2_beta1( $rev ) {
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_pcp_block', 'notify_email' ) ) {
            $template =& CRM_Core_Smarty::singleton( );
            $template->assign( 'notifyAbsent', true );
        }
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }

    function upgrade_2_2_beta2( $rev ) {
        $template =& CRM_Core_Smarty::singleton( );
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 
                                            'CRM_Contact_Form_Search_Custom_ZipCodeRange','id','name' ) ) {
            $template->assign( 'customSearchAbsentAll', true );
        } else if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 
                                                   'CRM_Contact_Form_Search_Custom_MultipleValues','id','name' ) ) {
            $template->assign( 'customSearchAbsent', true );
        }
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }
    
    function upgrade_2_2_beta3( $rev ) {
        $template =& CRM_Core_Smarty::singleton( );
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup','custom_data_type','id','name' ) ) {
            $template->assign( 'customDataType', true );
        }
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }
}

