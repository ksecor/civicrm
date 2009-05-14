<?php

/**
 * Field handler to provide acess control for the activity type field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_activity extends civicrm_handler_field {
    static $_activityTypes;

    function construct( ) {
        if ( ! self::$_activityTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityTypes = CRM_Core_PseudoConstant::activityType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_activityTypes[$values->{$this->field_alias}];
    }
}
?>