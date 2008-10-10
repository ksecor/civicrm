<?php

class views_handler_filter_civicrm_location extends views_handler_filter_in_operator {
    static $_locationTypes;

    function construct( ) {
        if ( ! self::$_locationTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_locationTypes = CRM_Core_PseudoConstant::locationType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Location type');
            $options = array( );
            foreach (self::$_locationTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>