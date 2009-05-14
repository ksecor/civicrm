<?php

/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_contribution_page.inc
 * Filters Contribution Pages, and is configured using the labels from the database
 *
 */

class civicrm_handler_filter_contribution_page extends views_handler_filter_in_operator {
    static $_contributionPage;

    function construct( ) {
        if ( ! self::$_contributionPage ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_contributionPage = CRM_Contribute_PseudoConstant::contributionPage( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Contribution Page');
            $options = array( );
            foreach (self::$_contributionPage as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>