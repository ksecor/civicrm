<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_gender.inc
 * Filters Genders, and is configured using the labels from the database
 *
 */



class civicrm_handler_filter_gender extends views_handler_filter_in_operator {
    static $_gender;

    function construct( ) {
        if ( ! self::$_gender ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_gender = CRM_Core_PseudoConstant::gender( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Gender');
            $options = array( );
            foreach (self::$_gender as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
