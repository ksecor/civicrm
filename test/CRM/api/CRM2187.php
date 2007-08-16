<?php

require_once 'api/crm.php';

class TestOfCRM2187 extends UnitTestCase 
{
    protected $_membership   = array();
           
    function setUp( ) 
    {
    }
    
    function tearDown( ) 
    {
    }
    
    function testCreateMembership( )
    {
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2007-03-01',
                        'start_date'         => '2007-03-01',
                        'end_date'           => '2008-03-01',
                        'source'             => 'Payment',
                        'is_override'        => '1', 
                        'status_id'          => '2'                       
                        );
        
        $this->_membership = & crm_create_contact_membership( $params, '102' );    
    }
    
    function testUpdateMembership( )
    {
        $params = array(
                        'id'                 => $this->_membership['id'],
                        'membership_type_id' => '2',
                        'join_date'          => '2007-01-30',
                        'start_date'         => '2007-01-30',
                        'end_date'           => '2007-12-30',
                        'source'             => 'Donation',
                        'status_id'          => '2'                       
                        );	
        $membership = & crm_update_contact_membership( $params );        
        $this->assertEqual( $membership['id'],$this->_membership['id'] );
        $this->assertEqual( $membership['membership_type_id'],'2' );
        $this->assertEqual( $membership['join_date'],'20070130' );
        $this->assertEqual( $membership['status_id' ],'2' );       
        $this->_membership = $membership ;
    }
}
?>