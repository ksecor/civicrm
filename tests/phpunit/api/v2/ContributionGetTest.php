<?php

require_once 'api/v2/Contribute.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_ContributionGetTest extends CiviUnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    
    
    protected $_contribution;    
    protected $_contributionId;   
    protected $_contributionTypeId;     
    protected $_individualId;        
        
        
    function setUp() 
    {
        parent::setUp();
        
        $this->_contributionTypeId = $this->contributionTypeCreate();
        $this->_individualId = $this->individualCreate();
        $this->_contributionId = $this->contributionCreate($this->_individualId,$this->_contributionTypeId);
       
    }
    
    function tearDown() 
    {
        $this->contributionDelete($this->_contributionId);
        $this->contactDelete($this->_individualId);
        $this->contributionTypeDelete($this->_contributionTypeId);
    }
    
    
    function testGetEmptyParamsContribution()
    {
        $params = array();
        $contribution =& civicrm_contribution_get($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'No input parameters present' );
    }
    
    
    function testGetParamsNotArrayContribution()
    {
        $params = 'domain_id= 1';                            
        $contribution =& civicrm_contribution_get($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'Input parameters is not an array' );
    }
 

    function testGetContribution()
    {        
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $this->_individualId,
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 100.00,
                        'contribution_type_id'   => $this->_contributionTypeId,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 10.00,
                        'fee_amount'             => 51.00,
                        'net_amount'             => 91.00,
                        'trxn_id'                => 23456,
                        'invoice_id'             => 78910,
                        'source'                 => 'SSF',
                        'contribution_status_id' => 1,
                        //    'note'                   => 'Donating for Nobel Cause'#FIXME#
                        );
        
        $this->_contribution =& civicrm_contribution_add($params);
        $params = array('contribution_id'=>$this->_contribution['id']);        
        $contribution =& civicrm_contribution_get($params);
        $this->assertEquals($contribution['contact_id'],$this->_individualId); 
        $this->assertEquals($contribution['total_amount'],100.00);
        $this->assertEquals($contribution['contribution_type_id'],$this->_contributionTypeId);        
        $this->assertEquals($contribution['non_deductible_amount'],10.00);
        $this->assertEquals($contribution['fee_amount'],51.00);
        $this->assertEquals($contribution['net_amount'],91.00);
        $this->assertEquals($contribution['trxn_id'],23456);
        $this->assertEquals($contribution['invoice_id'],78910);
        $this->assertEquals($contribution['contribution_source'],'SSF');
        $this->assertEquals($contribution['contribution_status_id'], 'Completed' );
       
        $params1 = array('contribution_id' =>$this->_contributionId);
        $contribution1 =& civicrm_contribution_get($params1);
        $this->assertEquals($contribution1['contact_id'],$this->_individualId); 
        $this->assertEquals($contribution1['total_amount'],100.00);
        $this->assertEquals($contribution1['contribution_type_id'],$this->_contributionTypeId);        
        $this->assertEquals($contribution1['non_deductible_amount'],10.00);
        $this->assertEquals($contribution1['fee_amount'],50.00);
        $this->assertEquals($contribution1['net_amount'],90.00);
        $this->assertEquals($contribution1['trxn_id'],12345);
        $this->assertEquals($contribution1['invoice_id'],67890);
        $this->assertEquals($contribution1['contribution_source'],'SSF');
        $this->assertEquals($contribution1['contribution_status_id'], 'Completed' );  
        
        $params2 = array( 'contribution_id' => $this->_contribution['id'] );
        $contribution2 =& civicrm_contribution_delete( $params2 );
        $this->assertEquals($contribution2['is_error'], 0);
        $this->assertEquals($contribution2['result'], 1);
        
    }
 
}

