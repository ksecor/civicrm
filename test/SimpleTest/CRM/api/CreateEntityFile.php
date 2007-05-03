<?php

require_once 'api/crm.php';

class TestofCreateEntityFile extends UnitTestCase
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
                        'uri'          => 'file://home/rupam/desktop/details.txt',
                        'description'  => '.txt file'
                        );
        $this->_file =& crm_create_file($params);
        $this->assertEqual($this->_file['file_type_id'], 1);
        $this->assertEqual($this->_file['description'],'.txt file');
        $this->assertEqual($this->_file['uri'],'file://home/rupam/desktop/details.txt' ); 
                
        $params1 = array(
                        'file_type_id' => 2,
                        'uri'          => 'file://home/rupam/desktop/bill.png',
                        'description'  => '.png file'
                        );
        $this->_file1 =& crm_create_file($params1);
        $this->assertEqual($this->_file1['file_type_id'], 2);
        $this->assertEqual($this->_file1['description'],'.png file');
        $this->assertEqual($this->_file1['uri'],'file://home/rupam/desktop/bill.png' ); 
    }

    function testCreateEntityFileError()
    {
        
        $this->entity = array();        
        $this->createEntityFile = crm_create_entity_file($this->_file, $this->entity);        
        $this->assertIsA($this->createEntityFile,'CRM_Core_Error');
       
    }

    function testCreateEntityFile()
    {
        $contact = array('contact_id' => 2);
        $this->entity = crm_get_contact($contact);  
      
        $this->createEntityFile = crm_create_entity_file($this->_file['id'], $this->entity->id,'civicrm_contact');
        $this->assertEqual($this->createEntityFile['entity_table'], 'civicrm_contact');  
        $this->assertEqual($this->createEntityFile['entity_id'], 2);  
        $this->assertEqual($this->createEntityFile['file_id'], $this->_file['id']);  

        $this->createEntityFile1 = crm_create_entity_file($this->_file1['id'], $this->entity->id,'civicrm_contact');        
        $this->assertEqual($this->createEntityFile1['entity_table'], 'civicrm_contact');  
        $this->assertEqual($this->createEntityFile1['entity_id'], 2);  
        $this->assertEqual($this->createEntityFile1['file_id'], $this->_file1['id']);  
    }

     function testDeleteEntityFile()
    {
        $delete = array(
                        'id'           => $this->createEntityFile['id'],
                        'entity_id'    => $this->entity->id,
                        'entity_table' => 'civicrm_contact'
                        );
        
        $deleteEntityFile = crm_delete_entity_file($delete);
        $this->assertNull($deleteEntityFile);

        $delete1 = array(
                        'id'           => $this->createEntityFile1['id'],
                        'entity_id'    => $this->entity->id,
                        'entity_table' => 'civicrm_contact'
                        );
        
        $deleteEntityFile1 = crm_delete_entity_file($delete1);
        $this->assertNull($deleteEntityFile1);
    }
    
    function testDeleteFile()
    {
        $deleteFile =& crm_delete_file($this->_file['id']);
        $this->assertNull($deleteFile);

        $deleteFile1 =& crm_delete_file($this->_file1['id']);
        $this->assertNull($deleteFile1);
    }

}
?>
