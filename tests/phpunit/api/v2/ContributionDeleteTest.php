<?php

require_once 'api/v2/Contribute.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_ContributionDeleteTest extends CiviUnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    protected $_individualId;
    protected $_contribution;
    protected $_contributionTypeId;
    
    function setUp() 
    {
        parent::setUp();
        
        $this->_contributionTypeId = $this->contributionTypeCreate();
        $this->_individualId       = $this->individualCreate();
             
    }
    
    
    function testDeleteEmptyParamsContribution()
    {
        $params = array( );
        $contribution = civicrm_contribution_delete($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'Could not find contribution_id in input parameters' );
    }
    
    
    function testDeleteParamsNotArrayContribution()
    {
        $params = 'contribution_id= 1';                            
        $contribution = civicrm_contribution_delete($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'Could not find contribution_id in input parameters' );
    }

     
    function testDeleteWrongParamContribution()
    {
        $params = array( 'contribution_source' => 'SSF' );
        $contribution =& civicrm_contribution_delete( $params );
        $this->assertEquals($contribution['is_error'], 1);
        $this->assertEquals( $contribution['error_message'], 'Could not find contribution_id in input parameters' );
    }
    
    
    function testDeleteContribution()
    {
        $contributionID = $this->contributionCreate( $this->_individualId , $this->_contributionTypeId );
             
        $params         = array( 'contribution_id' => $contributionID );                            
        $contribution   = civicrm_contribution_delete( $params );
        $this->assertEquals( $contribution['is_error'], 0 );
        $this->assertEquals( $contribution['result'], 1 );
    }
    
    function tearDown() 
    {
        $this->contributionTypeDelete($this->_contributionTypeId);
        $this->contactDelete($this->_individualId);
            
    }    
}

