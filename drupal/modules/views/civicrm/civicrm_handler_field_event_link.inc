<?php

/**
 * Field handler to present a link to the user.
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_event_link extends views_handler_field {

    function option_definition() {
        $options = parent::option_definition();
        unset($options['label']);
        $options['text'] = array('default' => '', 'translatable' => TRUE);
        return $options;
    }

    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        unset($form['label']);
        $form['text'] = array(
                              '#type' => 'textfield',
                              '#title' => t('Text to display'),
                              '#default_value' => $this->options['text'],
                              );
    }

    function label() {

    }


    // An example of field level access control.
    function access() {
        return user_access('access CiviEvent');
    }


    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
        $id = $values->{'id'};

        $url = civicrm_views_href($text, "civicrm/event/info", "reset=1&id=$id");

        return $url;
    }
}
?>
