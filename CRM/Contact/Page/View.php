<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Core/BAO/CustomOption.php';

require_once 'CRM/Utils/Recent.php';

require_once 'CRM/Contact/BAO/Contact.php';
require_once 'CRM/Utils/Menu.php';

/**
 * Main page for viewing contact.
 *
 */
class CRM_Contact_Page_View extends CRM_Core_Page {
    /**
     * the id of the object being viewed (note/relationship etc)
     *
     * @int
     * @access protected
     */
    protected $_id;

    /**
     * the contact id of the contact being viewed
     *
     * @int
     * @access protected
     */
    protected $_contactId;

    /**
     * The action that we are performing
     *
     * @string
     * @access protected
     */
    protected $_action;

    /**
     * The permission we have on this contact
     *
     * @string
     * @access protected
     */
    protected $_permission;

    /**
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @return void
     * @access public
     *
     */
    function preProcess( )
    {
        $this->_id = CRM_Utils_Request::retrieve( 'id', $this );
        $this->assign( 'id', $this->_id );
        
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', $this, true );
        $this->assign( 'contactId', $this->_contactId );

        $this->_action = CRM_Utils_Request::retrieve('action', $this, false, 'browse');
        $this->assign( 'action', $this->_action);

        // check for permissions
        $this->_permission = null;
        if ( CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
            $this->assign( 'permission', 'edit' );
            $this->_permission = CRM_Core_Permission::EDIT;            
        } else if ( CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::VIEW ) ) {
            $this->assign( 'permission', 'view' );
            $this->_permission = CRM_Core_Permission::VIEW;
        } else {
            CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to view this contact.') );
        }

        $this->getContactDetails();

