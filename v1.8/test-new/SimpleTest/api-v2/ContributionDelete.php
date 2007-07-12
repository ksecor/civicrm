<?php

require_once 'api/v2/Contribute.php';

class TestOfContributionDeleteAPIV2 extends CiviUnitTestCase 
{
    /**
     * Assume empty database with just civicrm_data
     */
    protected $_individualId;
    protected $_contribution;
    protected $_contributionTypeId;
    
    function setUp() 
    {
        $this->_contributionTypeId = $this->contributionTypeCreate();  
        $this->_individualId = $this->individualCreate();
    }
    
    function tearDown() 
    {
        $this->contactDelete($this->_individualId);
        $this->contributionTypeDelete($this->_contributionTypeId);
    }    
   
   
    function testCreateContribution()
    {
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
                        'note'                   => 'Donating for Nobel Cause',
                        'return.contact_id'      => 1
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
        $this->assertEqual($contribution['contribution_status_id'],  1);
        $this->_contribution = $contribution;
    }


    function testDeleteEmptyParamsContribution()
    {
        $params = array();
        $contribution =& civicrm_contribution_delete($params);
        $this->assertEqual( $contribution['is_error'], 1 );
    }


    function testDeleteParamsNotArrayContribution()
    {
        $params = 'contribution_id= 1';                            
        $contribution =& civicrm_contribution_delete($params);
        $this->assertEqual( $contribution['is_error'], 1 );
    }

     
    function testDeleteWrongParamContribution()
    {
        $params = array( 'contribution_source' => 'SSF' );
        $val =& civicrm_contribution_delete( $params );
        $this->assertEqual($val['is_error'], 1);
    }


    function testDeleteContribution()
    {
        $params = array( 'contribution_id' => $this->_contribution['id'] );                            
        $contribution =& civicrm_contribution_delete($params);
        $this->assertEqual( $contribution['is_error'], 0 );
    }
}
?>
