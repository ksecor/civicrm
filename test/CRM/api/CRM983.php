<?php

require_once 'api/crm.php';

class TestOfCRM983 extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    // Create File
    function testCRM983CreateFile()
    {
        $create = array('file_type_id' => 1,
                        'uri' => 'file://home/guest/pic.jpg',
                        'description'  => 'Do Not know what is happeing.');
        $this->fileCreate = crm_create_file($create);
        CRM_Core_Error::debug('Created File', $this->fileCreate);
    }
    // Update File
    function testCRM983UpdateFile()
    {
        $update = array(
                        'id' => $this->fileCreate['id'],
                        'file_type_id' => 1,
                        'uri' => 'file://home/guest/new.rm',
                        'description'  => 'Do Not know what is happeing.'
                        );
        $this->fileUpdate = crm_update_file($update);
        CRM_Core_Error::debug('Updated File', $this->fileUpdate);
    }
    // Delete File
    function testCRM983DeleteFile()
    {
        $this->fileDelete = crm_delete_file($this->fileCreate['id']);
        CRM_Core_Error::debug('Delete File', $this->fileDelete);
        }
    // Get File
    function testCRM983GetFile()
    {
        $create = array('file_type_id' => 1,
                        'uri' => 'file://home/guest/pic.jpg',
                        'description'  => 'Do Not know what is happeing.');
        $this->fileCreate = crm_create_file($create);
        
        $get = array('file_type_id' => $this->fileCreate['file_type_id']);
        $this->fileGet = crm_get_file($get);
        CRM_Core_Error::debug('Get File', $this->fileGet);
    }
    // Create Entity File
    function testCRM983CreateEntityFile()
    {
        $create = array('file_type_id' => 1,
                        'uri' => 'file://home/guest/pic.jpg',
                        'description'  => 'Do Not know what is happeing.');
        $fileCreate = crm_create_file($create);
        
        $contact = array('contact_id' => 2);
        $this->entity = crm_get_contact($contact);
        $this->createEntityFile = crm_create_entity_file($fileCreate['id'], $this->entity->contact_id);
        
        CRM_Core_Error::debug('Create Entity File', $this->createEntityFile);
    }
    // Get Files By Entity
    function testCRM983GetFileByEntity()
    {
        $getFilesByEntity = crm_get_files_by_entity($this->entity->id);
        
        CRM_Core_Error::debug('Get Files By Entity', $getFilesByEntity);
    }
    // Delete Entity Files
    function testCRM983DeleteEntityFile()
    {
        $delete = array('id'           => $this->createEntityFile['id'],
                        'entity_id'    => $this->entity->id,
                        'entity_table' => 'civicrm_contact'
                        );
        
        $deleteEntityFile = crm_delete_entity_file($delete);
        CRM_Core_Error::debug('Delete Entity File', $deleteEntityFile);
    }
}
?>
