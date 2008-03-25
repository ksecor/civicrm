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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Upgrade/Form.php';

class CRM_Upgrade_TwoZero_Form_Step1 extends CRM_Upgrade_Form {
    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Database check failed - the current database is not v1.9.');

        // check if it's a 2.0 db
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_individual' ) && 
             ! CRM_Core_DAO::checkTableExists( 'civicrm_organization' ) && 
             CRM_Core_DAO::checkTableExists( 'civicrm_component' ) &&
             CRM_Core_DAO::checkTableExists('civicrm_contribution_widget') ) {
            $errorMessage = ts('Database check failed - it looks like you have already upgraded to the latest version of the database.');
            return false;
        }

        if ( CRM_Core_DAO::checkTableExists( 'civicrm_component' ) &&
             !CRM_Core_DAO::checkTableExists('civicrm_contribution_widget') ) {
            // Something wrong with the db
            $errorMessage = ts('Database consistency check failed.');
            return false;
        }

        // civicrm_mailing_spool is a pre 1.9 table. it should exist in all db's
        // we want to upgrade
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_mailing_spool' ) ) {
            return false;
        }

        // version is a 2.0 field, it should not exist in any db we want to upgrade
        if ( CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' ) ) {
            return false;
        }

        // show error if any of the tables, use 'MyISAM' storage engine. 
        $engines = CRM_Core_DAO::getStorageEngines( );
        if ( array_key_exists('MyISAM', $engines) ) {
            $errorMessage = ts('Your database is configured to use the MyISAM database engine. CiviCRM  requires InnoDB. You will need to convert any MyISAM tables in your database to InnoDB before proceeding.');
            return false;
        }

        return true;
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'cleanup.mysql' ) );
        $this->source( $sqlFile );

        $this->setVersion( '1.90' );
    }
    
    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '1'));
        
        if (! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' )) {
            return false;
        }

        return $this->checkVersion( '1.90' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step One (Database Cleanup)' );
    }

    function getTemplateMessage( ) {
        return '<p><strong>' . ts('This process will upgrade your v1.9 CiviCRM database to the v2.0 database format.') . '</strong></p><div class="messsages status"><ul><li><strong>' . ts('Make sure you have a current and complete backup of your CiviCRM database and codebase files before starting the upgrade process.') . '</strong></li><li>' . ts('The upgrade process consists of 6 steps, and may take a while depending on the size of your database.') . '</li><li>' . ts('You must complete all six steps to have a valid 2.0 database.') . '</li></ul></div><p>' . ts('Step One will start with cleaning your database. Click <strong>Begin Upgrade</strong> to begin the process.') . '</p>';
    }
            
    function getButtonTitle( ) {
        return ts( 'Begin Upgrade' );
    }

}



