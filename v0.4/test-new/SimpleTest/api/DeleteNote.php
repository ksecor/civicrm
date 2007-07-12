<?php

require_once 'api/crm.php';

class TestofDeleteNote extends UnitTestCase
{
    protected $_individual;
    protected $_note = array();
    
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function  testCreateContact()
    {
        $params = array(
                        'email' => 'aa@yahoo.com'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');
        $this->_individual = $contact;
    }
    
    function testCreateNote()
    {
        $params = array(  
                        'entity_table' => 'civicrm_contact',
                        'entity_id'    => $this->_individual->id,
                        'contact_id'   => 1,
                        'note'         =>'rupam jaiswal contact'
                        );
        $note =& crm_create_note($params);
        $this->_note = $note;
        $this->assertEqual($note['entity_table'],'civicrm_contact');
        $this->assertEqual($note['entity_id'],$this->_individual->id);
        $this->assertEqual($note['contact_id'],1);
        $this->assertEqual($note['note'],'rupam jaiswal contact');
    }
       
    function testDeleteNoteError()
    {
        $params = array();
        $deleteNote =& crm_delete_note($params);
        $this->assertIsA($deleteNote,'CRM_Core_Error');
    }

    function testDeleteNoteErrorByEntityId()
    {     
        $params = array(
                        'entity_id'    => $this->_note['entity_id']
                        );
        $deleteNote =& crm_delete_note($params);
        $this->assertIsA($deleteNote,'CRM_Core_Error');
    }    
    
    function testDeleteNoteErrorByEntityTable()
    {
        $params = array(
                        'entity_table' => $this->note['entity_table']
                        );
        $deleteNote =& crm_delete_note($params);
        $this->assertIsA($deleteNote,'CRM_Core_Error');
    }
    
    function testDeleteNoteById()
    {
        $params = array(
                        'id' => $this->_note['id']
                        );
        $deleteNote =& crm_delete_note($params);
        $this->assertEqual($deleteNote,1);
    }
    
    function testDeleteNoteByEntityTableEntityId()
    {
        //create a note
        $params = array(  
                        'entity_table' => 'civicrm_contact',
                        'entity_id'    => $this->_individual->id,
                        'contact_id'   => 1,
                        'note'         =>'rupam jaiswal contact no 2'
                        );
        $note =& crm_create_note($params);
               
        //delete a note
        $params = array(
                        'entity_table' => $note['entity_table'],
                        'entity_id'    => $this->_individual->id
                        );
        $deleteNote =& crm_delete_note($params);              
        $this->assertEqual($deleteNote,1);
    }

    function testDeleteContact()
    {
        $contact = $this->_individual;
        $deleteContact =& crm_delete_contact(& $contact,102);
        $this->assertNull($deleteContact);
    }
}
?>
