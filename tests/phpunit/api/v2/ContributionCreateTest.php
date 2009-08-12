<?php

require_once 'api/v2/Contribute.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_ContributionCreateTest extends CiviUnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    protected $_individualId;    
    protected $_contribution;
    protected $_contributionTypeId;
    //protected $_customGroupId;
    //protected $_customFieldId;
    
    function setUp() 
    {
        parent::setUp();

        $this->_contributionTypeId = $this->contributionTypeCreate();  
        $this->_individualId = $this->individualCreate();
        //$this->_customGroupId = $this->customGroupCreate('Contribution','C1');
        //$this->_customFieldId = $this->customFieldCreate($this->_customGroupId,'F1');
    }
    
    function tearDown() 
    {
        //$this->customFieldDelete($this->_customFieldId);
        //$this->customGroupDelete($this->_customGroupId);
        $this->contactDelete($this->_individualId);
        $this->contributionTypeDelete($this->_contributionTypeId);
    }
    
     
    function BROKEN_testCreateEmptyParamsContribution()
    {
        $params = array();
        $contribution =& civicrm_contribution_add($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'No input parameters present' );
    }
    

    function BROKEN_testCreateParamsNotArrayContribution()
    {
        $params = 'domain_id= 1';                            
        $contribution =& civicrm_contribution_add($params);
        $this->assertEquals( $contribution['is_error'], 1 );
        $this->assertEquals( $contribution['error_message'], 'Input parameters is not an array' );
    }
    
    
    function BROKEN_testCreateContribution()
    {
        //$customField = 'custom_' . $this->_customFieldId;
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $this->_individualId,                              
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 100.00,
                        'contribution_type_id'   => $this->_contributionTypeId,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 10.00,
                        'fee_amount'             => 50.00,
                        'net_amount'             => 90.00,
                        'trxn_id'                => 12345,
                        'invoice_id'             => 67890,
                        'source'                 => 'SSF',
                        'contribution_status_id' => 1,
                        //'note'                   => 'Donating for Nobel Cause',
                        $customField             => 'Custom Data for Contribution'
                        );
        
        $contribution =& civicrm_contribution_add($params);
        
        $this->assertEquals($contribution['domain_id'], 1);
        $this->assertEquals($contribution['contact_id'], $this->_individualId);                              
        $this->assertEquals($contribution['receive_date'],date('Ymd'));
        $this->assertEquals($contribution['total_amount'],100.00);
        $this->assertEquals($contribution['contribution_type_id'],$this->_contributionTypeId);
        $this->assertEquals($contribution['payment_instrument_id'],1);
        $this->assertEquals($contribution['non_deductible_amount'],10.00);
        $this->assertEquals($contribution['fee_amount'],50.00);
        $this->assertEquals($contribution['net_amount'],90.00);
        $this->assertEquals($contribution['trxn_id'],12345);
        $this->assertEquals($contribution['invoice_id'],67890);
        $this->assertEquals($contribution['source'],'SSF');
        $this->assertEquals($contribution['contribution_status_id'], 1);
        $this->_contribution = $contribution;

        $contributionID = array( 'contribution_id' => $contribution['id'] );
        $contribution   =& civicrm_contribution_delete($contributionID);
        
        $this->assertEquals( $contribution['is_error'], 0 );
        $this->assertEquals( $contribution['result'], 1 );
    }
    
    
    //To Update Contribution
    function BROKEN_testCreateUpdateContribution()
    {
        $params = array(
                        'domain_id'              => 1,
                        'contribution_id'        => $this->_contribution['id'],
                        'contact_id'             => $this->_individualId,                              
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 110.00,
                        'contribution_type_id'   => $this->_contributionTypeId,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 20.00,
                        'fee_amount'             => 60.00,
                        'net_amount'             => 100.00,
                        'trxn_id'                => 23456,
                        'invoice_id'             => 78901,
                        'source'                 => 'WORLD',
                        'contribution_status_id' => 1,
                        'note'                   => 'Donating for Nobel Cause',
                        );
        
        $contribution =& civicrm_contribution_add($params);
        $this->assertEquals($contribution['domain_id'], 1);
        $this->assertEquals($contribution['contact_id'], $this->_individualId);                              
        $this->assertEquals($contribution['receive_date'],date('Ymd'));
        $this->assertEquals($contribution['total_amount'],110.00);
        $this->assertEquals($contribution['contribution_type_id'],$this->_contributionTypeId);
        $this->assertEquals($contribution['payment_instrument_id'],1);
        $this->assertEquals($contribution['non_deductible_amount'],20.00);
        $this->assertEquals($contribution['fee_amount'],60.00);
        $this->assertEquals($contribution['net_amount'],100.00);
        $this->assertEquals($contribution['trxn_id'],23456);
        $this->assertEquals($contribution['invoice_id'],78901);
        $this->assertEquals($contribution['source'],'WORLD');
        $this->assertEquals($contribution['contribution_status_id'],  1);
        $this->_contribution = $contribution;
        
        $contributionID = array( 'contribution_id' => $contribution['id'] );
        $contribution   =& civicrm_contribution_delete($contributionID);
        
        $this->assertEquals( $contribution['is_error'], 0 );
        $this->assertEquals( $contribution['result'], 1 );
    }
   
}

