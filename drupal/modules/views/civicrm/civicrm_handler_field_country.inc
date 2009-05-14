<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_field_country.inc
 * Displays Country after grabbing the label from the database
 *
 */

class civicrm_handler_field_country extends views_handler_field {
    static $_countries;

    function construct( ) {
        if ( ! self::$_countries ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_countries = CRM_Core_PseudoConstant::countryIsoCode( );
        }
    }

    function render( $values ) {
        $sid = $values->{$this->field_alias};
        if ( empty( $sid ) ||
             (int ) $sid <= 0 ) {
            return null;
        }

        return self::$_countries[$values->{$this->field_alias}];
    }
}
?>
