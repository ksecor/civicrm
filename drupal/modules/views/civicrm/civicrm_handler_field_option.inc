<?php

/**
 * Field handler to provide acess control for an "pseudo constant / option value" pair
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_option extends civicrm_handler_field {
    static $_options;

    function render( $values ) {
        $this->initialize( );

        $id = $values->{$this->field_alias};
        if ( empty( $id ) ||
             (int ) $id <= 0 ) {
            return null;
        }

        return self::$_options[$values->{$this->field_alias}];
    }
}
?>
