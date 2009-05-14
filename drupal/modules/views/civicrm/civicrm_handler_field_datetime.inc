<?php

/*
 * Unchanged anonymous code contribution. No claim.
 *
 * civicrm_handler_field_datetime.inc
 * Converts and displays text to something views friendly.
 *
 */

class civicrm_handler_field_datetime extends views_handler_field_date {

    /*
     * Convert the DATETIME from the database into unixtime then allow
     * views_handler_field_date to render as usual.
     * Also trick php into thinking the time is in the same timezone, no
     * matter the default timezone
     */
    function render($values) {
        // get default time zone form Drupal
        $timezone = variable_get('date_default_timezone', 0);
        $value = $values->{$this->field_alias};
        $date = new DateTime($value);
        $gmt = $date->getOffset();  //gives me the offset to GMT

        if($value) {
            $value = strtotime($value);
             //set the timestamp to GMT
            $value = $value + $gmt;
            //set the timestamp to site default
            $value = $value - $timezone;

            if($value) {
                $values->{$this->field_alias} = $value;
            }
        }

        return parent::render($values);
    }
}

?>
