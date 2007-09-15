<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contact/BAO/GroupNesting.php';
/**
 * This class is to build the form for adding Group
 */
class CRM_Group_Form_Edit extends CRM_Core_Form {



    /**
     * values for selecting an organization to associate with a group
     * 
     *
     */
    protected $_orgSelectValues;
    
    /**
     * the group id, used when editing a group
     *
     * @var int
     */
    protected $_id;
 
    /**
     * The title of the group being deleted
     *
     * @var string
     */
    protected $_title;

    /**
     * Store the tree of custom data and fields
     *
     * @var array
     */
    protected $_groupTree;
    
    /**
     * what blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /**
     * set up variables to build the form
     *
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        
        $this->_id    = $this->get( 'id' );
        
        if ( $this->_id ) {
            $breadCrumbPath = CRM_Utils_System::url( 'civicrm/group', 'reset=1' );
            CRM_Utils_System::appendBreadCrumb( ts('Manage Groups') , $breadCrumbPath);
        }
        
        if ($this->_action == CRM_Core_Action::DELETE) {    
            if ( isset($this->_id) ) {
                $params   = array( 'id' => $this->_id );
                CRM_Contact_BAO_Group::retrieve( $params, $defaults );
                
                $this->_title = $defaults['title'];
                $this->assign( 'name' , $this->_title );
                $this->assign( 'count', CRM_Contact_BAO_Group::memberCount( $this->_id ) );
                CRM_Utils_System::setTitle( ts('Confirm Group Delete') );
            }
        } else {
            $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree('Group',$this->_id,0);
            if ( isset($this->_id) ) {
                $params   = array( 'id' => $this->_id );
                CRM_Contact_BAO_Group::retrieve( $params, $defaults );
                $groupValues = array( 'id'              => $this->_id,
                                      'title'           => $defaults['title'],                                     
                                      'saved_search_id' => (isset($defaults['saved_search_id'])) ? $defaults['saved_search_id'] : "");
                $this->assign_by_ref( 'group', $groupValues );
                CRM_Utils_System::setTitle( ts('Group Settings: %1', array( 1 => $defaults['title'])));
            }
        }
    }
    
    /*
     * This function sets the default values for the form. LocationType that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            CRM_Contact_BAO_Group::retrieve( $params, $defaults );

            if ( $defaults['group_type'] ) {
                $types = explode( CRM_Core_DAO::VALUE_SEPARATOR,
                                  substr( $defaults['group_type'], 1, -1 ) );
                $defaults['group_type'] = array( );
                foreach ( $types as $type ) {
                    $defaults['group_type'][$type] = 1;
                }
            }
        }



        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
	
	require_once 'CRM/Contact/BAO/GroupOrganization.php';
	if ( isset ($this->_id ) ) {
	    if (CRM_Contact_BAO_GroupOrganization::exists( $this->_id ) ) {
	        $defaults['add_group_org'] = 1;
	    }
	}
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Delete Group'),
                                             'isDefault' => true   ),
                                     array ( 'type'       => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
            
        } else {

            $this->applyFilter('__ALL__', 'trim');
            $this->add('text', 'title'       , ts('Name:') . ' ' ,
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ),true );
            $this->addRule( 'title', ts('Name already exists in Database.'),
                            'objectExists', array( 'CRM_Contact_DAO_Group', $this->_id, 'title' ) );
            
            $this->add('text', 'description', ts('Description:') . ' ', 
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'description' ) );

            require_once 'CRM/Core/OptionGroup.php';
            $this->addCheckBox( 'group_type',
                                ts( 'Group Type' ),
                                CRM_Core_OptionGroup::values( 'group_type', true ),
                                null, null, null, null, '&nbsp;&nbsp;&nbsp;' );

            $this->add( 'select', 'visibility', ts('Visibility'        ), CRM_Core_SelectValues::ufVisibility( ), true ); 
            
            $session = & CRM_Core_Session::singleton( );
            $uploadNames = $session->get( 'uploadNames' );
            if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
                $buttonType = 'upload';
            } else {
                $buttonType = 'next';
            }
            
            $childGroups = array( );
            if ( isset( $this->_id ) ) {
                $childGroupIds = CRM_Contact_BAO_GroupNesting::getDescendentGroupIds( $this->_id, false );
                foreach ( $childGroupIds as $childGroupId ) {
                    $childGroupInfo = array( );
                    $params = array( 'id' => $childGroupId );
                    CRM_Contact_BAO_Group::retrieve( $params, $childGroupInfo );
                    $childGroups[$childGroupId] = $childGroupInfo['title'];
                    $this->addElement( 'checkbox', "remove_child_group_$childGroupId", $childGroupInfo['title'] );
                }
            }
            
            $this->assign_by_ref( 'child_groups', $childGroups );
            
            require_once 'CRM/Contact/BAO/Group.php';
            $childGroupSelectValues = array( '' => '' );
            if ( isset( $this->_id ) ) {
                $potentialChildGroupIds = CRM_Contact_BAO_GroupNesting::getPotentialChildGroupIds( $this->_id );
            } else {
                $potentialChildGroups = CRM_Contact_BAO_Group::getGroups();
                $potentialChildGroupIds = array( );
                foreach ( $potentialChildGroups as $potentialChildGroup ) {
                    $potentialChildGroupIds[] = $potentialChildGroup->id;
                }
            }
            foreach ( $potentialChildGroupIds as $potentialChildGroupId ) {
                $potentialChildGroupInfo = array( );
                $params = array( 'id' => $potentialChildGroupId );
                CRM_Contact_BAO_Group::retrieve( $params, $potentialChildGroupInfo );
                $childGroupSelectValues[$potentialChildGroupId] = $potentialChildGroupInfo['title'];
            }
            
            
            if ( count( $childGroupSelectValues ) > 1 ) {
                $this->add( 'select', 'add_child_group', ts('Add Child Group'), $childGroupSelectValues );
            }

            require_once ( 'CRM/Contact/BAO/GroupOrganization.php' );
	    $this->add( 'checkbox', 'add_group_org', ts('Make this an Organization?'), null, null );
	    
	    //Provide list of organizations from which to choose associated org.
	    require_once ( 'CRM/Contact/DAO/Organization.php');
	    $orgsList = array( );
	    $this->_orgSelectValues = array( );
	    $this->_orgSelectValues[] = "- Select an Organization -";
	    $query = "SELECT id, organization_name FROM civicrm_organization";
	    $dao = new CRM_Contact_DAO_Organization( );
	    $dao->query($query);
	    while ( $dao->fetch() ) {
	      $orgsList[] = array('id' => $dao->id, 'org_name' => $dao->organization_name );
	      $this->_orgSelectValues[] = $dao->organization_name;
	    }
	    
	    $this->add( 'select', 'select_group_org', ts('Select Organization'), $this->_orgSelectValues );
	        $this->addButtons( array(
                                     array( 'type'      => $buttonType,
                                            'name'      => ( $this->_action == CRM_Core_Action::ADD ) ? ts('Continue') : ts('Save'),
                                            'isDefault' => true   ),
                                     array( 'type'      => 'cancel',
                                            'name'      => ts('Cancel') ),
                                     )
            );

            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        }
    }
    
    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        
        if ($this->_action & CRM_Core_Action::DELETE ) {
            CRM_Contact_BAO_Group::discard( $this->_id );
            CRM_Core_Session::setStatus( ts('The Group "%1" has been deleted.', array(1 => $this->_title)) );        
        } else {
            // store the submitted values in an array
            //$params = $this->exportValues();
	 
            $params = $this->controller->exportValues( $this->_name );
	    // CRM_Core_Error::debug('p', $_POST);
	    //CRM_Core_Error::debug('p', $params);
            

            $params['domain_id'] = CRM_Core_Config::domainID( );
            $params['is_active'] = 1;

            if ( is_array( $params['group_type'] ) ) {
                $params['group_type'] =
                    CRM_Core_DAO::VALUE_SEPARATOR . 
                    implode( CRM_Core_DAO::VALUE_SEPARATOR,
                             array_keys( $params['group_type'] ) ) .
                    CRM_Core_DAO::VALUE_SEPARATOR;
            } else {
                $params['group_type'] = '';
            }

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $params['id'] = $this->_id;
            }

            // format custom data
            // get mime type of the uploaded file
            if ( !empty($_FILES) ) {
                foreach ( $_FILES as $key => $value) {
                    $files = array( );
                if ( $params[$key] ) {
                    $files['name'] = $params[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $params[$key] = $files;
                }
            }
            
            $customData = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Group', null, $this->_id);
                }
            }
            
            if (! empty($customData) ) {
                $params['custom'] = $customData;
            }

            //special case to handle if all checkboxes are unchecked
            $customFields = CRM_Core_BAO_CustomField::getFields( 'Group' );
            
            if ( !empty($customFields) ) {
                foreach ( $customFields as $k => $val ) {
                    if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                         ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                        CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                     '', 'Group', null, $this->_id);
                    }
                }
            }
            
            require_once 'CRM/Contact/BAO/Group.php';
            $group =& CRM_Contact_BAO_Group::create( $params );
            
            /*
             * Remove any child groups requested to be removed
             */
            $childGroupIds = CRM_Contact_BAO_GroupNesting::getChildGroupIds( $group->id );
            foreach ( $childGroupIds as $childGroupId ) {
                if ( isset( $params["remove_child_group_$childGroupId"] ) ) {
                    CRM_Contact_BAO_GroupNesting::removeChildGroup( $group->id, $childGroupId );
                }
            }
            
