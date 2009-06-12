<?php
/**
 * Argument handler for a year (CCYY)
 */
class views_handler_argument_civicrm_year extends views_handler_argument_date {
  /**
   * Constructor implementation
   */
  function construct() {
    parent::construct();
    $this->arg_format = 'Y';
    $this->formula = views_date_sql_extract('YEAR', "***table***.$this->real_field","datetime",'0');
  }
}
