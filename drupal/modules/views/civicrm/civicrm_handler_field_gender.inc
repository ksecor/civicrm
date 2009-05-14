<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_gender.inc
 * Displays Gender after grabbing the label from the database
 *
 */
class civicrm_handler_field_gender extends views_handler_field {
    static $_gender;

    function construct( ) {
        if ( ! self::$_gender ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_gender = CRM_Core_PseudoConstant::gender( );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_gender[$values->{$this->field_alias}];
    }
}

