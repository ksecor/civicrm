<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

        return true;
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'contact.mysql' ) );
        $this->source( $sqlFile );

        $this->setVersion( '1.91' );
    }
    
    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = 'post-condition failed for upgrade step 1';

        if (! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'first_name' ) ||
            ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'organization_name' ) ||
            ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'household_name' )) {
            return false;
        }

        return $this->checkVersion( '1.91' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step One (Contact Upgrade)' );
    }

    function getTemplateMessage( ) {
        return ts( '<p><strong>Note:</strong> Make sure you have taken the backup of your CiviCRM database and files before starting with the upgrade process.</p>
<p>This process will upgrade your <strong>v1.9 CiviCRM database to v2.0 database.</strong></p>
<p>This step will upgrade the contact section of your database.</p>'
);
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }

}


?>
