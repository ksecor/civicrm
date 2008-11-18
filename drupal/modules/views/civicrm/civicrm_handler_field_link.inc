<?php

/**
 * Field handler to present a link to the user.
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_link extends civicrm_handler_field {
    function construct() {
        parent::construct();
        $this->additional_fields['id'] = 'id';
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['text'] = array('default' => '', 'translatable' => TRUE);
        return $options;
    }

    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        $form['text'] = array(
                              '#type' => 'textfield',
                              '#title' => t('Text to display'),
                              '#default_value' => $this->options['text'],
                              );
    }

    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function query() {
        $this->ensure_my_table();
        $this->add_additional_fields();
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
        $id = $values->{$this->aliases['id']};
        return civicrm_views_href($text, "civicrm/contact/view", "reset=1&id=$id");
    }
}
?>