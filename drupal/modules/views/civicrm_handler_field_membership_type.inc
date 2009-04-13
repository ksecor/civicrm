<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_contribution_type.inc
 * Displays Contribution Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_membership_type extends views_handler_field {
    static $_membershipType;

    function construct( ) {
        if ( ! self::$_membershipType ) {
            civicrm_initialize( );
            require_once 'CRM/Member/PseudoConstant.php';
            self::$_membershipType = CRM_Member_PseudoConstant::membershipType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_membershipType[$values->{$this->field_alias}];
    }
}
?>