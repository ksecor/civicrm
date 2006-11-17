<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
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
     * set up variables to build the form
     *
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        $this->_id    = $this->get( 'id' );

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
                                      'saved_search_id' => $defaults['saved_search_id']);
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
        }

        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
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
            $this->add( 'select', 'visibility', ts('Visibility'        ), CRM_Core_SelectValues::ufVisibility( ), true ); 
            
            $session = & CRM_Core_Session::singleton( );
            $uploadNames = $session->get( 'uploadNames' );
            if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
                $buttonType = 'upload';
            } else {
                $buttonType = 'next';
            }
            
            
            $this->addButtons( array(
                                     array ( 'type'      => $buttonType,
                                             'name'      => ( $this->_action == CRM_Core_Action::ADD ) ? ts('Continue') : ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'       => 'cancel',
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
            
            $params['domain_id'] = CRM_Core_Config::domainID( );
            $params['is_active'] = 1;
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $params['id'] = $this->_id;
            }
            
            $group =& CRM_Contact_BAO_Group::create( $params );

            
            // do the updates/inserts
            CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );            
            CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Group',$group->id); 

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

}

?>
