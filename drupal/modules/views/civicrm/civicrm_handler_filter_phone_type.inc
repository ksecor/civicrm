<?php

class civicrm_handler_filter_phone_type extends views_handler_filter_in_operator {
    static $_phoneTypes;

    function construct( ) {
        if ( ! self::$_phoneTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_phoneTypes = CRM_Core_PseudoConstant::phoneType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Phone type');
            $options = array( );
            foreach (self::$_phoneTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>
