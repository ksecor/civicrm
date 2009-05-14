<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_activity_type.inc
 * Displays Activity Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_activity_type extends views_handler_field {
    static $_activityType;

    function construct( ) {
        if ( ! self::$_activityType ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityType = CRM_Core_PseudoConstant::activityType( );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_activityType[$values->{$this->field_alias}];
    }
}

