<?php

/**
 * Field handler to present a link to user delete.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_link_delete extends views_handler_field_civicrm_link {
    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
        $id = $values->{$this->aliases['id']};
        return civicrm_views_href( $text,
                                   'civicrm/contact/delete',
                                   "reset=1&id=$id" );
    }
}
?>