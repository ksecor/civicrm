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
    
    function testCRM983CreateFile()
    {
        $create = array('file_type_id' => 350,
                        'uri' => 'file://home/guest/pic.jpg',
                        'description'  => 'Do Not know what is happeing.');
        $this->fileCreate = crm_create_file($create);
        CRM_Core_Error::debug('Created File', $this->fileCreate);
    }
        
    function testCRM983UpdateFile()
    {
        $update = array(
                        'id' => $this->fileCreate->id,
                        'file_type_id' => 350,
                        'uri' => 'file://home/guest/new.rm',
                        'description'  => 'Do Not know what is happeing.'
                        );
        $this->fileUpdate = crm_update_file($update);
        CRM_Core_Error::debug('Updated File', $this->fileUpdate);
    }
    
    function testCRM983DeleteFile()
    {
        $delete = array(//'id' => 23,
                        'file_type_id' => 350,
                        'description'  => 'Do Not know what is happeing.'
                        );
        $this->fileDelete = crm_delete_file($delete);
        CRM_Core_Error::debug('Delete File', $this->fileDelete);
    }
    
    function testCRM983GetFile()
    {
        $get = array('file_type_id' => 350,
                     'description'  => 'Do Not know what is happeing.'
                     );
        $this->fileGet = crm_get_file($get);
        CRM_Core_Error::debug('Get File', $this->fileGet);
    }
    
    function testCRM983CreateEntityFile()
    {
        $create = array('file_type_id' => 350,
                        'uri' => 'file://home/guest/pic.jpg',
                        'description'  => 'Do Not know what is happeing.');
        $fileCreate = crm_create_file($create);
        
        $contact = array('contact_id' => 2);
        $this->entity = crm_get_contact($contact);
        
        $this->createEntityFile = crm_create_entity_file($fileCreate, $this->entity);
        
        CRM_Core_Error::debug('Create Entity File', $this->createEntityFile);
    }
    
    function testCRM983GetFileByEntity()
    {
        $getFilesByEntity = crm_get_files_by_entity($this->entity);
        
        CRM_Core_Error::debug('Create Entity File', $getFilesByEntity);
    }
    
    function testCRM983DeleteEntityFile()
    {
        $delete = array('id'           =>  $this->createEntityFile->id,
                        'entity_id'    => $this->entity->id,
                        'entity_table' => 'civicrm_contact'
                        );
        
        $deleteEntityFile = crm_delete_entity_file($delete);
        CRM_Core_Error::debug('Delete Entity File', $deleteEntityFile);
    }
}
?>