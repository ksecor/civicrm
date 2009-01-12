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
     */
    function render($values) {
        $value = $values->{$this->field_alias};

        if($value) {
            $value = strtotime($value);

            if($value) {
                $values->{$this->field_alias} = $value;
            }
        }

        return parent::render($values);
    }
}

?>
