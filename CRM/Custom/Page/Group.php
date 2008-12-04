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

require_once 'CRM/Core/Page.php';

/**
 * Create a page for displaying Custom Groups.
 *
 * Heart of this class is the run method which checks
 * for action type and then displays the appropriate
 * page.
 *
 */
class CRM_Custom_Page_Group extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     * 
     * @param null
     * 
     * @return  array   array of action links that we need to display for the browse screen
     * @access public
     */
    function &actionLinks()
    {
        // check if variable _actionsLinks is populated
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data group? Any profile fields that are linked to custom fields of this group will be disabled.');
            self::$_actionLinks = array(
                                        CRM_Core_Action::BROWSE  => array(
                                                                          'name'  => ts('View and Edit Custom Fields'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'reset=1&action=browse&gid=%%id%%',
                                                                          'title' => ts('View and Edit Custom Fields'),
                                                                          ),
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Preview'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=preview&reset=1&id=%%id%%',
                                                                          'title' => ts('Preview Custom Data Group'),
                                                                          ),
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Settings'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=update&reset=1&id=%%id%%',
                                                                          'title' => ts('Edit Custom Group') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=disable&reset=1&id=%%id%%',
                                                                          'title' => ts('Disable Custom Group'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=enable&reset=1&id=%%id%%',
                                                                          'title' => ts('Enable Custom Group'),
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=delete&reset=1&id=%%id%%',
                                                                          'title' => ts('Enable Custom Group'),
                                                                          ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     * 
     * @param null
     * 
     * @return void
     * @access public
     *
     */
    function run()
    {

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'
        
        if ($action & CRM_Core_Action::DELETE) {
            $session = & CRM_Core_Session::singleton();
            $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/', 'action=browse'));
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Custom_Form_DeleteGroup',"Delete Cutom Group", null );
            $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                              $this, false, 0);
            $controller->set('id', $id);
            $controller->setEmbedded( true );
            $controller->process( );
            $controller->run( );
        }
        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($id, $action) ;
        } else if ($action & CRM_Core_Action::PREVIEW) {
            $this->preview($id) ;
        } else {
            require_once 'CRM/Core/BAO/CustomGroup.php';
            require_once 'CRM/Core/BAO/UFField.php';

            // if action is enable or disable to the needful.
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Core_BAO_CustomGroup::setIsActive($id, 0);
                CRM_Core_BAO_UFField::setUFFieldStatus($id, 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_BAO_CustomGroup::setIsActive($id, 1);
                //CRM_Core_BAO_UFField::setUFFieldStatus($id, 1);
            }

            // finally browse the custom groups
            $this->browse();
        }
        // parent run 
        parent::run();
    }


    /**
     * edit custom group
     *
     * @param int    $id       custom group id
     * @param string $action   the action to be invoked
     * 
     * @return void
     * @access public
     */
    function edit($id, $action)
    {
        // create a simple controller for editing custom data
        $controller =& new CRM_Core_Controller_Simple('CRM_Custom_Form_Group', ts('Custom Group'), $action);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/', 'action=browse'));
        $controller->set('id', $id);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }
    
    /**
     * Preview custom group
     *
     * @param int $id custom group id
     * @return void
     * @access public
     */
    function preview($id)
    {
        $controller =& new CRM_Core_Controller_Simple('CRM_Custom_Form_Preview', ts('Preview Custom Data'), null);
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group', 'action=browse'));
        $controller->set('groupId', $id);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();
    }


    /**
     * Browse all custom data groups.
     * 
     * @param string $action   the action to be invoked
     * 
     * @return void
     * @access public
     */
    function browse($action=null)
    {
        // get all custom groups sorted by weight
        $customGroup = array();
        $dao =& new CRM_Core_DAO_CustomGroup();

        $dao->orderBy('weight, title');
        $dao->find();

        while ($dao->fetch()) {
            $customGroup[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $customGroup[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on custom_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $customGroup[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                                    array('id' => $dao->id));
        }

        $customGroupExtends = CRM_Core_SelectValues::customGroupExtends();
        foreach ($customGroup as $key => $array) {
            CRM_Core_DAO_CustomGroup::addDisplayEnums($customGroup[$key]);
            $customGroup[$key]['extends_display'] = $customGroupExtends[$customGroup[$key]['extends']];
        }
        
        //fix for Displaying subTypes  
        $subTypes= array();
        require_once "CRM/Contribute/PseudoConstant.php";
        require_once "CRM/Member/BAO/MembershipType.php";
		require_once "CRM/Event/PseudoConstant.php";
                
        $subTypes['Activity']     = CRM_Core_PseudoConstant::activityType( false, true );
        $subTypes['Contribution'] = CRM_Contribute_PseudoConstant::contributionType( );
        $subTypes['Membership']   = CRM_Member_BAO_MembershipType::getMembershipTypes( false );
        $subTypes['Event']        = CRM_Core_OptionGroup::values('event_type');
        $subTypes['Participant']  = array( );
		$subTypes['ParticipantRole'     ] = CRM_Core_OptionGroup::values( 'participant_role' );;
	    $subTypes['ParticipantEventName'] = CRM_Event_PseudoConstant::event( );
               
        require_once "CRM/Contact/BAO/Relationship.php";
        
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');

        $allRelationshipType = array( );
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        $subTypes['Relationship'] = $allRelationshipType;
        
        require_once "CRM/Core/Component.php";
        $cSubTypes = CRM_Core_Component::contactSubTypes();
        $contactSubTypes = array();
        foreach ($cSubTypes as $key => $value ) {
            $contactSubTypes[$key] = $key;
        }

        $subTypes['Contact']  =  $contactSubTypes;
        foreach ($customGroup as $key => $values ) {
            $sub      = CRM_Utils_Array::value( 'extends_entity_column_value', $customGroup[$key] );
			$subName  = CRM_Utils_Array::value( 'extends_entity_column_id', $customGroup[$key] );
            if ( $customGroup[$key]['extends'] == 'Relationship' && CRM_Utils_Array::value('extends_entity_column_value', $customGroup[$key] ) ) {
                $sub = $sub.'_a_b';
            }
            $type = CRM_Utils_Array::value( 'extends', $customGroup[$key] );
                        
            if ( $sub ) {
				if ( $type == 'Participant') {
					if ( $subName == 1 ) {
						$customGroup[$key]["extends_entity_column_value"] = $subTypes['ParticipantRole'][$sub];
					} elseif ( $subName == 2 ) {
						$customGroup[$key]["extends_entity_column_value"] = $subTypes['ParticipantEventName'][$sub];
					}
				} else {
					$customGroup[$key]["extends_entity_column_value"] = $subTypes[$type][$sub];
				}
            } else {
                if ( is_array( CRM_Utils_Array::value( $type, $subTypes ) ) ) {
                    $customGroup[$key]["extends_entity_column_value"] = ts("-- Any --");
                }
            }
        }

        $returnURL = CRM_Utils_System::url( 'civicrm/admin/custom/group', "reset=1&action=browse" );
        require_once 'CRM/Utils/Weight.php';
        CRM_Utils_Weight::addOrder( $customGroup, 'CRM_Core_DAO_CustomGroup',
                                    'id', $returnURL );
        
        $this->assign('rows', $customGroup);
    }
}

