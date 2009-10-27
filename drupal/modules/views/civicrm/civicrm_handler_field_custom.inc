<?php

/**
 * Field handler to provide acess control for the activity type field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_custom extends views_handler_field {
    function construct( ) {
    }

    function render( $values ) {
        $value = $values->{$this->field_alias};

        if ( ! is_null( $value ) ) {
            // extract the field id from the name
            if ( preg_match('/_(\d+)$/', $this->real_field, $match ) ) {
                require_once 'CRM/Core/BAO/CustomOption.php';
                return CRM_Core_BAO_CustomOption::getOptionLabel($match[1], $value);
            }
        }
        return null;
    }
}
?>