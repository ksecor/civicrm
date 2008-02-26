<?php
require_once 'api/UFJoin.php';

class TestOfUFJoinEditAPIV1 extends CiviUnitTestCase 
{
    protected $_ufjoinparams;
    protected $_ufjoin;
    
    function setup( )
    {
      $this->_ufJoin = $this->ufjoinCreate( );
      
    }

    function tearDown( ) 
    {

        $this->ufjoinDelete( $this->_ufjoinparams );
        
    }
    
    function testEditUFJoinWithEmptyParams( )
    {
        $params = array( );
        $result = crm_edit_uf_join( $ufJoin, $params );
                 
        $this->assertTrue(array_key_exists( '_errors',$result ) );
        $this->assertEqual($result->_errors['0']['message'] , 'params is an empty array');
    }    

    function testEditUFJoinWithParamsNotArray( )
    {  
        $params = 'test';
        $result = crm_edit_uf_join( $this->_ufJoin ,$params ); 
        
        $this->assertTrue( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->_errors['0']['message'] , 'params is not an array' );
    }    
    
    function testEditUFJoinWithWrongUfObject( )
    {
        $ufJoin = array( );
        $params = array( );
        $params = array(
                            'is_active'    => 1,
                            'module'       => 'CiviMember',
                            'entity_table' => 'civicrm_membership',
                            'entity_id'    => 3,
                            'weight'       => 1,
                            );
        
        $result = crm_edit_uf_join( $ufJoin,$params );
        
        $this->assertTrue( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->_errors['0']['message'] , '$ufJoin is not a valid object' );
    }
    
    function testUFJoinEdit( )
    {
        $params = array(
                        'is_active'    => 1,
                        'module'       => 'CiviContribute',
                        'entity_table' => 'civicrm_contribution_page',
                        'entity_id'    => 2,
                        'weight'       => 1,
                        'uf_group_id'  => 1,
                        );
        
        $result = crm_edit_uf_join( $this->_ufJoin,$params );
             
        $this->assertFalse( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->is_active, 1 );
        $this->assertEqual( $result->module,'CiviContribute' );
        $this->assertEqual( $result->entity_table, 'civicrm_contribution_page' );
        $this->assertEqual( $result->weight, 1 );
        $this->assertEqual( $result->uf_group_id, 1 );
        $this->_ufjoinID       = $result->id;
        $params['uf_group_id'] = '';
        $this->_ufjoinparams   = $params;

    }    
    
}



?>