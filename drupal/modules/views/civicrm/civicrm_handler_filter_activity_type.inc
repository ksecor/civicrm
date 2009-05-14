<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_activity_type.inc
 * Filters Activity Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_activity_type extends views_handler_filter_in_operator {
    static $_activityType;

    function construct( ) {
        if ( ! self::$_activityType ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityType = CRM_Core_PseudoConstant::activityType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Activity Type');
            $options = array( );
            foreach (self::$_activityType as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
