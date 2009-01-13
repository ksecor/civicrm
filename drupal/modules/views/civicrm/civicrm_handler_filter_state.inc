<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_state.inc
 * Filters States, and is configured using the labels from the database
 *
 */


class civicrm_handler_filter_state extends views_handler_filter_in_operator {
    static $_states;

    function construct( ) {
        if ( ! self::$_states ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_states = CRM_Core_PseudoConstant::stateProvinceAbbreviation( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('State province');
            $options = array( );
            foreach (self::$_states as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>
