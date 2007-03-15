<?php

require_once 'api/crm.php';

class TestofGetNote extends UnitTestCase
{
    protected $_individual;
    protected $_note = array();

    function setUp()
    {
    }

    function tearDown()
    {
    }

    function testCreateContact()
    {
        $params = array('email' => 'aa@yahoo.com');
        $contact =& crm_create_contact($params, 'Individual');
        $this->_individual = $contact;
        $this->assertIsA($contact, 'CRM_Contact_BAO_Contact');        
    }

    function testCreateNote()
    {   
        $params = array(  
                        'entity_table' => 'civicrm_contact',
                        'entity_id'    => $this->_individual->id,
                        'contact_id'   => 1,
                        'note'         =>'rupam jaiswal'
                        );
        $note =& crm_create_note($params);
        $this->_note= $note;
        $this->assertEqual($note['entity_table'], 'civicrm_contact');
        $this->assertEqual($note['entity_id'], $this->_individual->id);
        $this->assertEqual($note['contact_id'], 1);
        $this->assertEqual($note['note'], 'rupam jaiswal');
    }
    
    function testGetNoteBad()
    {   
        $params = array();
        $note =& crm_get_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testGetNoteBadByEntityTable()
    {   
        $params = array(  
                        'entity_table' => 'civicrm_contact'
                        );
        $note =& crm_get_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testGetNoteBadByEntityId()
    {   
        $params = array(  
                        'entity_id' => $this->_individual->id
                        );
        $note =& crm_get_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testGetNoteByEntityTableAndEntityId()
    {   
        $params = array(  
                        'entity_table' => 'civicrm_contact',
                        'entity_id'    => $this->_individual->id,
                        );
        $note =& crm_get_note($params);
        //$this->assertIsA($note[$this->_note['id']], 'CRM_Core_BAO_Note');
        $this->assertEqual($this->_note['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->_note['entity_id'], $this->_individual->id);

    }

    function testGetNoteById()
    {   
        $params = array('id' => $this->_note['id']);
        $note =& crm_get_note($params);
        //        $this->assertIsA($note[$this->_note['id']], 'CRM_Core_BAO_Note');
        $this->assertEqual($this->_note['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->_note['entity_id'], $this->_individual->id);
        $this->assertEqual($this->_note['note'], 'rupam jaiswal');
    }
    
    function testDeleteNote()
    {
        $params = array(
                        'entity_table' => 'civicrm_contact',
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
