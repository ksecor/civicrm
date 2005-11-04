<?php

require_once 'api/crm.php';

class TestOfGetEntitiesByTagAPI extends UnitTestCase 
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

    function testGetEntitiesByTag()
    {
        /*$params = array('name'    => 'Manish1',
                        'description' => 'Zope',
                        'parent_id' => 'Null',
                        'domain_id' => '1'
                        );
        $tag=& crm_create_tag($params);*/
        require_once 'CRM/Core/DAO/Tag.php';
        $tag = & new CRM_Core_DAO_Tag();
        $tag->id = 2 ;
/*      require_once 'CRM/Core/BAO/Tag.php';
       $params = array('contact_id' => 83
                       );
       $defaults = array();
       $tag=& CRM_Core_BAO_Tag::retrieve($params,$defaults);
*/

/*       $params1  = array('contact_id' => 79 );
       $entity  = crm_get_contact($params1);
*/
       print_r(crm_get_entities_by_tag($tag,'Household'));

    }
}
?>