<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Core//OptionValue.php';

class CRM_Upgrade_ThreeZero_ThreeZero extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        
        $errorMessage = ts('Pre-condition failed for upgrade to 3.0.alpha1.');
        // check table, if the db is 3.0
        if ( CRM_Core_DAO::checkTableExists( 'civicrm_navigation' ) &&
             CRM_Core_DAO::checkTableExists( 'civicrm_participant_status_type' ) ) {
            $errorMessage =  ts('Database check failed - it looks like you have already upgraded to the latest version (v3.0.alpha1) of the database.');
            return false;
        } 
        // check table-column, if the db is 3.0 
        if ( CRM_Core_DAO::checkFieldExists( 'civicrm_menu',     'domain_id'   ) &&
             CRM_Core_DAO::checkFieldExists( 'civicrm_event',    'created_id'  ) &&
             CRM_Core_DAO::checkFieldExists( 'civicrm_event',    'is_template' ) &&
             CRM_Core_DAO::checkFieldExists( 'civicrm_uf_field', 'is_reserved' ) &&
             CRM_Core_DAO::checkFieldExists( 'civicrm_contact',  'email_greeting_id' )  &&
             CRM_Core_DAO::checkFieldExists( 'civicrm_payment_processor_type', 'payment_type' ) ) {
            
            $errorMessage =  ts('Database check failed - it looks like you have already upgraded to the latest version (v3.0.alpha1) of the database.');
            return false;
        } 
        
        //check previous version table e.g 2.2.*
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_cache' )        ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_pcp_block' )    ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_menu' )         ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_discount' )     ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_pcp' )          ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_pledge_block' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_contribution_soft' ) ) {
            
            $errorMessage .= ' Few important tables were found missing.';
            return false;
        }
        
        // check fields which MUST be present if a proper 2.2.* db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity',     'due_date_time' )    ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact',      'greeting_type_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contribution', 'check_number' ) ) {
            // db looks to have stuck somewhere between 2.1 & 2.2
            $errorMessage .= ' Few important fields were found missing in some of the tables.';
            return false;
        }

        return true;
    }
    
    function upgrade( $rev ) {

        //We execute some part of php after sql and then again sql
        //So using conditions for skipping some part of sql CRM-4575

        $template = & CRM_Core_Smarty::singleton( );
        $template->assign( 'skipGreetingTypePart', 1 );
        
        $upgrade =& new CRM_Upgrade_Form( );
        //Run the SQL file (1)
        $upgrade->processSQL( $rev );
        
        //delete unnecessary activities 
        $bulkEmailID = CRM_Core_OptionGroup::getValue('activity_type', 'Bulk Email', 'name' );
 
        if ( $bulkEmailID ) {

            $mailingActivityIds = array( );
            $query = " 
            SELECT max( ca.id ) as aid, 
                   ca.source_record_id sid
            FROM civicrm_activity ca
            WHERE ca.activity_type_id = %1 
            GROUP BY ca.source_record_id";
            
            $params = array( 1 => array(  $bulkEmailID, 'Integer' ) );
            $dao    = CRM_Core_DAO::executeQuery( $query, $params );

            while ( $dao->fetch( ) ) {
                $updateQuery = "
                UPDATE civicrm_activity_target cat, civicrm_activity ca 
                    SET cat.activity_id = {$dao->aid}  
                WHERE ca.source_record_id IS NOT NULL   AND
                      ca.activity_type_id = %1          AND 
                      ca.id <> {$dao->aid}              AND 
                      ca.source_record_id = {$dao->sid} AND 
                      ca.id = cat.activity_id";
                
                $updateParams = array( 1 => array(  $bulkEmailID, 'Integer' ) );    
                CRM_Core_DAO::executeQuery( $updateQuery,  $updateParams );
                
                $deleteQuery = " 
                DELETE ca.* 
                FROM civicrm_activity ca 
                WHERE ca.source_record_id IS NOT NULL  AND 
                      ca.activity_type_id = %1         AND 
                      ca.id <> {$dao->aid}             AND 
                      ca.source_record_id = {$dao->sid}";
                
                $deleteParams = array( 1 => array(  $bulkEmailID, 'Integer' ) );    
                CRM_Core_DAO::executeQuery( $deleteQuery,  $deleteParams );
            }
        }
        
        //CRM-4453
        //lets insert column in civicrm_aprticipant table
        $query  = "
        ALTER TABLE `civicrm_participant` 
            ADD `fee_currency` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '3 character string, value derived from config setting.' AFTER `discount_id`";
        CRM_Core_DAO::executeQuery( $query );
        
        //get currency from contribution table if exists/default
        //insert currency when fee_amount != NULL or event is paid.
        $query = "
        SELECT  civicrm_participant.id 
        FROM    civicrm_participant
            LEFT JOIN  civicrm_event 
                   ON ( civicrm_participant.event_id = civicrm_event.id )
        WHERE  civicrm_participant.fee_amount IS NOT NULL OR 
               civicrm_event.is_monetary = 1";
        
        $participant = CRM_Core_DAO::executeQuery( $query );
        while ( $participant->fetch( ) ) {
            $query = "
            SELECT civicrm_contribution.currency 
            FROM   civicrm_contribution, 
                   civicrm_participant_payment
            WHERE  civicrm_contribution.id = civicrm_participant_payment.contribution_id AND  
                   civicrm_participant_payment.participant_id = {$participant->id}";

            $currencyID = CRM_Core_DAO::singleValueQuery( $query );
            if ( !$currencyID ) {
                $config     =& CRM_Core_Config::singleton( ); 
                $currencyID = $config->defaultCurrency;
            }
            
            //finally update participant record.
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Participant', $participant->id, 'fee_currency', $currencyID );
        }
        
        //CRM-4575
        //check whether {contact.name} is set in mailing labels
        require_once 'CRM/Core/BAO/Preferences.php';
        $mailingFormat    = CRM_Core_BAO_Preferences::value( 'mailing_format' );
        $defaultAddressee = CRM_Core_OptionGroup::values( 'addressee', false, false, false, 
                                                          " AND v.filter = 1 AND v.is_default =  1", 'label' );
        if ( strpos($mailingFormat,'{contact.contact_name}') === false ) {
            $commonAddressee = true;
        } else {
            //else compare individual name format with default individual addressee.
            $individualNameFormat = CRM_Core_BAO_Preferences::value( 'individual_name_format' );
            
            if ( array_search($individualNameFormat, $defaultAddressee) !== false ) {
                 $commonAddressee = true;
            } else {
                //otherwise insert new token in addressee and set as a default
                $addresseeGroupId  = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup',
                                                                  'addressee',
                                                                  'id',
                                                                  'name' );
                
                $optionValueParams = array( 'label'          => $individualNameFormat,
                                            'is_active'      => 1, 
                                            'contactOptions' => 1,
                                            'filter'         => 1,
                                            'defaultGreeting'=> 1,
                                            'is_default'     => 1
                                            );
                
                $action               = CRM_Core_Action::ADD;
                $addresseeGroupParams = array( 'name' => 'addressee' );
                $fieldValues          = array( 'option_group_id' => $addresseeGroupId );
                $weight               = CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_OptionValue', $fieldValues);

                $optionValueParams['weight'] = $weight;
                $addresseeTokne           = CRM_Core_OptionValue::addOptionValue( $optionValueParams, $addresseeGroupParams, 
                                                                                     $action, $optionId = null );
                $commonAddressee = false;
            } 
        }
        
        if ( !$commonAddressee ) {
            $template->assign('addresseeTokenValue', $addresseeTokne->value );    
        } else {
            $template->assign('defaultAddresseeTokenValue', key($defaultAddressee) );     
        }
        
        //Run the SQL file (2)
        $template->assign( 'skipGreetingTypePart', 0 );
        $upgrade->processSQL( $rev );

    }
}
