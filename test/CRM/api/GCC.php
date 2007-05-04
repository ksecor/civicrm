<?php

require_once 'api/crm.php';

class TestOfFileAPI extends UnitTestCase
{   
    function setUp()
    {
    }
    
    function tearDown()
    {
    }

    function testGetContact( ) {
        $p = array( 'contact_id' => 102 );
        $c =& crm_get_contact( $p );
        CRM_Core_Error::debug( 'c', $c );
    }

    /**
    function testGetFile( ) {
        $files = crm_get_files_by_entity( 13 );
        
        CRM_Core_Error::debug( 'c', $files );
    }

    function testCreateFile( ) {
        $fileName = "/Users/lobo/public_html/drupal/files/civicrm/upload/tt_XXX.csv";
        $params = array( 'mime_type' => 'text/x-csv' );
        crm_add_file_by_entity( $fileName, 13, 'civicrm_contact', $params );
    }

    function testDeleteFile( ) {
        $params = array( 'id' => 3,
                         'entity_id' => 13,
                         'entity_table' => 'civicrm_contact' );
        crm_delete_entity_file( $params );
    }
    **/

}
