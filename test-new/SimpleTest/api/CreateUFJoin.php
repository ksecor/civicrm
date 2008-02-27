<?php
require_once 'api/UFJoin.php';

class TestOfUFJoinCreateAPIV1 extends CiviUnitTestCase 
{
    protected $_ufjoinID;
    protected $_ufjoinparams;
    
    
    function tearDown() 
    {
        if ( $this->_ufjoinID ) {
            $this->ufjoinDelete( $this->_ufjoinparams );
        }
    }
    
    function testCreateUFJoinWithEmptyParams( )
    {
        $params = array( );
        $result = crm_add_uf_join( $params );
            
        $this->assertTrue(array_key_exists( '_errors',$result ) );
        $this->assertEqual($result->_errors['0']['message'] , 'params is an empty array');
    }    

    function testCreateUFJoinWithParamsNotArray( )
    {
        $params = 'test';
        $result = crm_add_uf_join( $params ); 
        
        $this->assertTrue( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->_errors['0']['message'] , 'params is not an array' );
    }    
    
    function testCreateUFJoinWithoutUFGroupId( )
    {
        $params = array( );
        $params = array(
                            'is_active'    => 1,
                            'module'       => 'CiviEvent',
                            'entity_table' => 'civicrm_event',
                            'entity_id'    => 3,
                            'weight'       => 1,
                            );
        
        $result = crm_add_uf_join( $params );
        
        $this->assertTrue( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->_errors['0']['message'] , 'uf_group_id is required field' );
    }
    
    function testUFJoinCreate( )
    {
        $params = array(
                        'is_active'    => 1,
                        'module'       => 'CiviEvent',
                        'entity_table' => 'civicrm_event',
                        'entity_id'    => 3,
                        'weight'       => 1,
                        'uf_group_id'  => 1,
                        );
        
        $result = crm_add_uf_join( $params );
        
        $this->assertFalse( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->is_active, 1 );
        $this->assertEqual( $result->module,'CiviEvent' );
        $this->assertEqual( $result->entity_table, 'civicrm_event' );
        $this->assertEqual( $result->weight, 1 );
        $this->assertEqual( $result->uf_group_id, 1 );
        $this->_ufjoinID       = $result->id;
        $params['uf_group_id'] = '';
        $this->_ufjoinparams   = $params;

    }    
    
}



?>