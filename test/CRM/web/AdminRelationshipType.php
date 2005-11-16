<?php

require_once "CommonAPI.php";

class TestOfAdminRelationshipTypeForm extends WebTestCase 
{  
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testAdminAddRelationshipType()
    {
        //echo "\n ************* Admin Relationship Types : Add ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Relationship Types')) {
            $this->clickLink('Relationship Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertWantedText("New Relationship Type")) {
            $this->clickLinkById("newRelationshipType");
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("New Relationship Type");
        
        $nameAB = 'Organization Name';
        $nameBA = 'Owner of Organization';
        $contactA = 'Organizations';
        $contactB = 'Individuals';
        $description = 'This Relationship Type is Created by Web Test';
        
        $this->setFieldById('name_a_b', $nameAB);
        $this->setFieldById('name_b_a', $nameBA);
        $this->setFieldById('contact_type_a', $contactA);
        $this->setFieldById('contact_type_b', $contactB);
        $this->setFieldbyId('description', $description);
        $this->setField('is_active', 0);
        
        $this->clickSubmitByName('_qf_RelationshipType_next');
        
        $this->assertResponse(200);
    }
    /*
    function testAdminEditRelationshipType()
    {
        //echo "\n ************* Admin Relationship Types : Edit ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Relationship Types')) {
            $this->clickLink('Relationship Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Edit')) {
            $this->clickLink('Edit');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Edit Relationship Type");
        
        $nameAB = 'Organization Name E';
        $nameBA = 'Owner of Organization E';
        $contactA = 'Organizations';
        $contactB = 'Individuals';
        $description = 'This Relationship Type is Created by Web Test...Edited.';
        
        $this->setFieldById('name_a_b', $nameAB);
        $this->setFieldById('name_b_a', $nameBA);
        $this->setFieldById('contact_type_a', $contactA);
        $this->setFieldById('contact_type_b', $contactB);
        $this->setFieldbyId('description', $description);
        $this->setField('is_active', 1);
        
        $this->clickSubmitByName('_qf_RelationshipType_next');
        $this->assertWantedText("The Relationship Type has been saved.");
        
        $this->assertResponse(200);
    }
    
    function testAdminDeleteRelationshipType()
    {
        //echo "\n ************* Admin Relationship Types : Delete ************* \n";
        
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')) {
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Configure");
        
        if ($this->assertLink('Relationship Types')) {
            $this->clickLink('Relationship Types');
        }
        
        $this->assertResponse(200);
        
        if ($this->assertLink('Delete')) {
            $this->clickLink('Delete');
        }
        
        $this->assertResponse(200);
        $this->assertWantedText("Delete Relationship Type");
        
        $this->clickSubmitByName('_qf_RelationshipType_next');
        $this->assertWantedText('Selected Relationship type has been deleted.');
        
        $this->assertResponse(200);
    }*/
}
?>