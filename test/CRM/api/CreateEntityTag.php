<?php

require_once 'api/crm.php';

class TestOfCreateEntityTagAPI extends UnitTestCase 
{
    protected $_tag;
    //protected $_household;
    //protected $_organization;

    function setUp() 
    {
    }

    function tearDown() 
    {
    }

    /* Test cases for crm_create_location for Individual contact */ 

    function testCreateEntityTag()
    {
        $params = array('name'    => 'Manish1',
                        'description' => 'Zope',
                        'parent_id' => 'Null',
                        'domain_id' => '1'
                        );
        $tag=& crm_create_tag($params);
        
/*      require_once 'CRM/Core/BAO/Tag.php';
       $params = array('contact_id' => 83
                       );
       $defaults = array();
       $tag=& CRM_Core_BAO_Tag::retrieve($params,$defaults);
*/

       $params1  = array('contact_id' => 79 );
       $entity  = crm_get_contact($params1);
       crm_create_entity_tag($tag, $entity);

    }
}
?>