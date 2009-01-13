<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_contribution_status.inc
 * Displays Contribution Status after grabbing the label from the database
 *
 */
class civicrm_handler_field_contribution_status extends views_handler_field {
    static $_contributionStatus;

    function construct( ) {
        if ( ! self::$_contributionStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_contributionStatus[$values->{$this->field_alias}];
    }
}
?>