<?php

require_once 'api/crm.php';

class TestofUpdateFile extends UnitTestCase
{
    protected $_file = array();   
    
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
                        'description'  => 'new file'
                        );
        $file =& crm_create_file($params);
        $this->assertEqual($file['file_type_id'], 1);
        $this->assertEqual($file['description'],'new file');
        $this->assertEqual($file['uri'],'file://home/rupam/desktop/details.txt' ); 
        $this->_file = $file;
    }
    
    function testUpdateFileEmptyError()
    {
        $params = array();
        $file =& crm_update_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }
    
    function testUpdateFileByUriError()
    {
        $params = array(
                        'uri'        => 'file://home/rupam/desktop/civicrm.settings.complete.php',
                        'description'=> 'old file by id'
                        );
        $file =& crm_update_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }
    
    function testUpdateFileError()
    {
        $params = array(
                        'file_type_id' => 1,
                        'uri'          => 'file://home/rupam/desktop/civicrm.settings.complete.php',
                        'description'  => 'old file'
                        );
        $file =& crm_update_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }
    
    function testUpdateFileById()
    {
        $params = array(
                        'id'         => $this->_file['id'],
                        'uri'        => 'file://home/rupam/desktop/civicrm.settings.complete.php',
                        'description'=> 'old file by id'
                        );
        $file =& crm_update_file($params);
        $this->assertEqual($file['id'],$this->_file['id'] );
        $this->assertEqual($file['file_type_id'],1 );
        $this->assertEqual($file['uri'], 'file://home/rupam/desktop/civicrm.settings.complete.php');
        $this->assertEqual($file['description'],'old file by id' );
       
    }
    
    function testDeleteFile()
    {
        $deleteFile =& crm_delete_file($this->_file['id']);
        $this->assertNull($deleteFile);
    }
}
?>
