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
        // just check the first 10 civicrm tables, rather than checking all 106!
        if ( CRM_Core_DAO::isDBMyISAM( 10 ) ) {
            $errorMessage = ts('Your database is configured to use the MyISAM database engine. CiviCRM  requires InnoDB. You will need to convert any MyISAM tables in your database to InnoDB before proceeding.');
            return false;
        }

        // check FK constraint names are in valid format.
        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'county_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'state_province_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'country_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_event',   'payment_processor_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_activity', 'source_contact_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_activity', 'parent_id')
            ) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '1')) . ' ' . ts('FK constraint names not in the required format.') . ' ' . ts('Please rebuild your 1.9 database to ensure schema integrity.');
            return false;
        }

        return true;
    }

    function upgrade( ) {
        // run cleanup sql script
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'cleanup.mysql' ) );
        $this->source( $sqlFile );

        // duplicate check for custom field names
        $query    = "SELECT count(*) AS number FROM civicrm_custom_field group by name having number > 1";
        $res      = $this->runQuery( $query );
        if ($res->fetch()) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '1')) . ' ' . ts("Duplicate entries found in %1 for the column '%2'.", array(1 => 'civicrm_custom_field', 2 => 'name'));
            CRM_Core_Error::fatal( $errorMessage );
        }

        // duplicate check for custom group names
        $query    = "SELECT count(*) AS number FROM civicrm_custom_group group by name having number > 1";
        $res      = $this->runQuery( $query );
        if ($res->fetch()) {
            $errorMessage = ts('Database consistency check failed for step %1.', array( 1 => '1' )) . ' ' . ts("Duplicate entries found in %1 for the column '%2'.", array(1 => 'civicrm_custom_group', 2 => 'name'));
            CRM_Core_Error::fatal( $errorMessage );
        }

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
        $msg = '<p><strong>' . ts('This process will upgrade your v1.9 CiviCRM database to the v2.0 database format.') . '</strong></p><div class="messsages status"><ul><li><strong>' . ts('Make sure you have a current and complete backup of your CiviCRM database and codebase files before starting the upgrade process.') . '</strong></li><li>' . ts('The upgrade process consists of 6 steps, and may take a while depending on the size of your database.') . '</li><li>' . ts('You must complete all six steps to have a valid 2.0 database.') . '</li></ul></div><p>' . ts('Step One will start with cleaning your database. Click <strong>Begin Upgrade</strong> to begin the process.') . '</p>';
        
        return $msg;
    }
            
    function getButtonTitle( ) {
        return ts( 'Begin Upgrade' );
    }
}

