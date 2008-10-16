<?php

class civicrm_handler_filter_activityType extends civicrm_handler_filter_option {
    function initialize( ) {
        if ( ! self::$_options ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_states = CRM_Core_PseudoConstant::activityType( );
        }
    }

    function title( ) {
        return t('State/Province');
    }
}
?>
