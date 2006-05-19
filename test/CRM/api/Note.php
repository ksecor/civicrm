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
                            'contact_id'    => 101,
                            'modified_date' =>'20060318'
                            );
        $note =& crm_create_note($noteParams);
        //print_r($note);
    }
        
    function testGetNote()
    {
        $noteParams = array('id'=>109,
                            //'entity_id'     => 152,
                            //'entity_table'  => 'civicrm_relationship'
                            );
        $note =& crm_get_note($noteParams);
        print_r($note);
    }

    function testDeleteNote()
    {
        $noteParams = array('id'=>110,
                            //'entity_id'     => 152,
                            //'entity_table'  => 'civicrm_relationship'
                            );
        $note =& crm_delete_note($noteParams);
        print_r($note);
    }

    function testUpdateNote()
    {
        $noteParams = array('id' => 109,
                            'entity_id'     => 152,
                            'entity_table'  => 'civicrm_rela'
                            );
        $note =& crm_update_note($noteParams);
        print_r($note);
    }
}
?>