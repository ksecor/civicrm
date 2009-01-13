<?php

/**
 * Field handler to provide simple renderer that allows linking to a user.
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field extends views_handler_field {
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
        $form['link_to_civicrm'] = array(
                                         '#title' => t('Link this field to its user'),
                                         '#type' => 'checkbox',
                                         '#default_value' => $this->options['link_to_civicrm'],
                                         );
    }

    function render_link($data, $values) {
        if (!empty($this->options['link_to_civicrm']) && user_access('access CiviCRM') && $values->id) {
            return civicrm_views_href( $data,
                                      'civicrm/contact/view',
                                      "reset=1&cid={$values->id}" );
        }
        else {
            return $data;
        }
    }

    function render($values) {
        return $this->render_link(check_plain($values->{$this->field_alias}), $values);
    }
}
