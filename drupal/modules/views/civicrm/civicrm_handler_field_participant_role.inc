<?php

/**
 * Field handler to provide access control for the Participant Status (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_participant_role extends views_handler_field {
    static $_participant_role;

    function construct( ) {
        if ( ! self::$_participant_role ) {
            civicrm_initialize( );
            require_once 'CRM/Core/OptionGroup.php';
            self::$_participant_role = CRM_Core_OptionGroup::values( 'participant_role', false );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_participant_role[$values->{$this->field_alias}];
    }
}

