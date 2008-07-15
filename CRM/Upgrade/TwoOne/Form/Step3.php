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

class CRM_Upgrade_TwoOne_Form_Step3 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '3'));

        return $this->checkVersion( '2.02' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'misc.mysql' ) );
        $this->source( $sqlFile );
        
        $this->setVersion( '2.03' );
    }
    
    function verifyPostDBState( &$errorMessage ) {
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_activity' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_assignment') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_target') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_address') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_address') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_case') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_case_activity') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_component') ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_contribution_widget' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_grant' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_group_nesting' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_group_organization' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_loc_block' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_openid' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_openid_associations' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_openid_nonces' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_preferences_date' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_tell_friend' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_timezone' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_worldregion' )
             ) {
            // db is not 2.1
            $errorMessage .= ' Few 2.1 tables were found missing.';
            return false;
        }
           // check fields which MUST be present if a proper 2.1 db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'source_record_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'activity_date_time' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'status_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'first_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'last_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'gender_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'mail_to_household_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'user_unique_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'household_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'organization_name' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contribution', 'honor_type_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contribution_page', 'is_pay_later' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contribution_page', 'pay_later_text' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_country', 'region_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'column_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'option_group_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'table_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'is_multiple' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'loc_block_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email', 'location_type_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email', 'is_billing' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_entity_tag', 'contact_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_event', 'participant_listing_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_event', 'loc_block_id' ) ||
            
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_event_page', 'is_pay_later' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_event_page', 'pay_later_text' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_financial_trxn', 'contribution_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_im', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_im', 'location_type_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_membership_payment', 'contribution_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_membership_type', 'receipt_text_signup' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_membership_type', 'receipt_text_renewal' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'component_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_participant_payment', 'contribution_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_payment_processor', 'url_api' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_payment_processor_type', 'url_api_default' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_phone', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_phone', 'location_type_id' ) ||

             ! CRM_Core_DAO::checkFieldExists( 'civicrm_uf_match', 'uf_name' )
             ) {
            // db looks to have stuck somewhere between 2.0 & 2.1
            $errorMessage .= ' Few important fields were found missing in some of the tables.';
            return false;
        }

        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '1'));

        return $this->checkVersion( '2.03' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.1 Upgrade: Step Three (Miscellaneous)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'Step Three will upgrade rest of your database.') . '</p>';
    }
            
    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }
}