        $contactImage = $this->get( 'contactImage' );
        $displayName  = $this->get( 'displayName'  );
        $this->assign( 'displayName', $displayName );

        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );
        CRM_Utils_Recent::add( $displayName,
                               CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $this->_contactId ),
                               $contactImage,
                               $this->_contactId );
        
        // also add the cid params to the Menu array
        CRM_Utils_Menu::addParam( 'cid', $this->_contactId );

        //Custom Groups Inline
        $entityType = CRM_Contact_BAO_Contact::getContactType($this->_contactId);
        $_groupTree = CRM_Core_BAO_CustomGroup::getTree($entityType, $this->_contactId);

        //showhide blocks for Custom Fields inline
        $sBlocks = array();
        $hBlocks = array();
        $form = array();

        foreach ($_groupTree as $group) {           
            
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                
                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name'];
                $form[$elementName]['name'] = $elementName;
                $form[$elementName]['html'] = null;
                
                if ( $field['data_type'] == 'String' ||
                     $field['data_type'] == 'Int' ||
                     $field['data_type'] == 'Float' ||
                     $field['data_type'] == 'Money') {

                    if ($field['html_type'] == 'Radio' || $field['html_type'] == 'CheckBox') {
                        
                        $freezeString = $field['html_type'] == 'Radio' ? "( )" : "[ ]";
                        $freezeStringChecked = $field['html_type'] == 'Radio' ? "(x)" : "[x]";
                        
                        $customData = array();
                        if ( $field['html_type'] == 'CheckBox' ) {
                            $customData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $field['customValue']['data']);
                            
                        } else {
                            $customData[] = $field['customValue']['data'];
                        }
                        
                        
                        $coDAO =& new CRM_Core_DAO_CustomOption();
                        //$coDAO->custom_field_id = $field['id'];
                        $coDAO->entity_id    = $field['id'];
                        $coDAO->entity_table = 'civicrm_custom_field';
                        $coDAO->orderBy('weight ASC, label ASC');
                        $coDAO->find( );                    
                        
                        $counter = 1;
                        while($coDAO->fetch()) {
                            
                            $checked = in_array($coDAO->value, $customData) ? $freezeStringChecked : $freezeString;
                            $form[$elementName]['html'] .= "<tt>". $checked ."</tt>".$coDAO->label."&nbsp;\n";
                            $form[$elementName][$counter]['html'] = "<tt>". $checked ."</tt>".$coDAO->label."\n";
                            $counter++;
                        }
                    } else {
                        if ( $field['html_type'] == 'Select' ) {
                            $coDAO =& new CRM_Core_DAO_CustomOption();
                            $coDAO->entity_id = $field['id'];
                            $coDAO->entity_table = 'civicrm_custom_field';
                            $coDAO->orderBy('weight ASC, label ASC');
                            $coDAO->find( );
                            
                            while($coDAO->fetch()) {
                                if ( $coDAO->value == $field['customValue']['data'] ) {
                                    $form[$elementName]['html'] = $coDAO->label;
                                }
                            }
                        } else {
                            $form[$elementName]['html'] = $field['customValue']['data'];
                        }
                    }
                } else {
                    if ( isset($field['customValue']['data']) ) {
                        switch ($field['data_type']) {
                            
                        case 'Boolean':
                            
                            $freezeString = "( )";
                            $freezeStringChecked = "(x)";
                            if ( isset($field['customValue']['data']) ) {
                                if ( $field['customValue']['data'] == '1' ) {
                                    $form[$elementName]['html'] = "<tt>".$freezeStringChecked."</tt>Yes&nbsp;<tt>".$freezeString."</tt>No\n";
                                } else {
                                    $form[$elementName]['html'] = "<tt>".$freezeString."</tt>Yes&nbsp;<tt>".$freezeStringChecked."</tt>No\n";
                                }
                            } else {
                                $form[$elementName]['html'] = "<tt>".$freezeString."</tt>Yes&nbsp;<tt>".$freezeString."</tt>No\n";
                            }                        
                            
                            break;
                            
                        case 'StateProvince':
                            $form[$elementName]['html'] = CRM_Core_PseudoConstant::stateProvince( $field['customValue']['data'] );
                            break;
                            
                        case 'Country':
                            $form[$elementName]['html'] = CRM_Core_PseudoConstant::country( $field['customValue']['data'] );
                            break;
                            
                        case 'Date':
                            $form[$elementName]['html'] = CRM_Utils_Date::customFormat($field['customValue']['data']);
                            break;
                            
                        default:
                            $form[$elementName]['html'] = $field['customValue']['data'];
                        }                    
                    }
                }
            }

            //showhide group
            if ( $group['collapse_display'] ) {
                $sBlocks[] = "'". $group['title'] . "[show]'" ;
                $hBlocks[] = "'". $group['title'] ."'";
            } else {
                $hBlocks[] = "'". $group['title'] . "[show]'" ;
                $sBlocks[] = "'". $group['title'] ."'";
            }
        }
        
        $showBlocks = implode(",",$sBlocks);
        $hideBlocks = implode(",",$hBlocks);
        
        $this->assign('viewForm',$form);
        $this->assign('showBlocks1',$showBlocks);
        $this->assign('hideBlocks1',$hideBlocks);
        $this->assign('groupTree', $_groupTree);

        //------------
        // create menus ..
        $startWeight = CRM_Utils_Menu::getMaxWeight('civicrm/contact/view');
        $startWeight++;
        CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($this->_contactId), 'civicrm/contact/view/cd', $startWeight);

        //display OtherActivity link 
        $otherAct = CRM_Core_PseudoConstant::activityType(false);
        $activityNum = count($otherAct);
        $this->assign('showOtherActivityLink',$activityNum);
    }


    /**
     * Get meta details of the contact.
     *
     * @return void
     * @access public
     */
    function getContactDetails()
    {
        $displayName = $this->get( 'displayName' );
             
        // if the display name is cached, we can skip the other processing
        if ( isset( $displayName ) ) {
            // return;
        }

        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );

        $this->set( 'displayName' , $displayName );
        $this->set( 'contactImage', $contactImage );
    }

}

?>
