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
require_once 'api/v2/UFGroup.php';
require_once 'api/v2/UFJoin.php';

/**
 * Test class for UFGroup API - civicrm_uf_*
 * @todo Split UFGroup and UFJoin tests
 *
 *  @package   CiviCRM
 */
class api_v2_UFGroupTest extends CiviUnitTestCase
{

    protected $_ufGroupId;
    protected $_ufFieldId;
    protected $_individualID;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        parent::setUp();

        $params = array(
            'group_type' => 'Contact',
            'title'      => 'Test Profile',
            'help_pre'   => 'Profle to Test API',
            'is_active'  => 1,
        );

        $ufGroup = civicrm_uf_group_create($params);
        $this->_ufGroupId = $ufGroup['id'];
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        if ($this->_ufFieldId) {
            civicrm_uf_field_delete($this->_ufFieldId);
            $this->_ufFieldId = NULL;
        }

        if ($this->_ufGroupId) {
            civicrm_uf_group_delete($this->_ufGroupId);
            $this->_ufGroupId = NULL;
        }

        if ($this->_individualID) {
            $this->contactDelete($this->_individualID);
            $this->_individualID = NULL;
        }
    }

    /**
     * fetch profile title by its id	
     */
    public function testGetUFProfileTitle()
    {
        $ufProfile = civicrm_uf_profile_title_get($this->_ufGroupId);
        $this->assertEquals($ufProfile, 'Test Profile');
        $this->assertEquals(count($ufProfile), 1);
    }

    /**
     * fetch profile html by contact id and profile title
     */
    public function testGetUFProfileHTML()
    {
        $this->_individualID = $this->individualCreate();
        $profileHTML         = civicrm_uf_profile_html_get($this->_individualID, 'Test Profile');
        $this->assertNotNull($profileHTML);
    }


    /**
     * fetch profile html by contact id and profile id
     */
    public function testGetUFProfileHTMLById()
    {
        $this->markTestSkipped('Throws Fatal error: Class name must be a valid object or a string');
        $this->_individualID = $this->individualCreate();
        $profileHTML         = civicrm_uf_profile_html_by_id_get($this->_individualID, $this->_ufGroupId);
        $this->assertNotNull($profileHTML);
    }


    /**
     * fetch profile html with group id
     */
    public function testGetUFProfileCreateHTML()
    {
        $this->markTestSkipped('Throws Fatal error: Class name must be a valid object or a string');
        $fieldsParams = array(
            'field_name'       => 'first_name',
            'field_type'       => 'Individual',
            'visibility'       => 'Public Pages and Listings',
            'weight'           => 1,
            'location_type_id' => 1,
            'label'            => 'Test First Name',
            'is_searchable'    => 1,
            'is_active'        => 1,
        );
        $ufField    = civicrm_uf_field_create($this->_ufGroupId, $fieldsParams);

        $joinParams =  array(
            'is_active'   => 1,
            'module'      => 'Profile',
            'weight'      => 1,
            'uf_group_id' => $this->_ufGroupId,
        );
        $ufJoin = civicrm_uf_join_edit($joinParams);

        $profileHTML = civicrm_uf_create_html_get($this->_ufGroupId, true);
        $this->assertNotNull($profileHTML);
    }


    /**
     * creating profile fields / fetch profile fields
     */
    public function testGetUFProfileFields()
    {
        $params = array(
            'field_name'       => 'country',
            'field_type'       => 'Contact',
            'visibility'       => 'Public Pages and Listings',
            'weight'           => 1,
            'location_type_id' => 1,
            'label'            => 'Test Country',
            'is_searchable'    => 1,
            'is_active'        => 1,
        );

        $ufField          = civicrm_uf_field_create($this->_ufGroupId, $params);
        $this->_ufFieldId = $ufField['id'];

        foreach ($params as $key => $value) {
            $this->assertEquals($ufField[$key], $params[$key]);
        }

        $ufProfile = civicrm_uf_profile_fields_get($this->_ufGroupId);
        $this->assertEquals($ufProfile['country-1']['field_type'],       $params['field_type']);
        $this->assertEquals($ufProfile['country-1']['title'],            $params['label']);
        $this->assertEquals($ufProfile['country-1']['visibility'],       $params['visibility']);
        $this->assertEquals($ufProfile['country-1']['location_type_id'], $params['location_type_id']);
        $this->assertEquals($ufProfile['country-1']['group_id'],         $this->_ufGroupId);
        $this->assertEquals($ufProfile['country-1']['groupTitle'],       'Test Profile');
        $this->assertEquals($ufProfile['country-1']['groupHelpPre'],     'Profle to Test API');
    }


    /**
     * fetch contact id by uf id
     */
    public function testGetUFMatchID()
    {
        $session   =& CRM_Core_Session::singleton();
        $ufId      = $session->get('ufID');
        $ufMatchId = civicrm_uf_match_id_get($ufId);
        $this->assertEquals($ufMatchId, $session->get('userID'));
    }


    /**
     * fetch uf id by contact id
     */
    public function testGetUFID()
    {
        $session    =& CRM_Core_Session::singleton();
        $userId     = $session->get('userID');
        $ufIdFetced = civicrm_uf_id_get($userId);
        $this->assertEquals($ufIdFetced, $session->get('ufID'));
    }

    /**
     * updating group
     */
    public function testUpdateUFGroup()
    {
        $params = array(
            'title'     => 'Edited Test Profile',
            'help_post' => 'Profile Pro help text.',
            'is_active' => 1,
        );

        $updatedGroup = civicrm_uf_group_update($params, $this->_ufGroupId);
        foreach ($params as $key => $value) {
            $this->assertEquals($updatedGroup[$key], $params[$key]);
        }
    }


    /**
     * create / updating field
     */
    public function testCreateUFField()
    {
        $params = array(
            'field_name'       => 'country',
            'field_type'       => 'Contact',
            'visibility'       => 'Public Pages and Listings',
            'weight'           => 1,
            'location_type_id' => 1,
            'label'            => 'Test Country',
            'is_searchable'    => 1,
            'is_active'        => 1,
        );
        $ufField          = civicrm_uf_field_create($this->_ufGroupId, $params);
        $this->_ufFieldId = $ufField['id'];
        foreach ($params as $key => $value) {
            $this->assertEquals($ufField[$key], $params[$key]);
        }

        $params = array(
            'field_name'       => 'country',
            'label'            => 'Edited Test Country',
            'location_type_id' => 1,
            'weight'           => 1,
            'is_active'        => 1,
        );

        $updatedField = civicrm_uf_field_update($params,$ufField['id']);
        foreach ($params as $key => $value) {
            $this->assertEquals($updatedField[$key], $params[$key]);
        }
    }


    /**
     * deleting field
     */
    public function testDeleteUFField()
    {
        $params = array(
            'field_name'       => 'country',
            'field_type'       => 'Contact',
            'visibility'       => 'Public Pages and Listings',
            'weight'           => 1,
            'location_type_id' => 1,
            'label'            => 'Test Country',
            'is_searchable'    => 1,
            'is_active'        => 1,
        );
        $ufField          = civicrm_uf_field_create($this->_ufGroupId, $params);
        $this->_ufFieldId = $ufField['id'];
        foreach ($params as $key => $value) {
            $this->assertEquals($ufField[$key], $params[$key]);
        }
        $result = civicrm_uf_field_delete($ufField['id']);
        $this->assertEquals($result, 1);
    }


    /**
     * validate profile html
     */
    public function testValidateProfileHTML()
    {
        $this->markTestSkipped('Throws Fatal error: Class name must be a valid object or a string');
        $this->_individualID = $this->individualCreate();
        $result              = civicrm_profile_html_validate($this->_individualID, 'Test Profile');
        $this->assertEquals($result, 1);
    }


    /**
     * create/update uf join
     */
    public function testEditUFJoin()
    {
        $params =  array(
            'module'       => 'CiviContribute',
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
            'weight'       => 1,
            'uf_group_id'  => $this->_ufGroupId,
            'is_active'    => 1,
        );
        $ufJoin = civicrm_uf_join_edit($params);
        foreach ($params as $key => $value) {
            $this->assertEquals($ufJoin[$key], $params[$key]);
        }
        $params =  array(
            'id'           => $ufJoin['id'],
            'module'       => 'CiviContribute',
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
            'weight'       => 1,
            'uf_group_id'  => $this->_ufGroupId,
            'is_active'    => 0,
        );
        $ufJoinUpdated = civicrm_uf_join_edit($params);
        foreach ($params as $key => $value) {
            $this->assertEquals($ufJoinUpdated[$key], $params[$key]);
        }
    }


    /**
     * find uf join id
     */
    public function testFindUFJoinId()
    {
        $params = array(
            'module'       => 'CiviContribute',
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
            'weight'       => 1,
            'uf_group_id'  => $this->_ufGroupId,
            'is_active'    => 1,
        );
        $ufJoin       = civicrm_uf_join_add($params);
        $searchParams = array(
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
        );
        $ufJoinId = civicrm_uf_join_id_find($searchParams);
        $this->assertEquals($ufJoinId, $ufJoin['id']);
    }


    /**
     * find uf join group id
     */
    public function testFindUFGroupId()
    {
        $params =  array(
            'module'       => 'CiviContribute',
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
            'weight'       => 1,
            'uf_group_id'  => $this->_ufGroupId,
            'is_active'    => 1,
        );
        $ufJoin       = civicrm_uf_join_add($params);
        $searchParams = array(
            'entity_table' => 'civicrm_contribution_page',
            'entity_id'    => 1,
        );
        $ufGroupId = civicrm_uf_join_UFGroupId_find($searchParams);
        $this->assertEquals($ufGroupId, $this->_ufGroupId);
    }


    /**
     * fetch all profiles
     */
    public function testGetUFProfileGroups()
    {
        $ufProfileGroup = civicrm_uf_profile_groups_get();
        $this->assertNotNull(count($ufProfileGroup));
    }
}
