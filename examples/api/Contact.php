<?php

function example_crm_get_contact() {
    $params = array (
                     'email'  => 'paula@foobar.org'
                     );
    $return_properties = array (
                                'contact_id',
                                'first_name',
                                'last_name',
                                'phone',
                                'postal_code',
                                'state_province'
                                );

    $myContact =& crm_get_contact( $params, $return_properties );
}

?>