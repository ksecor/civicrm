<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        parent::setUp();
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
     * @todo Implement testGet().
     */
    public function testGet()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
        
    /**
     * Test civicrm_group_organization_get witgh empty params.
     */
    public function testGetWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_get($params);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

///////////////// civicrm_group_organization_create methods

    /**
     * @todo Implement testCreate().
     */
    public function testCreate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }    

    /**
     * @todo Implement testCreateWithEmptyParams().
     */
    public function testCreateWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_create($params);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

///////////////// civicrm_group_organization_remove methods

    /**
     * @todo Implement testRemove().
     */
    public function testRemove()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }


    /**
     * Attempt calling _remove with empty params
     */
    public function testRemoveWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_remove($params);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }
}
?>
