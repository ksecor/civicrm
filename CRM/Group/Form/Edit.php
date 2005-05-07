<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
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
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Group_Form_Edit
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * set up variables to build the form
     *
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        $this->_id    = $this->get( 'id' );
        if ( isset($this->_id) ) {
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

        $this->add('text', 'title'       , 'Name: ' ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ) );
        $this->addRule( 'title', 'Please enter the name.', 'required' );

        $this->add('text', 'description', 'Description: ', 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'description' ) );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ( $this->_mode == CRM_Core_Form::MODE_ADD ) ? 'Continue' : 'Save',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        // store the submitted values in an array
        $params = $this->exportValues();

        // action is taken depending upon the mode
        $group               = new CRM_Contact_DAO_Group( );
        $group->domain_id    = 1;
        $group->name         = $params['title'];
        $group->title        = $params['title'];
        $group->description  = $params['description'];

        if ($this->_mode & self::MODE_UPDATE ) {
            $group->id = $this->_id;
        }

        $group->save( );

        CRM_Core_Session::setStatus( 'The Group "' . $group->name . '" has been saved' );        

        /*
         * Add context to the session, in case we are adding members to the group
         */
        if ($this->_mode & self::MODE_ADD ) {
            $this->set( 'context', 'amtg' );
            $this->set( 'amtgID' , $group->id );
            
            $session = CRM_Core_Session::singleton( );
            $session->pushUserContext( CRM_Utils_System::url( 'civicrm/group/search', 'reset=1&force=1&context=smog&gid=' . $group->id ) );
        }
    }

}

?>