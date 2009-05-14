<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_activity_status.inc
 * Filters Activity Statuses, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_activity_status extends views_handler_filter_in_operator {
    static $_activityStatus;

    function construct( ) {
        if ( ! self::$_activityStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityStatus = CRM_Core_PseudoConstant::activityStatus( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Activity Status');
            $options = array( );
            foreach (self::$_activityStatus as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
