<?php

require_once 'api/crm.php';

class TestofCreateFile extends UnitTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testCreateFileErrorEmpty()
    {
        $params = array();
        $file =& crm_create_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');        
    }
    
    function testCreateFileErrorByUri()
    {
        $params = array(
                        'uri' => 'file://home/rupam/desktop/details.txt'
                        );
        $file =& crm_create_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');        
    }
    
    function testCreateFileErrorByDescription()
    {
        $params = array(
                        'description' => 'file://home/rupam/desktop/details.txt'
                        );
        $file =& crm_create_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');        
    }
    
    function testCreateFile()
    {
        $params = array(
                        'file_type_id' => 1,
                        'uri'          => 'file://home/rupam/desktop/details.txt',
                        'description'  => 'new file',
                        );
        $file =& crm_create_file($params);
        $this->assertEqual($file['file_type_id'], 1);
        $this->assertEqual($file['description'],'new file');
        $this->assertEqual($file['uri'],'file://home/rupam/desktop/details.txt' ); 
        $this->_file = $file;
    }
    
    function testDeleteFile()
    {
        $deleteFile =& crm_delete_file($this->_file['id']);   
        $this->assertNull($deleteFile);
    }
}
?>