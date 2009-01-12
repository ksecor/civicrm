<?php
/*
 * Unchanged anonymous code contribution. No claim.
 *
 * civicrm_handler_field_email.inc
 * Displays Email and links to email address.
 *
 */
/**
 * Field handler to provide acess control for the email field
 *
 * @ingroup civicrm_field_handlers
 */
class civicrm_handler_field_email extends views_handler_field {
  function option_definition() {
    $options = parent::option_definition();
    $options['link_to_user'] = array('default' => 'mailto');
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['link_to_user'] = array(
      '#title' => t('Link this field'),
      '#type' => 'radios',
      '#options' => array(
        0 => t('No link'),
        'mailto' => t("With a mailto:"),
      ),
      '#default_value' => $this->options['link_to_user'],
    );
  }

  function render($values) {
    if ($this->options['link_to_user'] == 'mailto') {
      return l($values->{$this->field_alias}, "mailto:" . $values->{$this->field_alias});
    }
    return $this->render_link(check_plain($values->{$this->field_alias}), $values);
  }
}
?>