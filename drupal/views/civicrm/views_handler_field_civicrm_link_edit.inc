<?php

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_link_edit extends views_handler_field_civicrm_link {
    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('edit');
        $id = $values->id;
        return civicrm_views_href( $text,
                                   'civicrm/contact/add',
                                   "reset=1&action=update&cid=$id" );
    }
}
?>