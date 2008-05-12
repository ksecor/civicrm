<?php
class Contact extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a contact
     *
     * @return $contactID id of created contact
     */
    function create( $params ) {
        require_once "CRM/Contact/BAO/Contact.php";
        $contactID = CRM_Contact_BAO_Contact::createProfileContact( $params, CRM_Core_DAO::$_nullArray );
        return $contactID;
    }

    /*
     * Helper function to create
     * a contact of type Individual
     *
     * @return $contactID id of created Individual
     */
    function createIndividual( $params = null ) {
        //compose the params, when not passed
        if ( !$params ) {
            $first_name = 'John';
            $last_name  = 'Doe';
            $contact_source = 'Testing purpose';
            $params = array(
                            'first_name'     => $first_name,
                            'last_name'      => $last_name,
                            'contact_source' => $contact_source
                            );
        }
        return self::create($params);
    }

    /*
     * Helper function to create
     * a contact of type Household
     *
     * @return $contactID id of created Household
     */
    function createHousehold( $params = null) {
        //compose the params, when not passed
        if ( !$params ) {
            $household_name = "John Doe's home";
            $params = array( 'household_name'     => $household_name );
        }
        return self::create($params);
    }

    /*
     * Helper function to create
     * a contact of type Organisation
     *
     * @return $contactID id of created Organisation
     */
    function createOrganisation( $params = null ) {
        //compose the params, when not passed
        if ( !$params ) {
            $organization_name = "My Organization";
            $params = array( 'organization_name' => $organization_name );
        }
        return $this->create($params);
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