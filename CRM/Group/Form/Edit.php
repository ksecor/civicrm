<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
     * the group id, used when editing a group
     *
     * @var int
     */
    protected $_id;

    /**
     * the group object, if an id is present
     *
     * @var object
     */
    protected $_group;

    /**
     * The title of the group being deleted
     *
     * @var string
     */
    protected $_title;
    
    /**
     * Store the group values
     *
     * @var array
     */
    protected $_groupValues;

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
            $breadCrumb = array( array('title' => ts('Manage Groups'),
                                       'url'   => CRM_Utils_System::url( 'civicrm/group', 
                                                                         'reset=1' )) );
            CRM_Utils_System::appendBreadCrumb( $breadCrumb );

            $this->_groupValues = array( );
            $params   = array( 'id' => $this->_id );
            $this->_group =& CRM_Contact_BAO_Group::retrieve( $params,
                                                              $this->_groupValues );
            $this->_title = $this->_groupValues['title'];
        }
        $this->assign ( 'action', $this->_action );
        $this->assign ( 'showBlockJS', true );

        if ($this->_action == CRM_Core_Action::DELETE) {    
            if ( isset($this->_id) ) {
                $this->assign( 'title' , $this->_title );
                $this->assign( 'count', CRM_Contact_BAO_Group::memberCount( $this->_id ) );
                CRM_Utils_System::setTitle( ts('Confirm Group Delete') );
            }
        } else {
            $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree('Group',$this->_id, 0);
            if ( isset($this->_id) ) {
                $groupValues = array( 'id'              => $this->_id,
                                      'title'           => $this->_title,
                                      'saved_search_id' =>
                                      isset( $this->_groupValues['saved_search_id'] ) ?
                                      $this->_groupValues['saved_search_id'] : '' );
                $this->assign_by_ref( 'group', $groupValues );
                CRM_Utils_System::setTitle( ts('Group Settings: %1', array( 1 => $this->_title)));
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

        if ( isset( $this->_id ) ) {
            $defaults = $this->_groupValues;
            if ( CRM_Utils_Array::value('group_type',$defaults) ) {
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
            $this->add('text', 'title'       , ts('Name') . ' ' ,
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ),true );
            $this->addRule( 'title', ts('Name already exists in Database.'),
                            'objectExists', array( 'CRM_Contact_DAO_Group', $this->_id, 'title' ) );
            
            $this->add('textarea', 'description', ts('Description') . ' ', 
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'description' ) );

            require_once 'CRM/Core/OptionGroup.php';
            $groupTypes = CRM_Core_OptionGroup::values( 'group_type', true );
            if ( isset( $this->_id ) &&
                 CRM_Utils_Array::value( 'saved_search_id', $this->_groupValues ) ) {
                unset( $groupTypes['Access Control'] );
            }
            
            if ( ! CRM_Core_Permission::access( 'CiviMail' ) ) {
                unset( $groupTypes['Mailing List'] );
            }

            if ( ! empty( $groupTypes ) ) {
                $this->addCheckBox( 'group_type',
                                    ts( 'Group Type' ),
                                    $groupTypes,
                                    null, null, null, null, '&nbsp;&nbsp;&nbsp;' );
            }

            $this->add( 'select', 'visibility', ts('Visibility'),
                        CRM_Core_SelectValues::ufVisibility( ), true ); 
            
            $session = & CRM_Core_Session::singleton( );
            $uploadNames = $session->get( 'uploadNames' );
            if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
                $buttonType = 'upload';
            } else {
                $buttonType = 'next';
            }

            $groupNames =& CRM_Core_PseudoConstant::group();

            $parentGroups = array( );
            if ( isset( $this->_id ) &&
                 CRM_Utils_Array::value( 'parents', $this->_groupValues ) ) {
                $parentGroupIds = explode( ',', $this->_groupValues['parents'] );
                foreach ( $parentGroupIds as $parentGroupId ) {
                    $parentGroups[$parentGroupId] = $groupNames[$parentGroupId];
                    $this->addElement( 'checkbox', "remove_parent_group_$parentGroupId",
                                       $groupNames[$parentGroupId] );
                }
            }
            $this->assign_by_ref( 'parent_groups', $parentGroups );
            
            if ( isset( $this->_id ) ) {
                require_once 'CRM/Contact/BAO/GroupNestingCache.php';
                $potentialParentGroupIds =
                    CRM_Contact_BAO_GroupNestingCache::getPotentialCandidates( $this->_id,
                                                                               $groupNames );
            } else {
                $potentialParentGroupIds = array_keys( $groupNames );
            }

            $parentGroupSelectValues = array( '' => '- ' . ts('select') . ' -' );
            foreach ( $potentialParentGroupIds as $potentialParentGroupId ) {
                if ( array_key_exists( $potentialParentGroupId, $groupNames ) ) {
                    $parentGroupSelectValues[$potentialParentGroupId] = $groupNames[$potentialParentGroupId];
                }
            }
            
            if ( count( $parentGroupSelectValues ) > 1 ) {
                $this->add( 'select', 'add_parent_group', ts('Add Parent'), $parentGroupSelectValues );
            }

            $this->addButtons( array(
                                     array ( 'type'      => $buttonType,
                                             'name'      =>
                                             ( $this->_action == CRM_Core_Action::ADD ) ?
                                             ts('Continue') : ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'       => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );

            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree );
        }

    }
    
    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        
        $updateNestingCache = false;
        if ($this->_action & CRM_Core_Action::DELETE ) {
            CRM_Contact_BAO_Group::discard( $this->_id );
            CRM_Core_Session::setStatus( ts("The Group '%1' has been deleted.", array(1 => $this->_title)) );        
            $updateNestingCache = true;
        } else {
            // store the submitted values in an array
            $params = $this->controller->exportValues( $this->_name );

            $params['is_active'] = 1;

            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $params['id'] = $this->_id;
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
             * Remove any parent groups requested to be removed
             */
            $parentGroupIds = explode( ',', $this->_groupValues['parents'] );
            foreach ( $parentGroupIds as $parentGroupId ) {
                if ( isset( $params["remove_parent_group_$parentGroupId"] ) ) {
                    CRM_Contact_BAO_GroupNesting::remove( $parentGroupId, $group->id );
                    $updateNestingCache = true;
                }
            }
            
            /*
             * Add parent group, if that was requested
             */
            if ( ! empty( $params['add_parent_group'] ) ) {
                CRM_Contact_BAO_GroupNesting::add( $params['add_parent_group'], $group->id );
                $updateNestingCache = true;
            }

            CRM_Core_Session::setStatus( ts('The Group \'%1\' has been saved.', array(1 => $group->title)) );        
            
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

        // update the nesting cache
        if ( $updateNestingCache ) {
            require_once 'CRM/Contact/BAO/GroupNestingCache.php';
            CRM_Contact_BAO_GroupNestingCache::update( );
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
    function setShowHide( &$groupsWithParentren, $force ) 
    {
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
 
        $this->_showHide->addShow( 'id_parent_groups_show' );
        $this->_showHide->addHide( 'id_parent_groups' );

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


