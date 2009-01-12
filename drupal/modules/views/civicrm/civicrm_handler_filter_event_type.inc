<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_event_type.inc
 * Filters Event Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_event_type extends views_handler_filter_in_operator {
    static $_event_type;

    function construct( ) {
        if ( ! self::$_event_type ) {
            civicrm_initialize( );
            require_once 'CRM/Core/OptionGroup.php';
            self::$_event_type = CRM_Core_OptionGroup::values( 'event_type', false );;
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Event Type');
            $options = array( );
            foreach (self::$_event_type as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