            /*
             * Add child group, if that was requested
             */
            if ( ! empty( $params['add_child_group'] ) ) {
                CRM_Contact_BAO_GroupNesting::addChildGroup( $group->id, $params['add_child_group']);
            }
            
	    if ( ! empty ( $params['add_group_org'] ) ) {
	      if ( CRM_Contact_BAO_GroupOrganization::exists( $group->id ) ) {
		// do nothing for now

	      } else {
		    if ( $params['select_group_org'] != "- Select an Organization -")  {
		      $title = $this->_orgSelectValues[$params['select_group_org']];
		    }
		    else {
		        $title = $group->title;
		    }
		    require_once('CRM/Contact/BAO/GroupOrganization.php');
		    CRM_Contact_BAO_GroupOrganization::add($group->id, $title);
       
		    $contactId = CRM_Contact_BAO_GroupOrganization::getOrganizationContactId($group->id);
		    //		    CRM_Core_Error::debug('p', $params);

		    $url = CRM_Utils_System::url("civicrm/contact/add&reset=1&action=update&cid=$contactId");
		    CRM_Utils_System::redirect($url);
	      }
	    } else if ( CRM_Contact_BAO_GroupOrganization::exists( $group->id ) ) {
	        require_once( 'CRM/Contact/BAO/GroupOrganization.php' );
	        CRM_Contact_BAO_GroupOrganization::remove( $group->id );
			$contactId = CRM_Contact_BAO_GroupOrganization::getOrganizationContactId( $group->id );
		CRM_Contact_BAO_Contact::deleteContact( $contactId );
	    }

