<?php

/**
 * Field handler for events to provide simple renderer that allows linking 
 * to the event page.
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_event extends views_handler_field {
    /**
     * Override init function to provide generic option to link to user.
     */
    function init(&$view, &$data) {
        parent::init($view, $data);
    }

    /**
     * Provide link to node option
     */
    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        $form['link_to_event'] = array(
                                         '#title' => t('Link this field to its event page'),
                                         '#type' => 'checkbox',
                                         '#default_value' => $this->options['link_to_event'],
                                         );
    }

    function render_link($data, $values) {
        if (!empty($this->options['link_to_event']) && user_access('access CiviEvent') && $values->id) {
            return civicrm_views_href( $data,
                                       'civicrm/event/info',
                                       "reset=1&id={$values->id}" );
        }
        else {
            return $data;
        }
    }

    function render($values) {
        return $this->render_link(check_plain($values->{$this->field_alias}), $values);
    }
}
