<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/UFGroup.php';

/**
 *  This class is for UF Group
 */
class CRM_UF_Form_Group extends CRM_Core_Form {

    /**
     * the form id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_id;
    
    /**
     * the title for group
     *
     * @var int
     * @access protected
     */
    protected $_title;
    protected $_groupElement;
    protected $_group;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        // current form id
        $this->_id = $this->get('id');
        $this-> assign('gid',$this->_id);

        $this->_group    =& CRM_Core_PseudoConstant::group( ); 
        
        // setting title for html page
        if ( $this->_action == CRM_Core_Action::UPDATE ) {
            $title = CRM_Core_BAO_UFGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle( ts( 'Profile Settings - %1', array(1 => $title ) ) );
        } else if($this->_action == CRM_Core_Action::DELETE ) {
            $title = CRM_Core_BAO_UFGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle( ts( 'Delete %1', array(1 => $title ) ) );
            $this->_title = $title;
            $this-> assign('title',$title);
        } else {
            CRM_Utils_System::setTitle( ts('New CiviCRM Profile Group') );
        }
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Delete Profile Group'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
            return;

        }
        
        $this->applyFilter('__ALL__', 'trim');

        // title
        $this->add('text', 'title', ts('Profile Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'title'), true);
        
        if ( isset ($ufgroupId ) ){
            if( !( $this->_action & CRM_Core_Action::UPDATE ) ) {
                $this->addRule( 'title', ts('Profile Title is already exist in Database.'), 'objectExists', 
                                array( 'CRM_Core_DAO_UFGroup', $ufgroupId, 'title' ) );
            }
        }
        //add checkboxes
        $uf_group_type = array();
        $UFGroupType = CRM_Core_SelectValues::ufGroupTypes( );
        foreach ($UFGroupType as $key => $value ) {
            $uf_group_type[] = HTML_QuickForm::createElement('checkbox', $key, null, $value);
        }
        $this->addGroup($uf_group_type, 'uf_group_type', ts('Used For'), '&nbsp;');

        // help text
        $this->add('textarea', 'help_pre',  ts('Pre-form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'help_pre'));
        $this->add('textarea', 'help_post', ts('Post-form Help'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'help_post'));

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFJoin', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this CiviCRM Profile active?') );

        // should mapping be enabled for this group
        $this->addElement('checkbox', 'is_map', ts('Should mapping be enabled on this profile?') );

        // should we allow updates on a exisitng contact
        $this->addElement('checkbox', 'is_update_dupe', ts('Should we update the contact on a duplicate match?' ) );

        $this->addElement('text', 'post_URL', ts('Redirect URL'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'post_URL') );
        $this->addRule('post_URL', ts('Enter a valid URL.'), 'url');

        $this->addElement('text', 'cancel_URL', ts('Cancel Redirect URL'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFGroup', 'cancel_URL') );
        $this->addRule('post_URL', ts('Enter a valid URL.'), 'url');
        // add select for groups
        $group               = array('' => ts('- select -')) + $this->_group;
        $this->_groupElement =& $this->addElement('select', 'group', ts('Limit Listings to Group'), $group);

        //group where new contacts are directed.
        $this->addElement('select', 'add_contact_to_group', ts('Add Contacts to Group'), $group);
        
         // add CAPTCHA To this group ?
        $this->addElement('checkbox', 'add_captcha', ts('Include CAPTCHA?') );

        // is this group collapsed or expanded ?
        $this->addElement('checkbox', 'collapse_display', ts('Collapse profile fieldset on initial display?'));

        // should we display an edit link
        $this->addElement('checkbox', 'is_edit_link', ts('Include Edit Link in Search Results?'));

        // should we display a link to the website profile
        $this->addElement('checkbox', 'is_uf_link', ts('Include Website Profile Link in Search Results?'));

        // want to create cms user
        $this->addElement('checkbox', 'is_cms_user', ts('Create CMS User?'));

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );

        // views are implemented as frozen form
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onclick' => "location.href='civicrm/admin/uf/group?reset=1&action=browse'"));
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        $defaults = array();
        require_once 'CRM/Core/ShowHideBlocks.php';
        $showHide =& new CRM_Core_ShowHideBlocks( );

        if ($this->_action == CRM_Core_Action::ADD) {
            $defaults['weight'] = CRM_Core_BAO_UFGroup::getWeight( );
        }
        
        if ( isset($this->_id ) ) {
            
            $defaults['weight'] = CRM_Core_BAO_UFGroup::getWeight( $this->_id );
          
            $params = array('id' => $this->_id);
            CRM_Core_BAO_UFGroup::retrieve($params, $defaults);
            $defaults['group'] = $defaults['limit_listings_group_id'];
            $defaults['add_contact_to_group'] = $defaults['add_to_group_id'];
         
            //get the uf join records for current uf group
            $ufJoinRecords = CRM_Core_BAO_UFGroup::getUFJoinRecord( $this->_id );
            foreach ($ufJoinRecords as $key => $value ) {
                $checked[$value] = 1;
            }
            $defaults['uf_group_type'] = $checked;
            
            
            //get the uf join records for current uf group other than default modules
            $otherModules = array( );
            $otherModules = CRM_Core_BAO_UFGroup::getUFJoinRecord( $this->_id, true, true );
            if (!empty($otherModules)) {
                foreach($otherModules as $key) {
                    $otherModuleString .= " [ x ] <label>" . $key . "</label>";
                }
                $this-> assign('otherModuleString',$otherModuleString);
            }
            
            $showAdvanced = 0;
            $advFields = array('group', 'collapse_display', 'post_URL', 'cancel_URL',
                               'add_captcha', 'is_map', 'is_uf_link', 'is_edit_link',
                               'is_update_dupe');
            foreach($advFields as $key) {
                if ( !empty($defaults[$key]) ) {
                    $showAdvanced = 1;
                    break;
                }
            }

            if ( $showAdvanced ) {
                $showHide->addShow( "id-advanced" );
                $showHide->addHide( "id-advanced-show" );
            } else {
                $showHide->addShow( "id-advanced-show" );
                $showHide->addHide( "id-advanced" );
            }
    
        } else {
            $defaults['is_active'     ] = 1;
            $defaults['is_map'        ] = 0;
            $defaults['is_update_dupe'] = 0;
            $showHide->addShow( "id-advanced-show" );
            $showHide->addHide( "id-advanced" );
        }

        // Don't assign showHide elements to template in DELETE mode (fields to be shown and hidden don't exist)
        if ( !( $this->_action & CRM_Core_Action::DELETE )  ) {
            $showHide->addToTemplate( );
        }
        return $defaults;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        if( $this->_action & CRM_Core_Action::DELETE ) {
            $status = 0;
            $status = CRM_Core_BAO_UFGroup::del($this->_id);
            if ($status == 0) {
                CRM_Core_Session::setStatus(ts('This profile cannot be deleted since it is used for other modules.', array(1 => $this->_title)));
            } else {
                CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been deleted.', array(1 => $this->_title)));
            }            
            return;
        }
        // get the submitted form values.
        $params = $ids = array( );
        $params = $this->controller->exportValues( $this->_name );

        if ( $this->_action & ( CRM_Core_Action::UPDATE) ) {
            $ids['ufgroup'] = $this->_id;
        }
        
        // create uf group
        $ufGroup = CRM_Core_BAO_UFGroup::add($params, $ids);

        //make entry in uf join table
        CRM_Core_BAO_UFGroup::createUFJoin($params, $ufGroup->id);

        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been saved.', array(1 => $ufGroup->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/uf/group/field', 'reset=1&action=add&gid=' . $ufGroup->id);
            CRM_Core_Session::setStatus(ts('Your CiviCRM Profile Group "%1" has been added. You can <a href="%2">add fields</a> to this group now.',
                                           array(1 => $ufGroup->title, 2 => $url)));
        }
    }

}

?>
