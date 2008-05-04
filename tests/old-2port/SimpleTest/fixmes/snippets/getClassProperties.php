<?php

require_once 'api/crm.php';

class TestOfGetClassPropertiesSnippet extends UnitTestCase 
{

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    function testGetClassPropertiesIndividualCore() 
    {
        require_once 'CRM/Contact/DAO/Individual.php';
        $property_object = array();
        $prop = crm_get_class_properties("Individual","core");
        $this->assertNotA($prop,'CRM_Core_Error');
        //   $fields = CRM_Contact_DAO_Individual::fields( );
        $id = -1;
//         foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
//         }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
    
        $this->assertEqual($prop,$property_object);
    }
    
    function testGetClassPropertiesIndividualCustom() 
    {
        $property_object = array();
        $prop = crm_get_class_properties("Individual","custom");
        $this->assertNotA($prop,'CRM_Core_Error');

        // $fields = CRM_Contact_DAO_Individual::fields( );
        $id = -1;
       //  foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
        //      }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        $class_name = 'Individual';
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($class_name, null, -1);
        foreach($groupTree as $node) {
            $fields = $node["fields"];
            
            foreach($fields as $key => $values) {
       
                $property_object[] = array("id"=>$values['id'],"name"=>$values['name'],"data_type"=>$values['data_type'] ,"description"=>$values['help_post']);
            }
            
        }
        $this->assertEqual($prop,$property_object);
        
    }

    function testGetClassPropertiesOrganizationCore() 
    {
        require_once 'CRM/Contact/DAO/Organization.php';
        $prop = crm_get_class_properties("Organization","Core");
        $this->assertNotA($prop,'CRM_Core_Error');
        
        //  $fields = CRM_Contact_DAO_Organization::fields( );
        $id = -1;
   //      foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
        //      }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        
        $this->assertEqual($prop,$property_object);
        

    }
    
    function testGetClassPropertiesOrganizationCustom() 
    {
        $prop = crm_get_class_properties("Organization","custom");
        $this->assertNotA($prop,'CRM_Core_Error');

//   $fields = CRM_Contact_DAO_Organization::fields( );
        $id = -1;
      //   foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
//        }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        $class_name = 'Organization';
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($class_name, null, -1);
        foreach($groupTree as $node) {
            $fields = $node["fields"];
            
            foreach($fields as $key => $values) {
       
                $property_object[] = array("id"=>$values['id'],"name"=>$values['name'],"data_type"=>$values['data_type'] ,"description"=>$values['help_post']);
            }
            
        }
        $this->assertEqual($prop,$property_object);
    }

    function testGetClassPropertiesHouseholdCore() 
    {

        require_once 'CRM/Contact/DAO/Household.php';
        $prop = crm_get_class_properties("Household","Core");
        $this->assertNotA($prop,'CRM_Core_Error');

//  $fields = CRM_Contact_DAO_Household::fields( );
        $id = -1;
       //  foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
        //      }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        
        $this->assertEqual($prop,$property_object);
    }
    
    function testGetClassPropertiesHouseholdCustom() 
    {
        
        $prop = crm_get_class_properties("Household","custom");
        $this->assertNotA($prop,'CRM_Core_Error');

//$fields = CRM_Contact_DAO_Household::fields( );
        $id = -1;
       //  foreach($fields as $key => $values) {
//             $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']),"description"=>$values['title']);
        //       }
        $fields = CRM_Contact_DAO_Contact::fields( );
        foreach($fields as $key => $values) {
            
            $property_object[] = array("id"=>$id,"name"=>$key,"data_type"=>CRM_Utils_Type::typeToString($values['type']) ,"description"=>$values['title']);
        }
        $class_name = 'Household';
        $groupTree = CRM_Core_BAO_CustomGroup::getTree($class_name, null, -1);
        foreach($groupTree as $node) {
            $fields = $node["fields"];
            
            foreach($fields as $key => $values) {
       
                $property_object[] = array("id"=>$values['id'],"name"=>$values['name'],"data_type"=>$values['data_type'] ,"description"=>$values['help_post']);
            }
            
        }
        $this->assertEqual($prop,$property_object);
        
    }
}

