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

class CRM_Upgrade_TwoZero_Form_Step7 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '7'));
        
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_case' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_case_activity' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_component' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_contribution_widget' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_grant' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_group_organization' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_openid' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_preferences_date' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_tell_friend' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_timezone' ) ) {
            return false;
        }

        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_contribution_page', 'is_pay_later' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_financial_trxn', 'contribution_id' ) ) {
            return false;
        }

        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'column_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'option_group_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'table_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'is_multiple' ) ) {
            return false;
        }

        $query = "SELECT id FROM civicrm_payment_processor WHERE payment_processor_type IN ('PayPal', 'PayPal_Express')";
        $res   = $this->runQuery( $query );
        if ( $res->fetch() ) {
            CRM_Core_Session::setStatus( ts('%1: PayPal Payment Processor found. You will need to follow the instructions for %2', 
                                            array( 1 => "<strong>WARNING</strong>", 
                                                   2 => "<a href='http://wiki.civicrm.org/confluence/display/CRMDOC/Upgrade+Drupal+Sites+to+2.0#UpgradeDrupalSitesto2.0-12.UpdatePayPalProcessorSettings'>Updating PayPal Processor Settings.</a>"
                                                   )) );
            $res->free();
        }
        
        return $this->checkVersion( '2.0' );
    }
    
    function buildQuickForm( ) {
    }

    function getTitle( ) {
        return ts( 'Database Upgrade to v2.0 Completed' );
    }

    function getTemplateMessage( ) {
        if ( $this->_config->userFramework == 'Drupal' ) {
            $upgradeDoc = 'http://wiki.civicrm.org/confluence/x/7IFH';
        } else {
            $upgradeDoc = 'http://wiki.civicrm.org/confluence/x/SoJH';
        }
        return '<p><strong>' . ts('Your CiviCRM database has been successfully upgraded to v2.0.') . '</strong></p><p>' . ts('Please be sure to follow the remaining steps in the <a href=\'%1\' target=\'_blank\'><strong>Upgrade Instructions</strong></a>.', array( 1 => $upgradeDoc )) . '</p><p>' . ts('Thank you for using CiviCRM.') . '</p>';
    }
}

