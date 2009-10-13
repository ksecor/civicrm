<?php

class civicrm_handler_filter_mailing_name extends views_handler_filter_in_operator {
    static $_mailingNames;

    function construct( ) {
        parent::construct();
        if ( ! self::$_mailingNames ) {
            civicrm_initialize( );
            require_once 'CRM/Mailing/PseudoConstant.php';
            self::$_mailingNames = CRM_Mailing_PseudoConstant::completed( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Mailing Name');
            $options = array( );
            foreach (self::$_mailingNames as $id => $name ) {
                $options[$name] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>