<?php
/*
 * Heavily based on anonymous code contribution. No claim.
 * CiviCRM Views Integration
 *
 * civicrm_handler_filter_payment_instrument.inc
 * Filters Payment Instruments, and is configured using the labels from the database
 *
 */



class civicrm_handler_filter_payment_instrument extends views_handler_filter_in_operator {
    static $_paymentInstrument;

    function construct( ) {
        if ( ! self::$_paymentInstrument ) {
            civicrm_initialize( );
            require_once 'CRM/Contribute/PseudoConstant.php';
            self::$_paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Contribution Type');
            $options = array( );
            foreach (self::$_paymentInstrument as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>