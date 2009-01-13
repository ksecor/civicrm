<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contact_type.inc
 * Filters Contact Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_contact_type extends views_handler_filter_in_operator {
    static $_contactTypes;

    function construct( ) {
        if ( ! self::$_contactTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/SelectValues.php';
            self::$_contactTypes = CRM_Core_SelectValues::contactType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Contact type');
            $options = array( );
            foreach (self::$_contactTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }
}
?>