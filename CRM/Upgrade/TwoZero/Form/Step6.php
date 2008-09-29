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
require_once 'CRM/Core/BAO/CustomOption.php';

class CRM_Upgrade_TwoZero_Form_Step6 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '6'));
        
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'column_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'option_group_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'table_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'is_multiple' ) ) {
            return false;
        }

        // check FK constraint names are in valid format.
        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_contribution_page', 'payment_processor_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_uf_match', 'contact_id') ) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '6')) . ' '. ts('FK constraint names not in the required format.') . ' ' . ts('Please rebuild your 1.9 database to ensure schema integrity.');
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

        //process to upgrade the price set fields in participant and
        //contribution tables and also upgrade the line items labels
        //Fix for CRM-3403
        $event = $priceField = $lineItemLables = $participant = null;
        $query = "SELECT civicrm_event.title as title FROM   civicrm_event";
        $event =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        $eventTitles = array();
        while ( $event->fetch( ) ) {
            $eventTitles[] = $event->title;
        }
        foreach ( $eventTitles as $level ) {
            $query1 = "SELECT civicrm_participant_payment.participant_id,civicrm_participant_payment.contribution_id FROM civicrm_participant, civicrm_participant_payment where civicrm_participant.event_level = %1 AND civicrm_participant_payment.participant_id = civicrm_participant.id";
            $params      = array( 1 => array( $level, 'String' ) );
            $participant =& CRM_Core_DAO::executeQuery( $query1, $params );
            while ( $participant->fetch( ) ) {
                $eventLevel = array();
                $query2     = "SELECT label, qty FROM civicrm_line_item WHERE entity_id = {$participant->contribution_id} ";
                $params     = array( 1 => array( $participant->contribution_id, 'Integer' ) );
                $priceField =& CRM_Core_DAO::executeQuery( $query2, $params );
                while ( $priceField->fetch( ) ) {
                    $lineItem = explode( ':',$priceField->label );
                    if ( ! CRM_Utils_Array::value( 1, $lineItem ) ) {
                        $lineItem[1] = $lineItem[0].' '.'-'.' '.$priceField->qty;
                    }
                    $eventLevel[] = trim($lineItem[1]);
                }

                $eventLevels = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
                    implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $eventLevel ) .
                    CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
                
                $query3 = "UPDATE `civicrm_participant` SET `event_level` = '{$eventLevels}' WHERE `civicrm_participant`.`id` = {$participant->participant_id } ";
                CRM_Core_DAO::executeQuery( $query3, CRM_Core_DAO::$_nullArray );

                $query4 = "UPDATE `civicrm_contribution` SET `amount_level` = '{$eventLevels}' WHERE `civicrm_contribution`.`id` = {$participant->contribution_id } ";
                CRM_Core_DAO::executeQuery( $query4, CRM_Core_DAO::$_nullArray );
            }          
        }
        //upgrade the line items labels
        $query5         = "SELECT label FROM civicrm_line_item WHERE qty = 1 GROUP BY label";
        $lineItemLables = CRM_Core_DAO::executeQuery( $query5, CRM_Core_DAO::$_nullArray );
        while ( $lineItemLables->fetch( ) ) {
            $lineItems   = $lineItemLables->label;
            $lineItem    = explode( ':',$lineItemLables->label );
            if ( CRM_Utils_Array::value( 1, $lineItem ) ) {
                $amountLevel = trim($lineItem[1]);
                $query6      = "UPDATE `civicrm_line_item` SET `label` = '{$amountLevel}' WHERE `civicrm_line_item`.`label` ='{$lineItems }' ";
                CRM_Core_DAO::executeQuery( $query6, CRM_Core_DAO::$_nullArray );
            }
        }
        //make object, free
        $event->free();

        if ( is_object( $priceField ) ) {
            $priceField->free();
        }
        if ( is_object( $participant ) ) {
            $participant->free();
        }

        $lineItemLables->free();
        
        // drop queries
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'drop.mysql' ) );
        $this->source( $sqlFile );
               
        $this->setVersion( '2.0' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '6'));
        
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
        return ts( 'CiviCRM 2.0 Upgrade: Step Six (Upgrade Miscellaneous Data)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'This step will upgrade the remaining data in your database.' ) . '</p>';
    }

    function getButtonTitle( ) {
        return ts( 'Finish Upgrade' );
    }

}


?>
