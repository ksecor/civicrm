<?php

/**
 * Field handler to provide acess control for the location field (which is a lookup)
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_location extends views_handler_field_civicrm {
    static $_locationTypes;

    function construct( ) {
        if ( ! self::$_locationTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_locationTypes = CRM_Core_PseudoConstant::locationType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_locationTypes[$values->{$this->field_alias}];
    }
}
?>