            CRM_Core_Session::setStatus( ts('The Group "%1" has been saved.', array(1 => $group->title)) );        
            
            /*
             * Add context to the session, in case we are adding members to the group
             */
            if ($this->_action & CRM_Core_Action::ADD ) {
                $this->set( 'context', 'amtg' );
                $this->set( 'amtgID' , $group->id );
                
                $session =& CRM_Core_Session::singleton( );
                $session->pushUserContext( CRM_Utils_System::url( 'civicrm/group/search', 'reset=1&force=1&context=smog&gid=' . $group->id ) );
            }
        }
    }
    
    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param array   $defaults the array of default values
     * @param boolean $force    should we set show hide based on input defaults
     *
     * @return void
     */
    function setShowHide( &$groupsWithChildren, $force ) 
    {
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
 
        $this->_showHide->addShow( 'id_child_groups_show' );
        $this->_showHide->addHide( 'id_child_groups' );

        if ( $this->_showTagsAndGroups ) {
            //add group and tags
            $contactGroup = $contactTag = array( );
            if ($this->_contactId) {
                $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $this->_contactId, 'Added' );
                $contactTag   =& CRM_Core_BAO_EntityTag::getTag($this->_contactId);
            }
            
            if ( empty($contactGroup) || empty($contactTag) ) {
                $this->_showHide->addShow( 'group_show' );
                $this->_showHide->addHide( 'group' );
            } else {
                $this->_showHide->addShow( 'group' );
                $this->_showHide->addHide( 'group_show' );
            }
        }

        // is there any demographic data?
        if ( $this->_showDemographics ) {
            if ( CRM_Utils_Array::value( 'gender_id'  , $defaults ) ||
                 CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
                 $this->_showHide->addShow( 'id_demographics' );
                 $this->_showHide->addHide( 'id_demographics_show' );
            }
        }

        if ( $force ) {
            $locationDefaults = CRM_Utils_Array::value( 'location', $defaults );
            $config =& CRM_Core_Config::singleton( );
            CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                       $locationDefaults,
                                                       $this->_maxLocationBlocks );
        }
        
        $this->_showHide->addToTemplate( );
    }

}

?>
