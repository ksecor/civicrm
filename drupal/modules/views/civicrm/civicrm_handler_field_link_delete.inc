<?php

/**
 * Field handler to present a link to user delete.
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_link_delete extends civicrm_handler_field_link {
    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
        $id = $values->{$this->aliases['id']};
        return civicrm_href( $text,
                                   'civicrm/contact/delete',
                                   "reset=1&id=$id" );
    }
}
?>