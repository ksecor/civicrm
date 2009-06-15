<?php
/**
 * Argument handler for a year plus month (CCYYMM)
 */
class views_handler_argument_civicrm_year_month extends views_handler_argument_date {
  /**
   * Constructor implementation
   */
  function construct() {
    parent::construct();
    $this->format = 'F Y';
    $this->arg_format = 'Ym';
    $this->formula = views_date_sql_format($this->arg_format, "***table***.$this->real_field","datetime",'0');
  }

  /**
   * Provide a link to the next level of the view
   */
  function summary_name($data) {
    $created = $data->{$this->name_alias};
    return format_date(strtotime($created . "15"), 'custom', $this->format, 0);
  }

  /**
   * Provide a link to the next level of the view
   */
  function title() {
    return format_date(strtotime($this->argument . "15"), 'custom', $this->format, 0);
  }
}
