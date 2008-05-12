<?php
require_once 'api/v2/Group.php';

class TestOfGroupDeleteAPIV2 extends CiviUnitTestCase 
{
  
    protected $_groupID;
             
    function setUp( ) 
    {

    }
    
    function testGroupCreate( )
    {
        $this->_groupID = $this->groupCreate( );
    }
    function testGroupDeleteWithEmptyParams( )
    {
        $params      = null;        
        $deleteGroup =& civicrm_group_delete( $params ); 
        
        $this->assertEqual( $deleteGroup['is_error'], 1 );
        $this->assertEqual( $deleteGroup['error_message'], 'No input parameters present');
    }
    
    function testGroupDeleteWithWrongID( )
    {
        $params      = array( 'id' => 0 );        
        $deleteGroup =& civicrm_group_delete( $params ); 

        $this->assertEqual( $deleteGroup['is_error'], 1 );
        $this->assertEqual( $deleteGroup['error_message'], 'Invalid or no value for Group ID');
    }
    
    function testGroupDelete( )
    {
        $params      = array( 'id' => $this->_groupID );
        $deleteGroup =& civicrm_group_delete( $params );   
       
        $this->assertEqual( $deleteGroup['is_error'], 0 );
        $this->assertEqual( $deleteGroup['result'], 1 );
    }

    /**
     * Group with custom data 
     * ( will do this, once custom * v2 api are ready 
         with all changed schema for custom data  )
     */
    function testGroupDeleteWithCustomData( )
    {         
        
    }
    function tearDown( ) 
    {
        
    }
}

