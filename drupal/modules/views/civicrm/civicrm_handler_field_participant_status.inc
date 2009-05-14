<?php

/**
 * Field handler to provide access control for the Participant Status (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_participant_status extends views_handler_field {
    static $_participant_status;

    function construct( ) {
        if ( ! self::$_participant_status ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            CRM_Core_PseudoConstant::populate(self::$_participant_status, 'CRM_Event_DAO_ParticipantStatusType', false, 'label');
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_participant_status[$values->{$this->field_alias}];
    }
}

