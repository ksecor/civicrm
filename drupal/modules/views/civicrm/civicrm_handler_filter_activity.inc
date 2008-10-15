<?php

class civicrm_handler_filter_activity extends views_handler_filter_in_operator {
    static $_activityTypes;

    function construct( ) {
        if ( ! self::$_activityTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_activityTypes = CRM_Core_PseudoConstant::activityType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Activity type');
            $options = array( );
            foreach (self::$_activityTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>