<?php

require_once 'api/crm.php';

class TestofDeleteFile extends UnitTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCreateFile()
    {
        $params = array(
                        'file_type_id' => 1,
                        'uri'          => 'file://tmp/details.txt',
                        'description'  => 'new file',
                        );
        $this->_file =& crm_create_file($params);
        $this->assertEqual($this->_file['file_type_id'], 1);
        $this->assertEqual($this->_file['description'],'new file');
        $this->assertEqual($this->_file['uri'],'file://tmp/details.txt' ); 
    }
    
    function testDeleteFileErrorEmpty()
    {
        $params = array();
        $deleteFile =& crm_delete_file($params);
        $this->assertIsA($deleteFile,'CRM_Core_Error');
    }
    
    function testDeleteFileErrorByDescription()
    {
        $params = array(
                        'description' => 'new file'
                        );
        
        $deleteFile =& crm_delete_file($params);
        $this->assertIsA($deleteFile, 'CRM_Core_Error');
    }
    
    function testDeleteFileErrorByFileTypeId()
    {
        $params = array(
                        'file_type_id' => 1
                        );
        $deleteFile =& crm_delete_file($params);
        $this->assertIsA($deleteFile, 'CRM_Core_Error');
    }
    
    function testDeleteFileErrorByUri()
    {
        $params = array(
                        'uri' =>'file://tmp/details.txt'
                        );
        $deleteFile =& crm_delete_file($params);
        $this->assertIsA($deleteFile, 'CRM_Core_Error');
    }
    
    function testDeleteFile()
    {
        $deleteFile =& crm_delete_file($this->_file['id']);
        $this->assertNull($deleteFile);
    }
    
    /* Special Case for Delete File .. To check whether the Contact entries get deleted from civicrm_file_entity after successfule delete of File from civicrm_file. */

   function  testCreateFile1()
    {
        $params1 = array(
                        'file_type_id' => 1,
                        'uri'          => 'file://tmp/details.txt',
                        'description'  => '.txt file',
                        );
        $this->_file1 =& crm_create_file($params1);
        $this->assertEqual($this->_file1['file_type_id'], 1);
        $this->assertEqual($this->_file1['description'],'.txt file');
        $this->assertEqual($this->_file1['uri'],'file://tmp/details.txt' ); 
        
        $params2 = array(
                        'file_type_id' => 2,
                        'uri'          => 'file://tmp/bill.png',
                        'description'  => '.png file',
                        );
        $this->_file2 =& crm_create_file($params2);
        $this->assertEqual($this->_file2['file_type_id'], 2);
        $this->assertEqual($this->_file2['description'],'.png file');
        $this->assertEqual($this->_file2['uri'],'file://tmp/bill.png' );
    }
    
    function testCreateContact1()
    {
        $params1 = array('email'=>'aa1@gamil.com');
        $contact1 =& crm_create_contact($params1, 'Individual');
        $this->assertIsA($contact1, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact1->contact_type, 'Individual');
        $this->_individual1 = $contact1;
        
        $params2 = array('email'=>'aa2@gamil.com');
        $contact2 =& crm_create_contact($params2, 'Individual');
        $this->assertIsA($contact2, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact2->contact_type, 'Individual');
        $this->_individual2 = $contact2;
    }
        
    function testCreateEntityFile1()
    {
        $params1 = array('contact_id' => $this->_individual1->id);
        $this->entity1 = crm_get_contact($params1);
        
        $params2 = array('contact_id' => $this->_individual2->id);
        $this->entity2 = crm_get_contact($params2);
      
        //assigning file1 to contact1
        $this->createEntityFile1 = crm_create_entity_file($this->_file1['id'], $this->entity1->id,'civicrm_contact');
        $this->assertEqual($this->createEntityFile1['entity_table'], 'civicrm_contact');  
        $this->assertEqual($this->createEntityFile1['entity_id'], $this->entity1->id);  
        $this->assertEqual($this->createEntityFile1['file_id'], $this->_file1['id']);  
        
        //assigning file2 to contact1
        $this->createEntityFile12 = crm_create_entity_file($this->_file2['id'], $this->entity1->id,'civicrm_contact'); 
        $this->assertEqual($this->createEntityFile12['entity_table'], 'civicrm_contact');  
        $this->assertEqual($this->createEntityFile12['entity_id'], $this->entity1->id);  
        $this->assertEqual($this->createEntityFile12['file_id'], $this->_file2['id']);  
       
        //assigning file1 to contact2
        $this->createEntityFile2 = crm_create_entity_file($this->_file1['id'], $this->entity2->id,'civicrm_contact' ); 
        $this->assertEqual($this->createEntityFile2['entity_table'], 'civicrm_contact');  
        $this->assertEqual($this->createEntityFile2['entity_id'], $this->entity2->id);  
        $this->assertEqual($this->createEntityFile2['file_id'], $this->_file1['id']);  
    }
    
    function testGetFilesByEntity1()
    {
        //getting file for contact1
        $this->getFilesByEntity1 = crm_get_files_by_entity($this->entity1->id,'civicrm_contact');
        
        //assertions for file1
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['id'], $this->_file1['id']);  
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['entity_id'],$this->entity1->id );  
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['file_id'], $this->_file1['id']);
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['file_type_id'], $this->_file1['file_type_id']);  
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['uri'], $this->_file1['uri']);
        $this->assertEqual($this->getFilesByEntity1[$this->_file1['id']]['description'], $this->_file1['description']); 
        
        //assertions for file2
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['id'], $this->_file2['id']);  
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['entity_id'],$this->entity1->id);  
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['file_id'], $this->_file2['id']);
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['file_type_id'], $this->_file2['file_type_id']);  
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['uri'], $this->_file2['uri']);
        $this->assertEqual($this->getFilesByEntity1[$this->_file2['id']]['description'], $this->_file2['description']);  
        
        //getting file for contact2
        $this->getFilesByEntity2 = crm_get_files_by_entity($this->entity2->id,'civicrm_contact');
        
        //assertions for file1
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['id'], $this->_file1['id']);  
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['entity_id'],$this->entity2->id);  
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['file_id'], $this->_file1['id']);
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['file_type_id'], $this->_file1['file_type_id']);  
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['uri'], $this->_file1['uri']);
        $this->assertEqual($this->getFilesByEntity2[$this->_file1['id']]['description'], $this->_file1['description']);  
    }
    
    function testDeleteFile1()
    {
        //deleting file1     
        $deleteFile = crm_delete_file($this->_file1['id']);
        $this->assertNull($deleteFile);
    }

    function testGetFilesByEntityAfterDelete()
    {
        $this->getFilesByEntity1New = crm_get_files_by_entity($this->entity1->id,'civicrm_contact');
        $this->assertNull($this->getFilesByEntity1New[$this->_file1['id']]['id']);
        $this->assertNotNull($this->getFilesByEntity1New[$this->_file2['id']]['id']);
        
        $this->getFilesByEntity2New= crm_get_files_by_entity($this->entity2->id,'civicrm_contact');
        $this->assertIsA($this->getFilesByEntity2New, 'CRM_Core_Error');
    }

    function testDeleteFile2()
    {
        //deleting file2     
        $deleteFile = crm_delete_file($this->_file2['id']);
        $this->assertNull($deleteFile);
    }
    
    function testDeleteContact1()
    {
        $deleteContact1 =& crm_delete_contact(& $this->_individual1,102 );
        $this->assertNull($deleteContact1);
        
        $deleteContact2 =& crm_delete_contact(& $this->_individual2,102);
        $this->assertNull($deleteContact2);
    }
    
}

