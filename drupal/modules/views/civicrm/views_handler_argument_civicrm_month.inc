<?php

/**
 * Argument handler for a month (MM)
 */
class views_handler_argument_civicrm_month extends views_handler_argument_date {
  /**
   * Constructor implementation
   */
  function construct() {
    parent::construct();
    $this->formula = views_date_sql_extract('MONTH', "***table***.$this->real_field","datetime",'0');
    $this->format = 'F';
    $this->arg_format = 'm';
  }

  /**
   * Provide a link to the next level of the view
   */
  function summary_name($data) {
    $month = str_pad($data->{$this->name_alias}, 2, '0', STR_PAD_LEFT);
    return format_date(strtotime("2005" . $month . "15"), 'custom', $this->format);
  }

  /**
   * Provide a link to the next level of the view
   */
  function title() {
    $month = str_pad($this->argument, 2, '0', STR_PAD_LEFT);
    return format_date(strtotime("2005" . $month . "15"), 'custom', $this->format, 0);
  }

  function summary_argument($data) {
    // Make sure the argument contains leading zeroes.
    return str_pad($data->{$this->base_alias}, 2, '0', STR_PAD_LEFT);
  }
}
