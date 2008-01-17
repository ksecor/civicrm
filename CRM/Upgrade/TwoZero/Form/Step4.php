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

class CRM_Upgrade_TwoZero_Form_Step4 extends CRM_Upgrade_Form {

    function verifyPreDBState( ) {
    }

    function upgrade( ) {
        $query = "SHOW COLUMNS FROM civicrm_domain LIKE 'version'";
        $res   = $this->runQuery( $query );
        $row   = $res->fetchRow( DB_FETCHMODE_ASSOC );

        if (! isset($row['Field'])) {
            // Go to step1.
        } else {
            $domainID = CRM_Core_Config::domainID();
            $query    = "SELECT version FROM civicrm_domain WHERE id=$domainID";
            $res      = $this->runQuery( $query );
            $row      = $res->fetchRow( DB_FETCHMODE_ASSOC );
            
            if ((double)$row['version'] == 1.93) {
                $currentDir = dirname( __FILE__ );
                $sqlFile    = implode( DIRECTORY_SEPARATOR,
                                       array( $currentDir, '../sql', 'custom.mysql' ) );
                $this->source( $sqlFile );

                // data upgrade
                $query    = "UPDATE civicrm_custom_group cg1 
SET cg1.table_name = CONCAT( 'custom_value_', $domainID, '_', cg1.name )";
                $res      = $this->runQuery( $query );

                $query    = "UPDATE civicrm_custom_field cf1 SET cf1.column_name=cf1.name";
                $res      = $this->runQuery( $query );

                require_once 'CRM/Core/BAO/CustomGroup.php';
                require_once 'CRM/Core/BAO/CustomField.php';
                require_once 'CRM/Core/DAO/CustomOption.php';
                require_once 'CRM/Core/DAO/CustomValue.php';
                require_once 'CRM/Core/DAO/OptionGroup.php';
                require_once 'CRM/Core/DAO/OptionValue.php';
                
                $group =& new CRM_Core_DAO_CustomGroup();
                $group->find();
                
                while ($group->fetch()) {
                    CRM_Core_BAO_CustomGroup::createTable( $group );

                    $field =& new CRM_Core_DAO_CustomField();
                    $field->custom_group_id = $group->id;
                    $field->find();

                    while ($field->fetch()) {
                        CRM_Core_BAO_CustomField::createField( $field, 'add' );

                        $customOption =& new CRM_Core_DAO_CustomOption( );
                        $customOption->entity_table = 'civicrm_custom_field';
                        $customOption->entity_id    = $field->id;
                        $customOption->find();
                        
                        $hasFieldOptions = false;
                        while ($customOption->fetch()) {
                            if ( !$hasFieldOptions ) {
                                // make an entry in option_group
                                $optionGroup  =& new CRM_Core_DAO_OptionGroup( );
                                $optionGroup->domain_id =  CRM_Core_Config::domainID( );
                                $optionGroup->name      =  "{$field->column_name}_". date( 'YmdHis' );
                                $optionGroup->label     =  $field->label;
                                $optionGroup->is_active = 1;
                                $optionGroup->save( );
                                
                                // set custom_field's option_group_id
                                $field2 =& new CRM_Core_DAO_CustomField();
                                $field2->id = $field->id;
                                $field2->find(true);
                                $field2->option_group_id = $optionGroup->id;
                                $field2->save();
                                
                                $hasFieldOptions = true;
                            }

                            $optionValue =& new CRM_Core_DAO_OptionValue( );
                            $optionValue->option_group_id = $optionGroup->id;
                            $optionValue->label           = $customOption->label;
                            $optionValue->value           = $customOption->value;
                            $optionValue->weight          = $customOption->weight;
                            $optionValue->is_active       = $customOption->is_active;
                            $optionValue->save();
                        }
                    }
                }

                require_once 'CRM/Core/BAO/CustomValue.php';
                require_once 'CRM/Core/BAO/CustomValueTable.php';
                $customVal =& new CRM_Core_DAO_CustomValue();
                $customVal->find();

                while ($customVal->fetch()) {
                    $valParams = array();
                    $valParams[$customVal->custom_field_id]['custom_field_id'] = $customVal->custom_field_id;
                    $valParams[$customVal->custom_field_id]['file_id']         = $customVal->file_id;
                    list($valParams[$customVal->custom_field_id]['table_name'], 
                         $valParams[$customVal->custom_field_id]['column_name']) = 
                        CRM_Core_BAO_CustomField::getTableColumnName( $customVal->custom_field_id );

                    $field =& new CRM_Core_DAO_CustomField();
                    $field->id = $customVal->custom_field_id;
                    $field->find(true);
                    
                    $valCol = CRM_Core_BAO_CustomValue::typeToField($field->data_type);
                    $valParams[$customVal->custom_field_id]['type']        = $field->data_type;
                    $valParams[$customVal->custom_field_id]['value']       = $customVal->$valCol;
                    
                    $query    = "SELECT id FROM {$valParams[$customVal->custom_field_id]['table_name']} WHERE domain_id=$domainID and entity_id={$customVal->entity_id}";
                    $res      = $this->runQuery( $query );
                    $row      = $res->fetchRow( DB_FETCHMODE_ASSOC );
                    
                    if (isset($row['id'])) {
                        $valParams[$customVal->custom_field_id]['id'] = $row['id'];
                    }
                    CRM_Core_BAO_CustomValueTable::store( $valParams, $customVal->entity_table, $customVal->entity_id );
                }

                // migrate custom-option data
                foreach (array('civicrm_event_page', 'civicrm_contribution_page') as $entityTable) {
                    $customOption =& new CRM_Core_DAO_CustomOption( );
                    $customOption->entity_table = $entityTable;
                    $customOption->find();
                    
                    while ($customOption->fetch()) {
                        $optionGroup  =& new CRM_Core_DAO_OptionGroup( );
                        $optionGroup->domain_id =  CRM_Core_Config::domainID( );
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
                    }
                }

                // mark level
                $query = "UPDATE `civicrm_domain` SET version='1.94'";
                $res   = $this->runQuery( $query );
            } elseif ((double)$row['version'] > 1.93) {
                // This step already done. Move to next step.
            } else {
                // Move to previous step.
            }
        }
   }

    function verifyPostDBState( ) {
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Four (Custom Upgrade)' );
    }

    function getTemplateMessage( ) {
        return ts( 'This is a message' );
    }

    function getButtonTitle( ) {
        return ts( 'Proceed to Step Five' );
    }
}
?>
