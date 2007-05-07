<?php

require_once 'api/v2/Contribute.php';
require_once 'api/v2/Contact.php';

class TestOfCreateContribution extends UnitTestCase 
{
    protected $_individual   = array();
    protected $_contribution  = array();

    function setUp() 
    {
        // make sure this is just _41 and _data
    }
    
    function tearDown() 
    {
    }
    
    function testCreateIndividual() 
    {
        $params = array(
                        'first_name'   => 'Apoorva',
                        'last_name'    => 'Mehta',
                        'contact_type' => 'Individual',
                        );
        $contact =& civicrm_contact_add($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_individual = $contact;
    }
    
    function testCreateBadContributionEmptyParams()
    {
        $params = array();
        $contribution = civicrm_contribution_add($params);
        $this->assertEqual( $contribution['is_error'], 1 );
    }
    
    function testCreateBadContributionWithoutContactId()
    {
        $params = array(
                        'domain_id'              => 1,                                                    
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 100.00,
                        'contribution_type_id'   => 3,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 10.00,
                        'fee_amount'             => 50.00,
                        'net_amount'             => 90.00,
                        'trxn_id'                => 12345,
                        'invoice_id'             => 67890,
                        'source'                 => 'SSF',
                        'contribution_status_id' => 1
                        );
        $contribution = civicrm_contribution_add($params);
        $this->assertEqual( $contribution['is_error'], 1 );
    }


    function testCreateContribution()
    {
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $this->_individual['contact_id'],                              
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 101.00,
                        'contribution_type_id'   => 3,
                        'payment_instrument_id'  => 1,
                        'non_deductible_amount'  => 10.00,
                        'fee_amount'             => 50.00,
                        'net_amount'             => 90.00,
                        'trxn_id'                => 12349,
                        'invoice_id'             => 67894,
                        'source'                 => 'SSF',
                        'contribution_status_id' => 1,
                        'note'                   =>'ppp'
                        );

        $contribution = civicrm_contribution_add($params);
        $this->assertNotNull( $contribution['id'] );
        $this->assertEqual($contribution['domain_id'], 1);
        $this->assertEqual($contribution['contact_id'], $this->_individual['id']);                              
        $this->assertEqual($contribution['receive_date'],date('Ymd'));
        $this->assertEqual($contribution['total_amount'],101.00);
        $this->assertEqual($contribution['contribution_type_id'],3);
        $this->assertEqual($contribution['payment_instrument_id'],1);
        $this->assertEqual($contribution['non_deductible_amount'],10.00);
        $this->assertEqual($contribution['fee_amount'],50.00);
        $this->assertEqual($contribution['net_amount'],90.00);
        $this->assertEqual($contribution['trxn_id'],12349);
        $this->assertEqual($contribution['invoice_id'],67894);
        $this->assertEqual($contribution['source'],'SSF');
        $this->assertEqual($contribution['contribution_status_id'],  1);
        $this->_contribution = $contribution;
    }
}

?>
