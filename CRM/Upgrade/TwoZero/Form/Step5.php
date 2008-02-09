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

class CRM_Upgrade_TwoZero_Form_Step5 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = 'pre-condition failed for upgrade step 5';
        
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'column_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'option_group_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'table_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'is_multiple' ) ) {
            return false;
        }

        // check FK constraint names are in valid format.
        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_contribution_page', 'payment_processor_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_uf_match', 'contact_id') ) {
            $errorMessage = ts('Database consistency check failed for step 5. FK constraint names not in the required format.');
            return false;
        }

        return $this->checkVersion( '1.94' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'others.mysql' ) );
        $this->source( $sqlFile );
        
        // update preferences table
        $pattern     = '/\{(\w{3,})\}/i';
        $replacement = '{contact.$1}';

        $domainID    = CRM_Core_Config::domainID( );

        $query    = "SELECT * FROM civicrm_preferences WHERE domain_id=$domainID";
        $res      = $this->runQuery( $query );
        if ($res->fetch()) {
            $address_format = preg_replace($pattern, $replacement, $res->address_format);
            $mailing_format = preg_replace($pattern, $replacement, $res->mailing_format);
            $individual_name_format = preg_replace($pattern, $replacement, $res->individual_name_format);
            
            $query = "
UPDATE civicrm_preferences 
SET address_format='$address_format', 
    mailing_format='$mailing_format',
    individual_name_format='$individual_name_format'
WHERE id={$res->id}
";
            $op    = $this->runQuery( $query );
            $op->free();
        }
        $res->free();

        // drop queries
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'drop.mysql' ) );
        $this->source( $sqlFile );
               
        $this->setVersion( 2.0 );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = 'post-condition failed for upgrade step 5';
        
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

        return $this->checkVersion( '2.0' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Five (Upgrade Miscellaneous Data)' );
    }

    function getTemplateMessage( ) {
        return ts( '<p>This step will upgrade the remaining data in your database.</p>' );
    }

    function getButtonTitle( ) {
        return ts( 'Finish Upgrade' );
    }

}


?>
