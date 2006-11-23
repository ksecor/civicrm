<?php

require_once 'api/crm.php';

class TestOfUpdateContribution extends UnitTestCase 
{
    protected $_individual   = array();
    protected $_contribution  = array();

    function testCreateIndividual() 
    {
        $params = array(
                        'first_name' => 'Apoorva',
                        'last_name'  => 'Mehta'
                        );
        $contact =& crm_create_contact($params, 'Individual');
        $this->assertIsA($contact, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($contact->contact_type, 'Individual');
        $this->_individual = $contact;
    }    


    function testCreateContribution()
    {
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $this->_individual->id, 
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
        $contribution = crm_create_contribution($params);      
        $this->_contribution = $contribution;
    }

    
    function testUpdateBadContributionWrongParam()
    {
        $params =  array('contact_id' => -123);
        $contribution = crm_update_contribution(& $this->_contribution,$params);
        $this->assertIsA($contribution,'CRM_Core_Error');
    }

    function testUpdateContribution()
    {
        $params = array(
                        'domain_id'              => 1,
                        'contact_id'             => $this->_individual->id, 
                        'receive_date'           => date('Ymd'),
                        'total_amount'           => 200.00,
                        'contribution_type_id'   => 1,
                        'payment_instrument_id'  => 3,
                        'non_deductible_amount'  => 100.00,
                        'fee_amount'             => 500.00,
                        'net_amount'             => 900.00,
                        'trxn_id'                => 67890,
                        'invoice_id'             => 12345,
                        'source'                 => 'UNESCO',
                        'contribution_status_id' => 1
                        );
        $contribution = crm_update_contribution(& $this->_contribution, $params);
        $this->assertIsA($contribution,'CRM_Contribute_BAO_Contribution' );
        $this->assertEqual($contribution->domain_id, 1);
        $this->assertEqual($contribution->contact_id, $this->_individual->id);                              
        $this->assertEqual($contribution->receive_date,date('Ymd'));
        $this->assertEqual($contribution->total_amount,200.00);
        $this->assertEqual($contribution->contribution_type_id,1);
        $this->assertEqual($contribution->payment_instrument_id,3);
        $this->assertEqual($contribution->non_deductible_amount,100.00);
        $this->assertEqual($contribution->fee_amount,500.00);
        $this->assertEqual($contribution->net_amount,900.00);
        $this->assertEqual($contribution->trxn_id,67890);
        $this->assertEqual($contribution->invoice_id,12345);
        $this->assertEqual($contribution->source,'UNESCO');
        $this->assertEqual($contribution->contribution_status_id,  1);
    }

    function testDeleteContribution()
    {
        $val =& crm_delete_contribution( $this->_contribution );
        $this->assertNull($val);
    }

    function testDeleteIndividual() 
    {
        $contact = $this->_individual;
        $val =& crm_delete_contact(& $contact,102);
        $this->assertNull($val);        
    }
}

?>