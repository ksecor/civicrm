<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_relationship_type.inc
 * Filters Relationship Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_relationship_type extends views_handler_filter_in_operator {
    static $_relationshipType;

    function construct( ) {
        if ( ! self::$_relationshipType ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            $relationshipType_array = CRM_Core_PseudoConstant::relationshipType( );
			
			// relationshipType() returns information about relations as array with fields
			// 'name_a_b', 'name_b_a', 'contact_type_a' and 'contact_type_b'.
			// We keep just 'name_a_b' for simplicity.
			
			$options = array( );
            foreach ($relationshipType_array as $id => $value_array ) {
                $options[$id] = $value_array['name_a_b'];
            }
			
			self::$_relationshipType = $options;
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Relationship Type');
            $options = array( );
            foreach (self::$_relationshipType as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>