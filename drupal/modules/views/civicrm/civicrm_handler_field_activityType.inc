<?php

/**
 * Field handler to provide acess control for the state field (which is a lookup)
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_activityType extends civicrm_handler_field_option {
    function initialize( ) {
        if ( ! self::$_options ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_options = CRM_Core_PseudoConstant::activityType( );
        }
    }
}
?>
