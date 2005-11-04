<?php

require_once 'api/crm.php';

class TestOfDeleteEntityTagAPI extends UnitTestCase 
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

    function testDeleteEntityTag()
    {
        /*        $params = array('name'    => 'Manish2',
                        'description' => 'Zope',
                        'parent_id' => 'Null',
                        'domain_id' => '1'
                        );
        $tag=& crm_create_tag($params);
        */
/*      require_once 'CRM/Core/BAO/Tag.php';
       $params = array('contact_id' => 83
                       );
       $defaults = array();
       $tag=& CRM_Core_BAO_Tag::retrieve($params,$defaults);
*/

//        $params1  = array('contact_id' => 79 );
//        $entity  = crm_get_contact($params1);
/*         $entity = array('id'    => 'Manish1',
                        'description' => 'Zope',
                        'parent_id' => 'Null',
                        'domain_id' => '1'
*/
        require_once 'CRM/Core/DAO/Tag.php';
        require_once 'CRM/Core/BAO/EntityTag.php';
        $tag = & new CRM_Core_DAO_Tag();
        $tag->id = 1 ;

        $params1  = array('contact_id' => 2 );
        $entity  = crm_get_contact($params1);

        // $entity_tag=& crm_create_entity_tag($tag,$entity);
        $entityTag =& new CRM_Core_BAO_EntityTag( );
        $entityTag->id = 5;
        crm_delete_entity_tag($entityTag);
         
    }
}
?>