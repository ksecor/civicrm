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

        // FIXME: something NULLs $GLOBALS['_HTML_QuickForm_registered_rules'] when the tests are ran all together
        $GLOBALS['_HTML_QuickForm_registered_rules'] = array(
            'required'      => array('html_quickform_rule_required', 'HTML/QuickForm/Rule/Required.php'),
            'maxlength'     => array('html_quickform_rule_range',    'HTML/QuickForm/Rule/Range.php'),
            'minlength'     => array('html_quickform_rule_range',    'HTML/QuickForm/Rule/Range.php'),
            'rangelength'   => array('html_quickform_rule_range',    'HTML/QuickForm/Rule/Range.php'),
            'email'         => array('html_quickform_rule_email',    'HTML/QuickForm/Rule/Email.php'),
            'regex'         => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'lettersonly'   => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'alphanumeric'  => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'numeric'       => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'nopunctuation' => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'nonzero'       => array('html_quickform_rule_regex',    'HTML/QuickForm/Rule/Regex.php'),
            'callback'      => array('html_quickform_rule_callback', 'HTML/QuickForm/Rule/Callback.php'),
            'compare'       => array('html_quickform_rule_compare',  'HTML/QuickForm/Rule/Compare.php')
        );
        // FIXME: …ditto for $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES'] = array(
            'group'         =>array('HTML/QuickForm/group.php','HTML_QuickForm_group'),
            'hidden'        =>array('HTML/QuickForm/hidden.php','HTML_QuickForm_hidden'),
            'reset'         =>array('HTML/QuickForm/reset.php','HTML_QuickForm_reset'),
            'checkbox'      =>array('HTML/QuickForm/checkbox.php','HTML_QuickForm_checkbox'),
            'file'          =>array('HTML/QuickForm/file.php','HTML_QuickForm_file'),
            'image'         =>array('HTML/QuickForm/image.php','HTML_QuickForm_image'),
            'password'      =>array('HTML/QuickForm/password.php','HTML_QuickForm_password'),
            'radio'         =>array('HTML/QuickForm/radio.php','HTML_QuickForm_radio'),
            'button'        =>array('HTML/QuickForm/button.php','HTML_QuickForm_button'),
            'submit'        =>array('HTML/QuickForm/submit.php','HTML_QuickForm_submit'),
            'select'        =>array('HTML/QuickForm/select.php','HTML_QuickForm_select'),
            'hiddenselect'  =>array('HTML/QuickForm/hiddenselect.php','HTML_QuickForm_hiddenselect'),
            'text'          =>array('HTML/QuickForm/text.php','HTML_QuickForm_text'),
            'textarea'      =>array('HTML/QuickForm/textarea.php','HTML_QuickForm_textarea'),
            'fckeditor'     =>array('HTML/QuickForm/fckeditor.php','HTML_QuickForm_FCKEditor'),
            'tinymce'       =>array('HTML/QuickForm/tinymce.php','HTML_QuickForm_TinyMCE'),
            'dojoeditor'    =>array('HTML/QuickForm/dojoeditor.php','HTML_QuickForm_dojoeditor'),
            'link'          =>array('HTML/QuickForm/link.php','HTML_QuickForm_link'),
            'advcheckbox'   =>array('HTML/QuickForm/advcheckbox.php','HTML_QuickForm_advcheckbox'),
            'date'          =>array('HTML/QuickForm/date.php','HTML_QuickForm_date'),
            'static'        =>array('HTML/QuickForm/static.php','HTML_QuickForm_static'),
            'header'        =>array('HTML/QuickForm/header.php', 'HTML_QuickForm_header'),
            'html'          =>array('HTML/QuickForm/html.php', 'HTML_QuickForm_html'),
            'hierselect'    =>array('HTML/QuickForm/hierselect.php', 'HTML_QuickForm_hierselect'),
            'autocomplete'  =>array('HTML/QuickForm/autocomplete.php', 'HTML_QuickForm_autocomplete'),
            'xbutton'       =>array('HTML/QuickForm/xbutton.php','HTML_QuickForm_xbutton'),
            'advmultiselect'=>array('HTML/QuickForm/advmultiselect.php','HTML_QuickForm_advmultiselect'),
        );
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
        $this->_individualID = $this->individualCreate();
        $profileHTML         = civicrm_uf_profile_html_by_id_get($this->_individualID, $this->_ufGroupId);
        $this->assertNotNull($profileHTML);
    }


    /**
     * fetch profile html with group id
     */
    public function testGetUFProfileCreateHTML()
    {
        $fieldsParams = array(
            'field_name'       => 'first_name',
            'field_type'       => 'Individual',
            'visibility'       => 'Public Pages and Listings',
            'weight'           => 1,
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
        $this->assertEquals($ufProfile['country-Primary']['field_type'],       $params['field_type']);
        $this->assertEquals($ufProfile['country-Primary']['title'],            $params['label']);
        $this->assertEquals($ufProfile['country-Primary']['visibility'],       $params['visibility']);
        $this->assertEquals($ufProfile['country-Primary']['group_id'],         $this->_ufGroupId);
        $this->assertEquals($ufProfile['country-Primary']['groupTitle'],       'Test Profile');
        $this->assertEquals($ufProfile['country-Primary']['groupHelpPre'],     'Profle to Test API');
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
        $this->assertEquals($result, true);
    }


    /**
     * validate profile html
     */
    public function testValidateProfileHTML()
    {
        $this->_individualID = $this->individualCreate();
        $result              = civicrm_profile_html_validate($this->_individualID, 'Test Profile');
        $this->assertEquals($result, true);
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
