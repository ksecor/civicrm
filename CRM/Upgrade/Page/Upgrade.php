<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2008
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
        $currentRev = CRM_Core_BAO_Domain::version();

        CRM_Utils_System::setTitle(ts('Upgrade CiviCRM to Version %1', 
                                      array( 1 => $latestVer )));
        
        $upgrade    =& new CRM_Upgrade_Form( );

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'pageTitle', ts('Upgrade CiviCRM to Version %1', 
                                           array( 1 => $latestVer )));
        $template->assign( 'menuRebuildURL', 
                           CRM_Utils_System::url( 'civicrm/menu/rebuild', 'reset=1' ) );
        $template->assign( 'cancelURL', 
                          CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );

        if ( $upgrade->sortRevision($currentRev, $latestVer) >= 0 ) {
            $message = ts( 'Your database has already been upgraded to CiviCRM %1',
                           array( 1 => $latestVer ) );
            $template->assign( 'upgraded', true );
        } else {
            $message   = ts('CiviCRM upgrade was successful.');
            $template->assign( 'currentVersion',  $currentRev);
            $template->assign( 'newVersion',      $latestVer );
            $template->assign( 'upgradeTitle',   ts('Upgrade CiviCRM from v %1 To v %2', 
                                                    array( 1=> $currentRev, 2=> $latestVer ) ) );
            $template->assign( 'upgraded', false );

            if ( CRM_Utils_Array::value('upgrade', $_POST) ) {
                $revisions = $upgrade->getRevisionSequence();
                foreach ( $revisions as $rev ) {
                    // proceed only if $currentRev < $rev
                    if ( $upgrade->sortRevision($currentRev, $rev) < 0 ) {
                        
                        $phpFunctionName = 'upgrade_' . str_replace( '.', '_', $rev );
                        if ( is_callable(array('CRM_Upgrade_Page_Upgrade', "$phpFunctionName")) ) {
                            eval("\$this->{$phpFunctionName}('$rev');");
                        } else   {
                            $sqlFile   = implode( DIRECTORY_SEPARATOR, 
                                                  array(dirname(__FILE__), '..', 'Incremental', 
                                                        'sql', $rev . '.mysql') );
                            
                            if ( file_exists("$sqlFile.tpl") ) {
                                $tplFile = "$sqlFile.tpl";
                                
                                $config =& CRM_Core_Config::singleton();
                                $smarty = new Smarty;
                                $smarty->compile_dir = $config->templateCompileDir;
                                
                                $domain =& new CRM_Core_DAO_Domain();
                                $domain->find(true);
                                $multilingual = (bool) $domain->locales;
                                $locales      = explode(CRM_Core_DAO::VALUE_SEPARATOR, $domain->locales);
                                $smarty->assign('multilingual', $multilingual);
                                $smarty->assign('locales',      $locales);
                                
                                // we didn't call CRM_Core_BAO_Setting::retrieve(), so we need to set $dbLocale by hand
                                if ($multilingual) {
                                    global $dbLocale;
                                    $dbLocale = "_{$config->lcMessages}";
                                }
                                file_put_contents($sqlFile, $smarty->fetch($tplFile));
                            }
                            
                            if ( file_exists("$sqlFile") || file_exists("$sqlFile.tpl") ) {
                                $upgrade->source( $sqlFile );
                            }
                            
                            if ($multilingual) {
                                require_once 'CRM/Core/I18n/Schema.php';
                                CRM_Core_I18n_Schema::rebuildMultilingualSchema($locales);
                            }
                        }
                        $upgrade->setVersion( $rev );
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
            }
        }
        
        $template->assign( 'message', $message );
        $content = $template->fetch( 'CRM/common/success.tpl' );
        echo CRM_Utils_System::theme( 'page', $content, true, $this->_print );
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

}

