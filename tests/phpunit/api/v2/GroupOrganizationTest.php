<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'api/v2/GroupOrganization.php';

/**
 * Test class for GroupOrganization API - civicrm_group_organization_*
 *
 *  @package   CiviCRM
 */
class api_v2_GroupOrganizationTest extends CiviUnitTestCase
{

    function get_info( )
    {
        return array(
                     'name'        => 'Group Organization',
                     'description' => 'Test all Group Organization API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_groupID = $this->groupCreate();

        $this->_orgID   = $this->organizationCreate( );

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    ///////////////// civicrm_group_organization_get methods

    /**
     * Test civicrm_group_organization_get with valid params.
     */
    public function testGroupOrganizationGet()
    {

        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID
                         );
        $result =& civicrm_group_organization_create( $params );

        $paramsGet = array( 'organization_id' => $result['result']['organization_id']  );
        
        $result    = civicrm_group_organization_get($paramsGet);
        $this->assertEquals( $result['is_error'], 0);
    }
        
    /**
     * Test civicrm_group_organization_get with empty params.
     */
    public function testGroupOrganizationGetWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_get($params);

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     * Test civicrm_group_organization_get with wrong params.
     */
    public function testGroupOrganizationGetWithWrongParams()
    {
        $params = 'groupOrg';
        $result =& civicrm_group_organization_get($params);

        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );
    }

    ///////////////// civicrm_group_organization_create methods

    /**
     * check with valid params
     */
    public function testGroupOrganizationCreate()
    {
        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID
                         );
        $result =& civicrm_group_organization_create($params);

        $this->assertEquals( $result['is_error'], 0);
    }    

    /**
     * check with empty params array
     */
    public function testGroupOrganizationCreateWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_create($params);

        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     * check with invalid params
     */
    public function testGroupOrganizationCreateParamsNotArray()
    {
        $params = 'group_org';
        $result =& civicrm_group_organization_create( $params );

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );
    }


    ///////////////// civicrm_group_organization_remove methods

    /**
     *  Test civicrm_group_organization_remove with params not an array.
     */
    public function testGroupOrganizationRemoveParamsNotArray()
    {
        $params = 'delete';
        $result =& civicrm_group_organization_remove($params);
        
        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );

    }


    /**
     * Test civicrm_group_organization_remove with empty params.
     */
    public function testGroupOrganizationRemoveWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_remove($params);

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     *  Test civicrm_group_organization_remove with valid params.
     */
    public function break_testGroupOrganizationRemove()
    {
        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID
                         );
        $result =& civicrm_group_organization_create( $params );
        
        $paramsDelete = array( 'id' => $result['result']['id']  );
        $result =& civicrm_group_organization_remove( $paramsDelete );

        $this->assertEquals( $result['is_error'], 0 );

    }


}
?>
