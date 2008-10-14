<?php

/**
 * Field handler to provide acess control for the state field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_state extends civicrm_handler_field {
    static $_states;

    function construct( ) {
        if ( ! self::$_states ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_states = CRM_Core_PseudoConstant::stateProvince( );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_states[$values->{$this->field_alias}];
    }
}
?>