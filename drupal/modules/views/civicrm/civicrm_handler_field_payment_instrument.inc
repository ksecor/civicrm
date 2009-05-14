<?php

/**
 * Field handler to provide acess control for the location field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_payment_instrument extends views_handler_field {
    static $_paymentInstrument;

    function construct( ) {
        if ( ! self::$_paymentInstrument ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_paymentInstrument[$values->{$this->field_alias}];
    }
}
?>