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

class CRM_Upgrade_TwoZero_Form_Step3 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = 'pre-condition failed for upgrade step 3';

        // ensure that version field exists in db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' ) ) {
            return false;
        }

        // also ensure first_name, household_name and contact_name exist in db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'first_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'household_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'organization_name' ) ) {
            return false;
        }

        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'county_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'state_province_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'country_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_event',   'payment_processor_id') ) {
            $errorMessage = ts('Database consistency check failed for step 3. FK constraint names not in the required format.');
            return false;
        }

        return $this->checkVersion( '1.91' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'location.mysql' ) );
        $this->source( $sqlFile );
        
        $this->setVersion( '1.92' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = 'post-condition failed for upgrade step 3';

        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_loc_block' ) ) {
            return false;
        }
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_address', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_phone',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_im', 'contact_id' )    ) {
            return false;
        }
        
        return $this->checkVersion( '1.92' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Three (Location Data Upgrade)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'This step will upgrade the location data in your database.' ) . '</p>';
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }

}



