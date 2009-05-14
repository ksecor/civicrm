<?php

class civicrm_handler_filter_option extends views_handler_filter_in_operator {
    static $_options;

    function get_value_options() {
        $this->initialize( );

        if (!isset($this->value_options)) {
            $this->value_title = $this->title( );
            $options = array( );
            foreach (self::$_options as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}
?>
