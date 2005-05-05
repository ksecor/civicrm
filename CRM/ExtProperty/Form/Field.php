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
 * form to process actions on the field aspect of ExtProperty
 */
class CRM_ExtProperty_Form_Field extends CRM_Core_Form {
    /**
     * the ext prop group id saved to the session for an update
     *
     * @var int
     */
    protected $_gid;

    /**
     * The ext property id, used when editing the ext property
     *
     * @var int
     */
    protected $_id;
    
    /**
     * class constructor
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess( ) {
        $this->_gid = CRM_Utils_Request::retrieve( 'gid', $this );
        $this->_id  = CRM_Utils_Request::retrieve( 'id' , $this );
    }

    /**
    * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        
        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            CRM_Core_BAO_ExtProperty::retrieve( $params, $defaults );
            $this->_gid = $defaults['ext_property_group_id'];
        } else {
            $defaults['is_active'] = 1;
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
        $this->add( 'text', 'title'      , 'Field Label', CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_ExtProperty', 'title'       ), true );
        $this->addRule( 'title', 'Please enter label for this field.', 'title' );

        $this->add( 'text', 'description', 'Description', CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_ExtProperty', 'description' ), false );
        $this->add( 'select', 'data_type', 'Data Type', CRM_Core_SelectValues::$extPropertyDataType, true);
        $this->add( 'select', 'type', 'Form Field Type', CRM_Core_SelectValues::$formFieldType, true);
        $this->add( 'text', 'default_value', 'Default Value', CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_FormField', 'default_value' ), false );
        $this->addElement( 'checkbox', 'is_required', 'Required?' );
        $this->addElement( 'checkbox', 'is_active', 'Active?' );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => 'Save',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'reset',
                                         'name'      => 'Reset'),
                                 array ( 'type'      => 'cancel',
                                         'name'      => 'Cancel' ),
                                 )
                           );
    }
    
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        // store the submitted values in an array
        $params = $this->controller->exportValues( 'Field' );
        
        // set values for object properties
        $extProp                = new CRM_Core_DAO_ExtProperty( );
        $extProp->title         = $params['title'];
        $extProp->name          = CRM_Utils_String::titleToVar( $params['title'] );
        $extProp->description   = $params['description'];
        $extProp->data_type     = $params['data_type'];
        $extProp->default_value = $params['default_value'];
        $extProp->is_required   = CRM_Utils_Array::value( 'is_required', $params, false );
        $extProp->is_active     = CRM_Utils_Array::value( 'is_active', $params, false );

        $extProp->ext_property_group_id = $this->_groupID;
        
        if ( $this->_mode & self::MODE_UPDATE ) {
            $extProp->id = $this->_id;
        }
        $extProp->ext_property_group_id = $this->_gid;
        $extProp->save( );
        CRM_Core_Session::setStatus( 'Your custom field - ' . $extProp->title . ' has been saved' );
    }

}

?>