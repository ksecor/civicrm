<?php

require_once 'api/crm.php';

class TestofUpdateNote extends UnitTestCase
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
        $params = array('email' => 'aa@yahoo.com');
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
                        'note'         =>'rupam jaiswal'
                        );
        $note =& crm_create_note($params);
        $this->_note= $note;
        $this->assertEqual($note['entity_table'], 'civicrm_contact');
        $this->assertEqual($note['entity_id'], $this->_individual->id);
        $this->assertEqual($note['contact_id'], 1);
        $this->assertEqual($note['note'], 'rupam jaiswal');
    }
    
    function testUpdateNoteErrorwithoutId()
    {   
        $params = array(
                        'contact_id'   => 1,
                        'note'         => 'changed rupam jaiswal'
                        );
        $note =& crm_update_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testUpdateNoteBadByEntityTableEntityId()
    {   
        $params = array(                          
                        'entity_id'    => $this->_individual->id,
                        'entity_table' => 'civicrm_contact',
                        'note'         =>'changed rupam jaiswal by entity table & entity id'
                        );
        $note =& crm_update_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }

    function testUpdateNoteError()
    {   
        $params = array();
        $note =& crm_update_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testUpdateNote()
    {   
        $params = array(                          
                        'id'           => $this->_note['id'],
                        'note'         =>'changed rupam jaiswal'
                        );
        $note =& crm_update_note($params);
        $this->assertEqual($note['entity_table'], 'civicrm_contact');
        $this->assertEqual($note['id'], $this->_note['id']);
        $this->assertEqual($note['contact_id'], 1);
        $this->assertEqual($note['note'], 'changed rupam jaiswal');
    }
    
    function testDeleteNote()
    {
        $params = array(
                        'id'    => $this->_note['id']
                        );
        $deleteNote =& crm_delete_note($params);
        $this->assertEqual($deleteNote,1);
    }

    function testDeleteContact()
    {
        $contact = $this->_individual;
        $deleteContact =& crm_delete_contact(& $contact);
        $this->assertNull($deleteContact);
    }
}
?>