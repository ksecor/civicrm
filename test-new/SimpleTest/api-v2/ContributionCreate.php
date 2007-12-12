<?php

require_once 'api/v2/Contribute.php';

class TestOfContributionCreateAPIV2 extends CiviUnitTestCase 
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
    
     
    function testCreateEmptyParamsContribution()
    {
        $params = array();
        $contribution =& civicrm_contribution_add($params);
        $this->assertEqual( $contribution['is_error'], 1 );
        $this->assertEqual( $contribution['error_message'], 'No input parameters present' );
    }
    

    function testCreateParamsNotArrayContribution()
    {
        $params = 'domain_id= 1';                            
        $contribution =& civicrm_contribution_add($params);
        $this->assertEqual( $contribution['is_error'], 1 );
        $this->assertEqual( $contribution['error_message'], 'Input parameters is not an array' );
    }
    
    
    function testCreateContribution()
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
        
        $this->assertEqual($contribution['domain_id'], 1);
        $this->assertEqual($contribution['contact_id'], $this->_individualId);                              
        $this->assertEqual($contribution['receive_date'],date('Ymd'));
        $this->assertEqual($contribution['total_amount'],100.00);
        $this->assertEqual($contribution['contribution_type_id'],$this->_contributionTypeId);
        $this->assertEqual($contribution['payment_instrument_id'],1);
        $this->assertEqual($contribution['non_deductible_amount'],10.00);
        $this->assertEqual($contribution['fee_amount'],50.00);
        $this->assertEqual($contribution['net_amount'],90.00);
        $this->assertEqual($contribution['trxn_id'],12345);
        $this->assertEqual($contribution['invoice_id'],67890);
        $this->assertEqual($contribution['source'],'SSF');
        $this->assertEqual($contribution['contribution_status_id'], 1);
        $this->_contribution = $contribution;

        $contributionID = array( 'contribution_id' => $contribution['id'] );
        $contribution   =& civicrm_contribution_delete($contributionID);
        
        $this->assertEqual( $contribution['is_error'], 0 );
        $this->assertEqual( $contribution['result'], 1 );
    }
    
    
    //To Update Contribution
    function testCreateUpdateContribution()
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
        $this->assertEqual($contribution['domain_id'], 1);
        $this->assertEqual($contribution['contact_id'], $this->_individualId);                              
        $this->assertEqual($contribution['receive_date'],date('Ymd'));
        $this->assertEqual($contribution['total_amount'],110.00);
        $this->assertEqual($contribution['contribution_type_id'],$this->_contributionTypeId);
        $this->assertEqual($contribution['payment_instrument_id'],1);
        $this->assertEqual($contribution['non_deductible_amount'],20.00);
        $this->assertEqual($contribution['fee_amount'],60.00);
        $this->assertEqual($contribution['net_amount'],100.00);
        $this->assertEqual($contribution['trxn_id'],23456);
        $this->assertEqual($contribution['invoice_id'],78901);
        $this->assertEqual($contribution['source'],'WORLD');
        $this->assertEqual($contribution['contribution_status_id'],  1);
        $this->_contribution = $contribution;
        
        $contributionID = array( 'contribution_id' => $contribution['id'] );
        $contribution   =& civicrm_contribution_delete($contributionID);
        
        $this->assertEqual( $contribution['is_error'], 0 );
        $this->assertEqual( $contribution['result'], 1 );
    }
   
}
?>
