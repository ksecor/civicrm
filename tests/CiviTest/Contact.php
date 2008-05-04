<?php

class Contact extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a contact
     *
     * @return $contactID id of created contact
     */
    function create( ) {
        $first_name = 'John';
        $last_name  = 'Doe';
        $contact_source = 'Testing purpose';
        $email = 'abc@def.com';
        $params = array(
                        'first_name'     => $first_name,
                        'last_name'      => $last_name,
                        'contact_source' => $contact_source,
                        'email-Primary'  => $email,
                        'email'          => $email   
                        );
        $contactID = CRM_Contact_BAO_Contact::createProfileContact( $params, CRM_Core_DAO::$_nullArray );
        return $contactID;
    }
    
    /*
     * Helper function to delete a contact
     * 
     * @param  int  $contactID   id of the contact to delete
     * @return boolean true if contact deleted, false otherwise
     * 
     */
    function delete( $contactID ) {
        return CRM_Contact_BAO_Contact::deleteContact( $contactID );
    }
}

?>