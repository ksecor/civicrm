<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_event_type.inc
 * Display's Event Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_event_type extends views_handler_field {
    static $_event_type;

    function construct( ) {
        if ( ! self::$_event_type ) {
            civicrm_initialize( );
            require_once 'CRM/Core/OptionGroup.php';
            self::$_event_type = CRM_Core_OptionGroup::values( 'event_type', false );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }
        return self::$_event_type[$values->{$this->field_alias}];
    }
}

