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

class CRM_Upgrade_TwoZero_Form_Step5 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '5'));
        
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_assignment' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_target' )   ) {
            return false;
        }

        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'source_record_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'due_date_time'    )) {
            return false;
        }
        
        $query    = "SELECT id FROM civicrm_custom_field WHERE name IS NULL";
        $res      = $this->runQuery( $query );
        if ($res->fetch()) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '1')) . ' ' . ts("Value missing in %1 for the column '%2'. Please add a unique value for the 'name' column for each database record.", array(1 => 'civicrm_custom_field', 2 => 'name'));
            return false;
        }
        $res->free();

        // check if any of the custom fields has reserved keyword as
        // custom field name.
        $reservedKeyWords = 
            implode( "', '", array( 'id', 'database', 'column', 'table', 'field', 'group' ) );
        $query    = "SELECT id FROM civicrm_custom_field WHERE LOWER(name) IN ('$reservedKeyWords')";
        $res      = $this->runQuery( $query );
        if ($res->fetch()) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '1')) . ' ' . ts("A custom field cannot have any of '%1' as the '%2'. Please rename the name value for these records to something that does not conflict with mysql reserved keywords.", array(1 => $reservedKeyWords, 2 => 'custom field name'));
            return false;
        }
        $res->free();

        return $this->checkVersion( '1.93' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'custom.mysql' ) );
        $this->source( $sqlFile );
        
        // data migration / upgrade
        $domainID = CRM_Core_Config::domainID( );
        $query    = "UPDATE civicrm_custom_group cg1 
SET cg1.table_name = CONCAT( 'civicrm_value_', $domainID, '_', LOWER(cg1.name) )";
        $res      = $this->runQuery( $query );
        $res->free();

        $query    = "UPDATE civicrm_custom_field cf1 SET cf1.column_name = LOWER(cf1.name)";
        $res      = $this->runQuery( $query );
        $res->free();

        CRM_Core_DAO::freeResult();

        require_once 'CRM/Core/BAO/CustomGroup.php';
        require_once 'CRM/Core/BAO/CustomField.php';
        require_once 'CRM/Core/DAO/OptionGroup.php';
        require_once 'CRM/Core/DAO/OptionValue.php';
        
        //1.Make new tables 2.Add columns 3.Add option-groups & values
        $group =& new CRM_Core_DAO_CustomGroup();
        $group->find();
        
        while ($group->fetch()) {
            CRM_Core_BAO_CustomGroup::createTable( $group );
            
            $field =& new CRM_Core_DAO_CustomField();
            $field->custom_group_id = $group->id;
            $field->find();
            
            while ($field->fetch()) {
                CRM_Core_BAO_CustomField::createField( $field, 'add' );
                
                $query        = "
SELECT * FROM civicrm_custom_option co 
WHERE co.entity_table='civicrm_custom_field' AND co.entity_id={$field->id}";
                $customOption = $this->runQuery( $query );
                
                $hasFieldOptions = false;
                $optionGroupID   = null;
                while ($customOption->fetch()) {
                    if ( !$hasFieldOptions ) {
                        // make an entry in option_group
                        $optionGroup  =& new CRM_Core_DAO_OptionGroup( );
                        $optionGroup->domain_id =  $domainID;
                        $optionGroup->name      =  "{$field->column_name}_". date( 'YmdHis' );
                        $optionGroup->label     =  $field->label;
                        $optionGroup->is_active = 1;
                        $optionGroup->save( );

                        $optionGroupID = $optionGroup->id;
                        $optionGroup->free();

                        // set custom_field's option_group_id
                        $field2 =& new CRM_Core_DAO_CustomField();
                        $field2->id = $field->id;
                        $field2->find(true);
                        $field2->option_group_id = $optionGroup->id;
                        $field2->save();

                        $field2->free();
                        $hasFieldOptions = true;
                    }
                    $optionValue =& new CRM_Core_DAO_OptionValue( );
                    $optionValue->option_group_id = $optionGroupID;
                    $optionValue->label           = $customOption->label;
                    $optionValue->value           = $customOption->value;
                    $optionValue->weight          = $customOption->weight;
                    $optionValue->is_active       = $customOption->is_active;
                    $optionValue->save();

                    $optionValue->free();
                }
                $customOption->free();
            }
            $field->free();
        }
        $group->free();
        CRM_Core_DAO::freeResult();
        
        // migrate custom values in newly created tables.
        require_once 'CRM/Core/BAO/CustomValue.php';
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $group =& new CRM_Core_DAO_CustomGroup();
        $group->find();
        
        while ($group->fetch()) {
            $field =& new CRM_Core_DAO_CustomField();
            $field->custom_group_id = $group->id;
            $field->find();
            
            while ($field->fetch()) {
                $col    = "cv." . CRM_Core_BAO_CustomValue::typeToField($field->data_type);
                
                if ($field->data_type != 'File') {
                    $query  = "
INSERT INTO {$group->table_name} (domain_id,entity_id,{$field->column_name})
SELECT $domainID, cv.entity_id, $col FROM civicrm_custom_value cv 
WHERE cv.custom_field_id={$field->id}
ON DUPLICATE KEY UPDATE {$field->column_name}={$col}";
                    $res    = $this->runQuery( $query );
                    $res->free();
                } else {
                    $query  = "
INSERT INTO {$group->table_name} (domain_id,entity_id,{$field->column_name})
SELECT $domainID, cv.entity_id, cf.id 
FROM civicrm_custom_value cv
LEFT JOIN  civicrm_file cf ON (cf.uri = $col)
WHERE cv.custom_field_id={$field->id}
ON DUPLICATE KEY UPDATE {$field->column_name} = cf.id";
                    $res    = $this->runQuery( $query );
                    $res->free();
                    
                    $query  = "
UPDATE civicrm_entity_file ef, {$group->table_name} ct
SET    ef.entity_table = '{$group->table_name}', ef.entity_id = ct.id
WHERE  ct.{$field->column_name}=ef.file_id AND ct.entity_id=ef.entity_id";
                    $res    = $this->runQuery( $query );
                    $res->free();
                }
            }
            $field->free();
        }
        $group->free();
        CRM_Core_DAO::freeResult();

        // migrate custom-option data
        foreach (array('civicrm_event_page', 'civicrm_contribution_page', 'civicrm_price_field') as $entityTable) {
            $query        = "
SELECT * FROM civicrm_custom_option co 
WHERE co.entity_table='$entityTable'";
            $customOption = $this->runQuery( $query );
            
            while ($customOption->fetch()) {
                $optionGroup  =& new CRM_Core_DAO_OptionGroup( );
                $optionGroup->domain_id =  $domainID;
                $optionGroup->name      =  "{$customOption->entity_table}.amount.". $customOption->entity_id;
                if (! $optionGroup->find(true)) {
                    $optionGroup->save( );
                }
                
                $optionValue =& new CRM_Core_DAO_OptionValue( );
                $optionValue->option_group_id = $optionGroup->id;
                $optionValue->label           = $customOption->label;
                $optionValue->value           = $customOption->value;
                $optionValue->weight          = $customOption->weight;
                $optionValue->is_active       = $customOption->is_active;
                $optionValue->save();

                $optionValue->free();
                $optionGroup->free();
            }
            $customOption->free();
        }
        CRM_Core_DAO::freeResult();
        
        $this->setVersion( '1.94' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '5'));
        
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'column_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'option_group_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'table_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_custom_group', 'is_multiple' ) ) {
            return false;
        }

        return $this->checkVersion( '1.94' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Five (Custom Data Upgrade)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'This step will upgrade the custom section of your database.' ) . '</p>';
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }
}

