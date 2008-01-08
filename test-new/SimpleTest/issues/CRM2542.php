<?php

class TestOfCRM514 extends UnitTestCase 
{
    
    protected $contactAllParams = array(
                                        'nick_name' => 'This is nickname',
                                        'domain_id' => '1',
                                        'do_not_email' => '1',
                                        'do_not_phone' => '1',
                                        'do_not_mail' => '1',
                                        'contact_sub_type' => 'CertainSubType',
                                        'legal_identifier' => 'ABC23853ZZ2235',
                                        'external_identifier' => '1928837465',
                                        'home_URL' => 'http://some.url.com',
                                        'image_URL' => 'http://some.url.com/image.jpg',
                                        'preferred_communication_method' => 'Mail',
                                        'preferred_mail_format' => 'HTML',
                                        'do_not_trade' => '1',
                                        'is_opt_out' => '1',
                                        'contact_source' => 'Just some source.',
                                        'first_name' => 'Johny',
                                        'middle_name' => 'Lorenzo',
                                        'last_name' => 'TestSubject',
                                        'prefix' => 'Mr',
                                        'suffix' => 'VII',
                                        'greeting_type' => 'Informal',
                                        'custom_greeting' => 'Dear Pal',
                                        'job_title' => 'President',
                                        'gender' => 'Male',
                                        'birth_date' => '1977-03-12',
                                        'is_deceased' => '1',
                                        'deceased_date' => '2499-12-12',
                                        'email' => "johny@mail.com", 
                                        'contact_type' => 'Individual'
                                        );
    
    
    function setUp() 
    {        
    }
    
    function tearDown() 
    {        
    }
    
    function testCreateContact( ) 
    {
        foreach ( $this->contactAllParams as $name => $value ) {
            if ( $name == 'prefix' ) {
                $returnProperties['return.individual_prefix'] = 1;
            } elseif ( $name == 'suffix' ) {
                $returnProperties['return.individual_suffix'] = 1;
            } elseif ( $name == 'gender' ) {
                $returnProperties['return.gender'] = 1;
            } else {
                $returnProperties['return.'."{$name}"] = 1;
            }
        }
        
        require_once 'api/v2/Contact.php';
        $contact = &civicrm_contact_add( $this->contactAllParams );
        
        CRM_Core_Error::debug( "<b><i>Create :</i></b> ", $contact );
        
        $returnProperties['first_name'] = $this->contactAllParams['first_name'];
        $returnProperties['last_name']  = $this->contactAllParams['last_name'];
        
        $retrieved = &civicrm_contact_get( $returnProperties );
        
        CRM_Core_Error::debug( "<b><i>Get :</i></b> ", $retrieved );
                
        $delete = array( 'contact_id' => $contact['contact_id'] );
        
        civicrm_contact_delete( $delete );
    }
}
?>