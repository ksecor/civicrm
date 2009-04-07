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
            require_once 'CRM/Core/PseudoConstant.php';
            CRM_Core_PseudoConstant::populate(self::$_participant_status, 'CRM_Event_DAO_ParticipantStatusType', false, 'label');
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
