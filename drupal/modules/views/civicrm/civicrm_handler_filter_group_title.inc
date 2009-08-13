<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contribution_type.inc
 * Filters Contribution Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_group_name extends views_handler_filter_in_operator {
    static $_groupName;

    function construct( ) {
        parent::construct();
        if ( ! self::$_groupName ) {
            civicrm_initialize( );
            require_once 'CRM/Member/PseudoConstant.php';
            self::$_groupName = CRM_Member_PseudoConstant::allGroup( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Group Name');
            $options = array( );
            foreach (self::$_groupName as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>