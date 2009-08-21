<?php

require_once 'CiviTestCase.php';
require_once 'Contact.php';

class BAO_Core_Address extends CiviTestCase 
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'Address BAOs',
                     'description' => 'Test all Core_BAO_Address methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
   
    function setUp( ) 
    {
        parent::setUp();
    }

    /**
     * create() method (create and update modes)
     */
    function testCreate( )
    {
        $contactId = Contact::createIndividual( );
        
        $params = array( );
        $params['address']['1'] = array (
                                         'street_address' => 'Oberoi Garden',
                                         'supplemental_address_1' => 'Attn: Accounting',
                                         'supplemental_address_2' => 'Powai',
                                         'city' => 'Athens',
                                         'postal_code' => '01903',
                                         'state_province_id'=> '1000',
                                         'country_id' => '1228',
                                         'geo_code_1' => '18.219023',
                                         'geo_code_2' => '-105.00973',
                                         'location_type_id' => '1',
                                         'is_primary' => '1',
                                         'is_billing' =>'0' 
                                         );
                                       
        $params['address']['contact_id'] = $contactId;
        
        
        $fixAddress = true;
        
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::create( $params, $fixAddress, $entity = null );
        $addressId = $this->assertDBNotNull( 'CRM_Core_DAO_Address', 'Oberoi Garden' , 'id', 'street_address',
                                           'Database check for created address.' );
        
         // Now call add() to modify an existing  address

        $params = array( );
        $params['address']['1'] = array (
                                         
                                         'id' => $addressId,
                                         'street_address' => 'Oberoi Garden',
                                         'supplemental_address_1' => 'A-wing:3037',
                                         'supplemental_address_2' => 'Bandra',
                                         'city' => 'Athens',
                                         'postal_code' => '01903',
                                         'state_province_id'=> '1000',
                                         'country_id' => '1228',
                                         'geo_code_1' => '18.219023',
                                         'geo_code_2' => '-105.00973',
                                         'location_type_id' => '1',
                                         'is_primary' => '1',
                                         'is_billing' => '0'
                                         );
        $params['address']['contact_id'] = $contactId;
        
        
        require_once 'CRM/Core/BAO/Address.php';
        $block = CRM_Core_BAO_Address::create( $params, $fixAddress, $entity = null );
        $addressId = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId, 'id', 'contact_id',
                                             'Database check for deleted address.' );
        Contact::delete( $contactId );
    }

    /**
     * Add() method ( )
     */
    function testAdd()
    { 
        $contactId = Contact::createIndividual( );
        
        $fixParams = array(
                           'street_address' => 'E 906N Pine Pl W',
                           'supplemental_address_1' => 'Editorial Dept',
                           'supplemental_address_2' => '',
                           'city' => 'El Paso',
                           'postal_code' => '88575',
                           'postal_code_suffix' => '',
                           'state_province_id'=> '1001',
                           'country_id' => '1228',
                           'geo_code_1' => '31.694842',
                           'geo_code_2' => '-106.29998',
                           'location_type_id' => '1',
                           'is_primary' => '1',
                           'is_billing' => '0',
                           'contact_id' => $contactId
                           ); 
        
        require_once 'CRM/Core/BAO/Address.php';
        $addAddress = CRM_Core_BAO_Address::add( $fixParams, $fixAddress = true ) ;
             
        $addParams = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId , 'id', 'contact_id',
                                             'Database check for created contact address.' );
        
        $this->assertEqual( $addAddress->street_address ,'E 906N Pine Pl W', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->supplemental_address_1 ,'Editorial Dept', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->city ,'El Paso', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->postal_code ,'88575', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->geo_code_1 ,'31.694842', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->geo_code_2 ,'-106.29998', 'Checking same for returned addresses.' );
        $this->assertEqual( $addAddress->country_id ,'1228', 'Checking same for returned addresses.' );          
        Contact::delete( $contactId );
    }    
    /**
     * AllAddress() method ( )
     */
    function testallAddress( )
    { 
        $contactId = Contact::createIndividual( );
        
        $fixParams = array(
                           'street_address' => 'E 906N Pine Pl W',
                           'supplemental_address_1' => 'Editorial Dept',
                           'supplemental_address_2' => '',
                           'city' => 'El Paso',
                           'postal_code' => '88575',
                           'postal_code_suffix' => '',
                           'state_province_id'=> '1001',
                           'country_id' => '1228',
                           'geo_code_1' => '31.694842',
                           'geo_code_2' => '-106.29998',
                           'location_type_id' => '1',
                           'is_primary' => '1',
                           'is_billing' => '0',
                           'contact_id' => $contactId
                           ); 
        
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::add( $fixParams, $fixAddress=true ) ;
        
        $addParams = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId , 'id', 'contact_id',
                                             'Database check for created contact address.' );
        $fixParams = array(
                           'street_address' => 'SW 719B Beech Dr NW',
                           'supplemental_address_1' => 'C/o OPDC',
                           'supplemental_address_2' => '',
                           'city' => 'Neillsville',
                           'postal_code' => '54456',
                           'postal_code_suffix' => '',
                           'state_province_id'=> '1001',
                           'country_id' => '1228',
                           'geo_code_1' => '44.553719',
                           'geo_code_2' => '-90.61457',
                           'location_type_id' => '2',
                           'is_primary' => '',
                           'is_billing' => '1',
                           'contact_id' => $contactId
                           );
        
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::add( $fixParams, $fixAddress=true ) ;
        
        $addParams = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId , 'id', 'contact_id',
                                             'Database check for created contact address.' );
        
        require_once 'CRM/Core/BAO/Address.php';
        $allAddress = CRM_Core_BAO_Address::allAddress( $contactId ) ;
               
        $this->assertEqual( count( $allAddress ) , 2, 'Checking number of returned addresses.' );
        
        Contact::delete( $contactId );
    }
    /**
     * AllAddress() method ( ) with null value
     */
    function testnullallAddress()
    { 
        $contactId = Contact::createIndividual( );
        
        $fixParams = array(
                           'street_address' => 'E 906N Pine Pl W',
                           'supplemental_address_1' => 'Editorial Dept',
                           'supplemental_address_2' => '',
                           'city' => 'El Paso',
                           'postal_code' => '88575',
                           'postal_code_suffix' => '',
                           'state_province_id'=> '1001',
                           'country_id' => '1228',
                           'geo_code_1' => '31.694842',
                           'geo_code_2' => '-106.29998',
                           'location_type_id' => '1',
                           'is_primary' => '1',
                           'is_billing' => '0',
                           'contact_id' => $contactId
                           );
        
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::add( $fixParams, $fixAddress=true ) ;
        
        $addParams = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId , 'id', 'contact_id',
                                             'Database check for created contact address.' );
        
        $contact_Id = null;
        
        require_once 'CRM/Core/BAO/Address.php';
        $allAddress = CRM_Core_BAO_Address::allAddress( $contact_Id ) ;
        
        $this->assertEqual( $allAddress  , null, 'Checking null for returned addresses.' );
        
        Contact::delete( $contactId );
    }
    

    /**
    * getValues() method (get Adress fields)
    */
    function testGetValues( )
    {
        $contactId = Contact::createIndividual( );
        
        $params = array( );
        $params['address']['1'] = array (
                                         'street_address' => 'Oberoi Garden',
                                         'supplemental_address_1' => 'Attn: Accounting',
                                         'supplemental_address_2' => 'Powai',
                                         'city' => 'Athens',
                                         'postal_code' => '01903',
                                         'state_province_id'=> '1000',
                                         'country_id' => '1228',
                                         'geo_code_1' => '18.219023',
                                         'geo_code_2' => '-105.00973',
                                         'location_type_id' => '1',
                                         'is_primary' => '1',
                                         'is_billing' =>'0' 
                                         );
        
        $params['address']['contact_id'] = $contactId;
        
        
        $fixAddress = true;
        
        require_once 'CRM/Core/BAO/Address.php';
        CRM_Core_BAO_Address::create( $params, $fixAddress, $entity = null );
        
        $addressId = $this->assertDBNotNull( 'CRM_Core_DAO_Address', $contactId, 'id', 'contact_id',
                                             'Database check for created address.' );
        
        $entityBlock = array( 'contact_id' => $contactId );
        $address =  CRM_Core_BAO_Address::getValues( $entityBlock );
        $this->assertEqual( $address[1]['id'], $addressId );
        $this->assertEqual( $address[1]['contact_id'], $contactId);
        $this->assertEqual( $address[1]['street_address'], 'Oberoi Garden');

        Contact::delete( $contactId );
    }
}
?>