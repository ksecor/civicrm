<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_contribution_page.inc
 * Displays Contribution Page after grabbing the label from the database
 *
 */
class civicrm_handler_field_contribution_page extends views_handler_field {
    static $_contributionPage;

    function construct( ) {
        if ( ! self::$_contributionPage ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_contributionPage = CRM_Contribute_PseudoConstant::contributionPage( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_contributionPage[$values->{$this->field_alias}];
    }
}
?>