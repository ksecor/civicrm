<?php

require_once 'api/v2/Note.php';
require_once 'tests/phpunit/CiviTest/CiviUnitTestCase.php';

/**
 * Class contains api test cases for "civicrm_note"
 *
 */

class api_v2_NoteTest extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_params;

    function __construct( ) {
        parent::__construct( );
    }

    function get_info( )
    {
        return array(
                     'name'        => 'Note Create',
                     'description' => 'Test all Note Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp() 
    {
        //  Connect to the database
        parent::setUp();

        //  Truncate the tables
        $op = new PHPUnit_Extensions_Database_Operation_Truncate( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__) . '/truncate.xml') );

        $this->_contactID = $this->organizationCreate( );
        
        $this->_params = array(
                               'entity_table'  => 'civicrm_contact',
                               'entity_id'     => $this->_contactID,
                               'note'          => 'Hello!!! m testing Note',
                               'contact_id'    => $this->_contactID,
                               'modified_date' => date('Ymd'),
                               'subject'       => 'Test Note', 
                               );

        $this->_note      = $this->noteCreate( $this->_contactID );
        $this->_noteID    = $this->_note['id'];
    }

    /**
     * Note retrieval
     */
    function testRetrieveNote( )
    {
        throw new PHPUnit_Framework_IncompleteTestError(
                      "test not implemented" );
    }

    
    /**
     * Check create with wrong parameter (not Array)
     */
    function testCreateNoteParamsNotArray( )
    {
        $params = null;
        $result = civicrm_note_create( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertNotEquals( $result['error_message'], 'Required parameter missing' );
        $this->assertEquals( $result['error_message'], 'Params is not an array' );
    }    

    /**
     * Check create with empty params
     */    
    function testCreateNoteEmptyParams( )
    {
        $params = array( );
        $result = civicrm_note_create( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertFalse( array_key_exists( 'entity_id', $result ) );
        $this->assertEquals( $result['error_message'], 'Required parameter missing' );
    }

    /**
     * Check create with partial params
     */    
    function testCreateNoteParamsWithoutEntityId( )
    {
        unset($this->_params['entity_id']);
        $result = civicrm_note_create( $this->_params );
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Required parameter missing' );
    }

    /**
     * Check create with right params
     */
    function testCreateNote( )
    {
        $result = civicrm_note_create( $this->_params );
        $this->assertEquals( $result['note'], 'Hello!!! m testing Note');
        $this->assertTrue( array_key_exists( 'entity_id', $result ) );
        $this->assertEquals( $result['is_error'], 0 );
        civicrm_note_delete( $result );
    }

    /**
     * Check update with empty params
     */
    function testNoteUpdateEmpty( )
    {
        $params = array();        
        $note   = & civicrm_note_create( $params );
        $this->assertEquals( $note['is_error'], 1 );
        $this->assertEquals( $note['error_message'], 'Required parameter missing' );
        $this->fail( 'Not sure what this test does?' );
    }

    /**
     * Check update with missing contact id
     */
    function testNoteUpdateMissingContactId( )
    {
        $params = array(
                        'entity_id'    => $this->_contactID,
                        'entity_table' => 'civicrm_contact'                
                        );        
        $note   = & civicrm_note_create( $params );
        $this->assertEquals( $note['is_error'], 1 );
        $this->assertEquals( $note['error_message'], 'Required parameter missing' );
    }

    /**
     * Check successful update
     */    
    function testNoteUpdate( )
    {
        $params = array(
                        'id'           => $this->_noteID,
                        'contact_id'   => $this->_contactID,
                        'entity_id'    => $this->_contactID,
                        'entity_table' => 'civicrm_contribution',
                        'note'         => 'Note1',
                        'subject'      => 'Hello World'
                        );
        
        //Update Note
        $note = & civicrm_note_create( $params );
        
        $this->assertEquals( $note['id'],$this->_noteID );
        $this->assertEquals( $note['entity_id'],$this->_contactID );
        $this->assertEquals( $note['entity_table'],'civicrm_contribution' );
    }

    /**
     * Check delete with empty params
     */
    function testNoteDeleteWithEmptyParams( )
    {
        $params     = array();        
        $deleteNote = & civicrm_note_delete( $params );
               
        $this->assertEquals( $deleteNote['is_error'], 1 );
        $this->assertEquals( $deleteNote['error_message'], 'Invalid or no value for Note ID');
    }

    /**
     * Check delete with wrong id
     */    
    function testNoteDeleteWithWrongID( )
    {
        $params     = array( 'id' => 0 );        
        $deleteNote = & civicrm_note_delete( $params ); 
       
        $this->assertEquals( $deleteNote['is_error'], 1 );
        $this->assertEquals( $deleteNote['error_message'], 'Invalid or no value for Note ID');
    }

    /**
     * Check successful delete
     */        
    function testNoteDelete( )
    {
        $params = array( 'id'        => $this->_noteID,
                         'entity_id' => $this->_note['entity_id']
                         );
                        
        $deleteNote  =& civicrm_note_delete( $params );
             
        $this->assertEquals( $deleteNote['is_error'], 0 );
        $this->assertEquals( $deleteNote['result'], 1 );
    }
    
    function tearDown( ) 
    {
    }
}


