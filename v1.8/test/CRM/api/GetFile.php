<?php

require_once 'api/crm.php';

class TestofGetFile extends UnitTestCase
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
                        'description'  => 'new file',
                        );
        $file =& crm_create_file($params);
        $this->assertEqual($file['file_type_id'], 1);
        $this->assertEqual($file['description'],'new file');
        $this->assertEqual($file['uri'],'file://home/rupam/desktop/details.txt' ); 
        $this->_file = $file;
    }

    function testGetFileBadEmpty()
    {
        $params = array();
        $file =& crm_get_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }

    function testGetFileBadByUri()
    {
        $params = array(
                        'uri' =>'file://home/rupam/desktop/details.txt'
                        );
        $file =& crm_get_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }

    
    function testGetFileBadByDescription()
    {
        $params = array(
                        'description' => 'new file'
                        );
        $file =& crm_get_file($params);
        $this->assertIsA($file, 'CRM_Core_Error');
    }

    function testGetFileByFileTypeId()
    {
        $params = array(
                        'file_type_id' => 1
                        );
        $file =& crm_get_file($params);
        $this->assertEqual($file[$this->_file['id']]['id'],$this->_file['id'] );
        $this->assertEqual($file[$this->_file['id']]['file_type_id'],1 );
        $this->assertEqual($file[$this->_file['id']]['uri'],$this->_file['uri'] );
        $this->assertEqual($file[$this->_file['id']]['description'],$this->_file['description'] );
    }

    function testGetFile()
    {
        $params = array(
                        'id' =>$this->_file['id']
                        );
        $file =& crm_get_file($params);
        $this->assertEqual($file[$this->_file['id']]['id'],$this->_file['id'] );
        $this->assertEqual($file[$this->_file['id']]['file_type_id'],1 );
        $this->assertEqual($file[$this->_file['id']]['uri'],$this->_file['uri'] );
        $this->assertEqual($file[$this->_file['id']]['description'],$this->_file['description'] );

    }

    function testDeleteFile()
    {
        $deleteFile =& crm_delete_file($this->_file['id']);
        $this->assertNull($deleteFile);
    }
}
?>
