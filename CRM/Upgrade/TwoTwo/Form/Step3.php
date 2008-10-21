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

class CRM_Upgrade_TwoTwo_Form_Step3 extends CRM_Upgrade_Form {
    
    function verifyPreDBState( &$errorMessage ) 
    {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '3'));
        return $this->checkVersion( '2.12' );
    }
    
    function upgrade( ) 
    {
        //1. Add option group "from_email_address" and its option values
        // fix for CRM-3552
        
        $query = "
SELECT id 
  FROM civicrm_option_group 
 WHERE name = 'from_Email_address'";
        
        $fmaGroup = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        $fmaGroupId = null;
        if ( $fmaGroup->fetch( ) ) {
            $fmaGroupId = $fmaGroup->id;
        } else {
            //insert 'from_mailing_address'
            $query = "
INSERT INTO civicrm_option_group ( name, description, is_reserved, is_active )
VALUES ('from_email_address', 'From Email Address', 0, 1)";
            
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
            //get the group id.
            $query = "
SELECT id
  FROM civicrm_option_group 
 WHERE name = 'from_email_address'";
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            if ( $dao->fetch( ) ) {
                $fmaGroupId = $dao->id;
            }
        }
        
        if ( $fmaGroupId ) {
            //get domain from email address and name as default value.
            require_once 'CRM/Core/BAO/Domain.php';
            $domain =& CRM_Core_BAO_Domain::getDomain( );
            $domain->selectAdd( );
            $domain->selectAdd( 'email_name', 'email_address' );
            $domain->find(true);
            
            $formEmailAddress = '"' . $domain->email_name . '"<' . $domain->email_address . '>';
            
            $query ="
SELECT   max(ROUND(civicrm_option_value.value)) as maxVal, 
         max(civicrm_option_value.weight) as maxWt
    FROM civicrm_option_value, civicrm_option_group
   WHERE civicrm_option_group.name = 'from_Email_address'
     AND civicrm_option_value.option_group_id = civicrm_option_group.id
GROUP BY civicrm_option_group.id";
            
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
            $maxVal = $maxWt = 0;
            if ( $dao->fetch( ) ) {
                $maxWt  = $dao->maxWt;
                $maxVal = $dao->maxVal;
            }
            
            $maxWt  += 1;
            $maxVal += 1;
            
            //insert domain from email address and name.
            $query ="
INSERT INTO  `civicrm_option_value` 
             (`option_group_id`, `label`, `value`, `name` , `grouping`, `filter`, `is_default`, 
              `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`) 
     VALUES  ( %1, %2, %3, %2, NULL, 0, 1, %4, 'Default domain email address and from name.', 0, 0, 1, NULL)";
            
            $params = array( 1 => array( $fmaGroupId,       'Integer' ),
                             2 => array( $formEmailAddress, 'String'  ),
                             3 => array( $maxVal,           'Integer' ),
                             4 => array( $maxWt,            'Integer' ) );
            $dao    = CRM_Core_DAO::executeQuery( $query, $params );
            
            //drop civicrm_domain.email_name and
            //civicrm_domain.email_address.
            $query = " 
ALTER TABLE `civicrm_domain`
       DROP `email_name`,
       DROP `email_address`";
            $dao   = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        }
        
        $this->setVersion( '2.13' );
    }
    
    function verifyPostDBState( &$errorMessage ) {
        // check if Option Group & Option Values tables exists
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_option_group' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_option_value') ){
            $errorMessage .= '  option group or option value table is missing.';
            return false;
        }
        // check fields which MUST be present civicrm_option_group & civicrm_option_value
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'label' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'description' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'is_reserved' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_group', 'is_active' ) ){
            $errorMessage .= ' Few important fields were found missing in civicrm_option_group table.';
            return false;
        }
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'option_group_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'label' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'description' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'component_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_option_value', 'is_active' ) ){
            $errorMessage .= ' Few important fields were found missing in civicrm_option_value table.';
            return false;
        }
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '2'));
        
        return $this->checkVersion( '2.13' );
    }
    
    function getTitle( ) {
        return ts( 'CiviCRM 2.2 Upgrade: Step Three (Option Group And Values)' );
    }
    
    function getTemplateMessage( ) {
        return '<p>' . ts( 'Step Three will upgrade the Option Group And Values in your database.') . '</p>';
    }
    
    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }
}

