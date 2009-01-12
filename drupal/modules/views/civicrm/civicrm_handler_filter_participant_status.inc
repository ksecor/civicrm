<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_participant_status.inc
 * Filters Participant Statuses, and is configured using the labels from the database
 *
 */


class civicrm_handler_filter_participant_status extends views_handler_filter_in_operator {
    static $_participant_status;

    function construct( ) {
        if ( ! self::$_participant_status ) {
            civicrm_initialize( );
            require_once 'CRM/Core/OptionGroup.php';
            self::$_participant_status = CRM_Core_OptionGroup::values( 'participant_status', false );;
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Participant Status');
            $options = array( );
            foreach (self::$_participant_status as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
