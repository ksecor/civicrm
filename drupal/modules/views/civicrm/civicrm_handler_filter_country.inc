<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_country.inc
 * Filters Countries, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_country extends views_handler_filter_in_operator {
    static $_countries;

    function construct( ) {
        if ( ! self::$_countries ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_countries = CRM_Core_PseudoConstant::countryIsoCode( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Country');
            $options = array( );
            foreach (self::$_countries as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>
