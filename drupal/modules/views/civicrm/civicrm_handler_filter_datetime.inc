<?php

/*
 * All the comparisons need to be done with SQL DATETIMES and not unixtime
 */
class civicrm_handler_filter_datetime extends views_handler_filter_date {

    /*
     * We use strtotime() to accept a wide range of date inputs and then
     * convert the unixtime back to SQL DATETIME before adding the WHERE clause
     */
    function op_simple($field) {
        $value = intval(strtotime($this->value['value'], 0));

        if (!empty($this->value['type']) && $this->value['type'] == 'offset') {
            $value = time() + sprintf('%+d', $value); // keep sign
        }

        $value = civicrm_format_date($value);

        $this->query->add_where($this->options['group'], "$field $this->operator '%s'", $value);
    }

    function op_between($field) {
        if ($this->operator == 'between') {
            $a = intval(strtotime($this->value['min'], 0));
            $b = intval(strtotime($this->value['max'], 0));
        } else {
            $a = intval(strtotime($this->value['max'], 0));
            $b = intval(strtotime($this->value['min'], 0));

            $this->query->set_where_group('OR', $this->options['group']);
        }

        if ($this->value['type'] == 'offset') {
            $now = time();
            $a = $now + sprintf('%+d', $a); // keep sign
            $b = $now + sprintf('%+d', $b); // keep sign
        }

        $a = $this->format_date($a);
        $b = $this->format_date($b);

        // %s is safe here because strtotime + format_date scrubbed the input 
        $this->query->add_where($this->options['group'], "$field >= '%s'", $a);
        $this->query->add_where($this->options['group'], "$field <= '%s'", $b);
    }

    function format_date($unixtime) {
        return date("Y-m-d H:i:s", $unixtime);
    }

}
?>
