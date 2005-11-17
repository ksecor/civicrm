<?php
require_once "CommonAPI.php";

class TestOfNewOrganizationForm extends WebTestCase 
{
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testNewOrganization()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('New Organization')) {
            $this->clickLink('New Organization');
        }
        
        $this->assertResponse(200);
        
        // Household Block.
        $this->assertWantedText("Organization");
        $organization_name = 'AAB Pvt. Ltd.';
        $nick_name         = 'AABs';
        $legal_name        = 'AABs Companies';
        $home_url          = 'www.aab.com';
        $this->setFieldById('organization_name', $organization_name);
        $this->setFieldbyId('legal_name',        $legal_name       );
        $this->setFieldbyId('home_url',          $home_url         );
        $this->setFieldbyId('nick_name',         $nick_name        );
        
        // Communication Preferances Block.
        $this->assertWantedText("Communication Preferences");
        $preffered_communication = 'Phone';
        $this->setFieldbyId('preferred_communication_method', $preffered_communication);
        
        // Location Block.
        $this->assertWantedText("Location");
        $location_type1 = 'Work';
        $location_type2 = 'Main';
        $is_primary     = 1;
        $phone_type11   = 'Mobile';
        $phone_type12   = 'Phone';
        $phone_type21   = 'Fax';
        $phone_type22   = 'Mobile';
        $phone_type23   = 'Phone';
        $phone11        = '23423536';
        $phone12        = '435758';
        $phone21        = '977553224';
        $phone22        = '09803134';
        $phone23        = '2497072';
        $email11        = 'email11@yahoo.com';
        $email12        = 'email12@yahoo.com';
        $email13        = 'email13@yahoo.com';
        $email21        = 'email21@yahoo.com';
        $im_provider11  = 'AIM';
        $im_provider21  = 'Yahoo';
        $im_provider22  = 'AIM';
        $im_provider23  = 'MSN';
        $im_name11      = 'Hi';
        $im_name21      = 'Hello';
        $im_name22      = 'Hola';
        $im_name23      = 'How Goes ??';
        $this->setFieldbyId('location[1][location_type_id]',     $location_type1);
        $this->setFieldbyId('location[2][location_type_id]',     $location_type2);
        $this->setField('location[1][is_primary]',               $is_primary    );
        $this->setFieldbyId('location[1][phone][1][phone_type]', $phone_type11  );
        $this->setFieldbyId('location[1][phone][2][phone_type]', $phone_type12  );
        $this->setFieldbyId('location[2][phone][1][phone_type]', $phone_type21  );
        $this->setFieldbyId('location[2][phone][2][phone_type]', $phone_type22  );
        $this->setFieldbyId('location[2][phone][3][phone_type]', $phone_type23  );
        $this->setFieldbyId('location[1][phone][1][phone]',      $phone11       );
        $this->setFieldbyId('location[1][phone][2][phone]',      $phone12       );
        $this->setFieldbyId('location[2][phone][1][phone]',      $phone21       );
        $this->setFieldbyId('location[2][phone][2][phone]',      $phone22       );
        $this->setFieldbyId('location[2][phone][3][phone]',      $phone23       );
        $this->setFieldbyId('location[1][email][1][email]',      $email11       );
        $this->setFieldbyId('location[1][email][2][email]',      $email12       );
        $this->setFieldbyId('location[1][email][3][email]',      $email13       );
        $this->setFieldbyId('location[2][email][1][email]',      $email21       );
        $this->setFieldbyId('location[1][im][1][provider_id]',   $im_provider11 );
        $this->setFieldbyId('location[2][im][1][provider_id]',   $im_provider21 );
        $this->setFieldbyId('location[2][im][2][provider_id]',   $im_provider22 );
        $this->setFieldbyId('location[2][im][3][provider_id]',   $im_provider23 );
        $this->setFieldbyId('location[1][im][1][name]',          $im_name11     );
        $this->setFieldbyId('location[2][im][1][name]',          $im_name21     );
        $this->setFieldbyId('location[2][im][2][name]',          $im_name22     );
        $this->setFieldbyId('location[2][im][3][name]',          $im_name23     );
        
        // Address Sub Block.
        $this->assertWantedText("Address");
        $street_address1         = 'Mumbai 1';
        $street_address2         = 'Pune 2';
        $supplemental_address_12 = 'Mumbai..Supplemetal Address 12';
        $supplemental_address_21 = 'Pune..Supplemetal Address 21';
        $this->setFieldbyId('location[1][address][street_address]',         $street_address1        );
        $this->setFieldbyId('location[2][address][street_address]',         $street_address2        );
        $this->setFieldbyId('location[1][address][supplemental_address_2]', $supplemental_address_12);
        $this->setFieldbyId('location[2][address][supplemental_address_1]', $supplemental_address_21);
        
        // Notes Block.
        $this->assertWantedText("Contact Notes");
        $note = 'Note for AAB Companies. We beleve in ... \'what u can dream, we can build it.\'';
        $this->setFieldbyId('note', $note);
        
        $this->clickSubmitByName('_qf_Edit_next_view');
        
        $this->assertResponse(200);
        $this->assertWantedText("Your Organization contact record has been saved.");
     
    }
}
?>