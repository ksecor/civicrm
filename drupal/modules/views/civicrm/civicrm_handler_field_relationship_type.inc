<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_relationship_type.inc
 * Displays Relationship Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_relationship_type extends views_handler_field {
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

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_relationshipType[$values->{$this->field_alias}];
    }
}
?>