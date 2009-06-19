<?php

/**
 * Field handler to provide acess control for the phone type field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_phone_type extends views_handler_field {
    static $_phoneTypes;

    function construct( ) {
        if ( ! self::$_phoneTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_phoneTypes = CRM_Core_PseudoConstant::phoneType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_phoneTypes[$values->{$this->field_alias}];
    }
}
?>
