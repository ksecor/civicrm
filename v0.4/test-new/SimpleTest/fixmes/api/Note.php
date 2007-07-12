<?php

require_once 'api/crm.php';

class TestOfNoteAPI extends UnitTestCase 
{

    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    /* Test cases for CRUD for Notes  */ 
    function testCreateNote()
     {
        $noteParams = array(
                            'entity_id'     => 154,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note =& crm_create_note($noteParams);
     }
    
    function testGetNote()
    {
        $noteParams1 = array(
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note1 =& crm_create_note($noteParams1);
        
        $noteParams2 = array(
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa2',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note2 =& crm_create_note($noteParams2);
        
        $noteParams = array(//'id'=>26,
                            'entity_id'     => 154,
                            'entity_table'  => 'civicrm_relationship'
                            );
        $note =& crm_get_note($noteParams);
    }
    
    function testDeleteNote()
    {
        $noteParams1 = array(
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note1 =& crm_create_note($noteParams1);
        
        $noteParams2 = array(
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa2',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note2 =& crm_create_note($noteParams2);
        
        
        $noteParams = array(//'id'=>$note1->id,
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'contact_id'    => 100
                            );
        $note =& crm_delete_note($noteParams);
    }
    
    function testUpdateNote()
    {
        $noteParams1 = array(
                            'entity_id'     => 14,
                            'entity_table'  => 'civicrm_relationship',
                            'note'          => 'aaaaaaaaaaaa',
                            'contact_id'    => 100,
                            'modified_date' =>'20060318'
                            );
        $note1 =& crm_create_note($noteParams1);
               
        $noteParams = array('id'            => $note1['id'],
                            'entity_id'     => 100,
                            'entity_table'  => 'civicrm_contact',
                            'note'          => 'bbbbbbbb',
                            );
        $note =& crm_update_note($noteParams);
    }       
}
?>
