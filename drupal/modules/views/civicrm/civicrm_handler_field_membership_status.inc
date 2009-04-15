<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_contribution_type.inc
 * Displays Contribution Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_membership_status extends views_handler_field {
    static $_membershipStatus;

    function construct( ) {
        if ( ! self::$_membershipStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Member/PseudoConstant.php';
            self::$_membershipStatus = CRM_Member_PseudoConstant::membershipStatus( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_membershipStatus[$values->{$this->field_alias}];
    }
}
?>