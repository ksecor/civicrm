<?php

require_once 'api/crm.php';

class TestOfCreateNote extends UnitTestCase
{
    protected $_individual;
    
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

        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->_individual = $contact;
    }  
    
    
    function testCreateBadNoteByEntityTable()
    {   
        $params = array(  
                        'entity_id'    => 1020,
                        'entity_table' => 'civicrm_contact',                        
                        'note'         => 'By Entity Table'
                        );
        $note =& crm_create_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testCreateBadNoteByEntityId()
    {   
        $params = array(  
                        'entity_id'    => $this->_individual->id,
                        'note'         =>'By Entity Id'
                        );
        $note =& crm_create_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testCreateBadNoteByContactId()
    {   
        $params = array(  
                        'contact_id'   => 1020,
                        'note'         =>'By Contact Id'
                        );
        $note =& crm_create_note($params);
        $this->assertIsA($note, 'CRM_Core_Error');
    }
    
    function testCreateNote()
    {   
        $params = array(  
                        'entity_table' => 'civicrm_contact',
                        'entity_id'    => $this->_individual->id,
                        'contact_id'   => 1,
                        'note'         =>'Rupam Jaiswal'
                        );
        $this->note =& crm_create_note($params);
        
        $this->assertEqual($this->note['entity_table'], 'civicrm_contact');
        $this->assertEqual($this->note['entity_id'], $this->_individual->id);
        $this->assertEqual($this->note['contact_id'], 1);
        $this->assertEqual($this->note['note'], 'Rupam Jaiswal');
    }
    
    function testDeleteNote()
    {
        $params = array(
                        'id' => $this->note['id']
                        //'entity_table' => 'civicrm_contact',
                        //'entity_id'    => $this->_individual->id
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
