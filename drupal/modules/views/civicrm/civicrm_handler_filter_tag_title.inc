<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contribution_type.inc
 * Filters Contribution Types, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_tag_title extends views_handler_filter_in_operator {
    static $_tagName;

    function construct( ) {
        parent::construct();
        if ( ! self::$_tagName ) {
            civicrm_initialize( );
            require_once 'CRM/Member/PseudoConstant.php';
            self::$_tagName = CRM_Member_PseudoConstant::tag( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Tag Name');
            $options = array( );
            foreach (self::$_tagName as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>