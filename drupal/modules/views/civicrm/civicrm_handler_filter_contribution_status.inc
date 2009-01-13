<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contribution_status.inc
 * Filters Contribution Statuses, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_contribution_status extends views_handler_filter_in_operator {
    static $_contributionStatus;

    function construct( ) {
        if ( ! self::$_contributionStatus ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Contribution Status');
            $options = array( );
            foreach (self::$_contributionStatus as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>