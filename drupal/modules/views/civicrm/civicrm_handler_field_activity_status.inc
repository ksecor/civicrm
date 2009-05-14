<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_activity_status.inc
 * Displays Activity Status after grabbing the label from the database
 *
 */
class civicrm_handler_field_activity_status extends views_handler_field {
    static $_activityStatus;

    function construct( ) {
        if ( ! self::$_activityStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityStatus = CRM_Core_PseudoConstant::activityStatus( );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_activityStatus[$values->{$this->field_alias}];
    }
}

