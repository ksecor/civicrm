<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contribution_type.inc
 * Filters Contribution Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_membership_status extends views_handler_filter_in_operator {
    static $_membershipStatus;

    function construct( ) {
        if ( ! self::$_membershipStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Member/PseudoConstant.php';
            self::$_membershipStatus = CRM_Member_PseudoConstant::membershipStatus( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Membership Type');
            $options = array( );
            foreach (self::$_membershipStatus as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>