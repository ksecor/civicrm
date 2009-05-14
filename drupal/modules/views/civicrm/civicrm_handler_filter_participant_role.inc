<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 *
 * civicrm_handler_filter_participant_role.inc
 * Filters Participant Role, and is configured using the labels from the database
 *
 */


class civicrm_handler_filter_participant_role extends views_handler_filter_in_operator {
    static $_participant_role;

    function construct( ) {
        if ( ! self::$_participant_role ) {
            civicrm_initialize( );
            require_once 'CRM/Core/OptionGroup.php';
            self::$_participant_role = CRM_Core_OptionGroup::values( 'participant_role', false );;
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Participant Status');
            $options = array( );
            foreach (self::$_participant_role as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
