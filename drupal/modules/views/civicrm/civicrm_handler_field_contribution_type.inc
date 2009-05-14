<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_contribution_type.inc
 * Displays Contribution Type after grabbing the label from the database
 *
 */
class civicrm_handler_field_contribution_type extends views_handler_field {
    static $_contributionType;

    function construct( ) {
        if ( ! self::$_contributionType ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_contributionType = CRM_Contribute_PseudoConstant::contributionType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_contributionType[$values->{$this->field_alias}];
    }
}
?>