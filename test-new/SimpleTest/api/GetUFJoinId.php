<?php
require_once 'api/UFJoin.php';
require_once 'api/Event.php';

class TestOfGetUFJoinIdAPIV1 extends CiviUnitTestCase 
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
    
    function testGetUFJoinIdWithEmptyParams( )
    {
        $params = array( );
        $result = crm_find_uf_join_id( $params );
                         
        $this->assertTrue(array_key_exists( '_errors',$result ) );
        $this->assertEqual($result->_errors['0']['message'] , 'Array is not valid array');
    }    

    function testGetUFJoinIdWithParamsNotArray( )
    {  
        $params = 'test';
        $result = crm_find_uf_join_id( $params );
                  
        $this->assertTrue( array_key_exists( '_errors',$result ) );
        $this->assertEqual( $result->_errors['0']['message'] , 'test is not valid array' );
    }    
    
       
    function testGetUFJoinId( )
    {
        $params = array(
                        'is_active'    => 1,
                        'module'       => 'CiviEvent',
                        'entity_table' => 'civicrm_event',
                        'entity_id'    => 3,
                        'weight'       => 1,
                        'uf_group_id'  => 1,
                        );
        
        $result = crm_find_uf_join_id( $params );
                          
        $this->assertNotNull( $result );
        $params['uf_group_id'] = '';
        $this->_ufjoinparams   = $params;

    }    
    
}



?>