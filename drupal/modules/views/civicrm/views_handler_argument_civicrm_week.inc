<?php


/**
 * Argument handler for a week.
 */
class views_handler_argument_civicrm_week extends views_handler_argument_date {
  /**
   * Constructor implementation
   */
  function construct() {
    parent::construct();
    $this->arg_format = 'w';
    $this->formula = views_date_sql_extract('WEEK', "***table***.$this->real_field","datetime",'0');
  }

  /**
   * Provide a link to the next level of the view
   */
  function summary_name($data) {
    $created = $data->{$this->name_alias};
    return t('Week @week', array('@week' => $created));
  }
}
