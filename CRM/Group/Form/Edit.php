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

require_once 'CRM/Core/Form.php';

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
            $groupValues = array( 'id' => $this->_id, 'title' => $group[$this->_id] );
            $this->assign_by_ref( 'group', $groupValues );
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
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ) );
            $this->addRule( 'title', ts('Group name is required.'), 'required' );
            $this->addRule( 'title', ts('Name already exists in Database.'),
                            'objectExists', array( 'CRM_Contact_DAO_Group', $this->_id, 'title' ) );
            
            $this->add('text', 'description', ts('Description:') . ' ', 
                       CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'description' ) );
            $this->add( 'select', 'visibility', ts('Visibility'        ), CRM_Core_SelectValues::ufVisibility( ), true ); 
            
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ( $this->_action == CRM_Core_Action::ADD ) ? ts('Continue') : ts('Save'),
                                             'isDefault' => true   ),
                                     array ( 'type'       => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
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
            $params = $this->exportValues();

            $params['domain_id'] = CRM_Core_Config::domainID( );
            $params['is_active'] = 1;
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $params['id'] = $this->_id;
            }
            
            $group =& CRM_Contact_BAO_Group::create( $params );
            
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
