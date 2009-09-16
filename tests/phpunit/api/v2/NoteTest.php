<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

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

    function tearDown( ) 
    {
        $this->noteDelete( $this->_note );
        $this->contactDelete( $this->_contactID );
    }

///////////////// civicrm_note_get methods

    /**
     * check retrieve note with wrong params type
     */
    function testGetNoteWrongParamsType( )
    {
        $this->markTestIncomplete();
    } 

    /**
     * check retrieve note with empty params
     */
    function testGetNoteEmptyParams( )
    {
        $params = array( );
        $note   =& civicrm_note_get( $params );
        $this->assertEquals( $note['is_error'], 1 );
        $this->assertEquals( $note['error_message'], 'No input parameters present' );
    } 

    /**
     * check retrieve note with missing patrameters
     */
    function testGetNoteWithoutEntityId( )
    {   
        $params = array( 'entity_table' => 'civicrm_contact' );
        $note   =& civicrm_note_get( $params );
        $this->assertEquals( $note['is_error'], 1 ); 
        $this->assertEquals( $note['error_message'], 'Invalid entity ID' );
    }

    /**
     * check successful retrieve note 
     */
    function testGetNote( )
    { 
        $entityId = $this->_note['entity_id'];
        $params   = array(
                          'entity_table'  => 'civicrm_contact',
                          'entity_id'     => $entityId
                          ); 
        $result = civicrm_note_get( $params );
        $this->assertEquals( $result['is_error'], 0 );
    }


///////////////// civicrm_note_create methods
    
    /**
     * Check create with wrong parameter (not Array)
     */
    function testCreateNoteParamsNotArray( )
    {
        $params = null;
        $result = civicrm_note_create( $params );
        
        $this->assertEquals( $result['is_error'], 1 );
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
    }

///////////////// civicrm_note_update methods


    /**
     * Check update note with wrong params type.
     */
    function testUpdateWrongParamsType( )
    {
        $this->markTestIncomplete();
    } 

    /**
     * Check update with empty params
     */
    function testUpdateEmptyParams( )
    {
        $params = array();        
        $note   = & civicrm_note_update( $params );
        $this->assertEquals( $note['is_error'], 1 );
        $this->assertEquals( $note['error_message'], 'Required parameter missing' );
    }

    /**
     * Check update with missing contact id
     */
    function testUpdateMissingContactId( )
    {
        $params = array(
                        'entity_id'    => $this->_contactID,
                        'entity_table' => 'civicrm_contact'                
                        );        
        $note   = & civicrm_note_update( $params );
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
        $note = & civicrm_note_update( $params );
        
        $this->assertEquals( $note['id'],$this->_noteID );
        $this->assertEquals( $note['entity_id'],$this->_contactID );
        $this->assertEquals( $note['entity_table'],'civicrm_contribution' );
    }

///////////////// civicrm_note_delete methods

    /**
     * Check update note with wrong params type.
     */
    function testDeleteWrongParamsType( )
    {
        $this->markTestIncomplete();
    } 

    /**
     * Check delete with empty params
     */
    function testDeleteWithEmptyParams( )
    {
        $params     = array();        
        $deleteNote = & civicrm_note_delete( $params );
               
        $this->assertEquals( $deleteNote['is_error'], 1 );
        $this->assertEquals( $deleteNote['error_message'], 'Invalid or no value for Note ID');
    }

    /**
     * Check delete with wrong id
     */    
    function testDeleteWithWrongID( )
    {
        $params     = array( 'id' => 0 );        
        $deleteNote = & civicrm_note_delete( $params ); 
       
        $this->assertEquals( $deleteNote['is_error'], 1 );
        $this->assertEquals( $deleteNote['error_message'], 'Invalid or no value for Note ID');
    }

    /**
     * Check successful delete
     */        
    function testDelete( )
    {
        $params = array( 'id'        => $this->_noteID,
                         'entity_id' => $this->_note['entity_id']
                         );
                        
        $deleteNote  =& civicrm_note_delete( $params );
             
        $this->assertEquals( $deleteNote['is_error'], 0 );
        $this->assertEquals( $deleteNote['result'], 1 );
    }
    

}


