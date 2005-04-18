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

require_once 'CRM/Form.php';

/**
 * form to process actions on the field aspect of ExtProperty
 */
class CRM_ExtProperty_Form_Field extends CRM_Form {
    /**
    * The table name, used when editing/creating an ext property
     *
     * @var string
     */
    protected $_tableName;
    
    /**
    * The table id, used when editing/creating an ext property
     *
     * @var int
     */
    protected $_tableId;
    
    /**
        * The ext property id, used when editing the ext property
     *
     * @var int
     */
    protected $_extPropertyId;
    
    
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
        $this->_tableName = $this->get( 'tableName' );
        $this->_tableId   = $this->get( 'tableId'   );
        $this->_extPropertyId    = $this->get( 'extPropertyId'    );
    }

    /**
    * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( 'is_active' => '1' );
        $params   = array( );
        
        if ( $this->_mode & self::MODE_UPDATE ) {
            if ( isset( $this->_extPropertyId ) ) {
                $defaults['field'] = CRM_BAO_ExtProperty::getExtProperty( $this->_extPropertyId );
            }
        }
        
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     validation_id int unsigned NOT NULL   COMMENT 'FK to crm_validation.' 
     
     */
    public function buildQuickForm( ) {
        $this->add( 'text', 'title'      , 'Field Label', CRM_DAO::getAttribute( 'CRM_DAO_ExtProperty', 'title'       ), true );
        $this->add( 'text', 'description', 'Description', CRM_DAO::getAttribute( 'CRM_DAO_ExtProperty', 'description' ), false );
        $this->add( 'select', 'data_type', 'Data Type', CRM_SelectValues::$extPropertyDataType, true);
        $this->add( 'select', 'type', 'Form Field Type', CRM_SelectValues::$formFieldType, true);
        $this->add( 'text', 'default_value', 'Default Value', CRM_DAO::getAttribute( 'CRM_DAO_FormField', 'default_value' ), false );
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
     *     * @return void
     * @access public
     */
    public function postProcess( ) {
        // store the submitted values in an array
        $params = $this->exportValues();
        
        // action is taken depending upon the mode
        $extProperty                         = new CRM_DAO_ExtProperty( );
        $extProperty->extProperty            = $params['extProperty'];
        $extProperty->ext_property_group_id  = 1;
        
        if ($this->_mode & self::MODE_UPDATE ) {
            $extProperty->id = $this->_extPropertyId;
        } else {
            $extProperty->table_name = $this->_tableName;
            $extProperty->table_id   = $this->_tableId;
        }
        $extProperty->save( );
        
        $session = CRM_Session::singleton( );
        
        $session->setStatus( "Your Custom Field has been saved." );
    }

}

?>