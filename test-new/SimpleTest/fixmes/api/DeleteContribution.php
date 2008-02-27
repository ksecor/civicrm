<?php

require_once 'api/crm.php';

class TestOfDeleteContributionAPI extends UnitTestCase 
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
                        'contribution_status_id' => 1,
                        'note'                   =>'SSS'  
                        );
        $contribution = crm_create_contribution($params);       
        $this->_contribution = $contribution;
    }


    function testBadDeleteContributionEmptyParams()
    {
        $params = array();
        $contribution = crm_create_contribution($params);
        $this->assertIsA($contribution,'CRM_Core_Error' );        
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
