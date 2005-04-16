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
 * form to process actions on the group aspect of ExtProperty
 */
class CRM_ExtProperty_Form_Field extends CRM_Form {

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
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     title varchar(64)    COMMENT 'Friendly Name.',
     description varchar(255)    COMMENT 'Property description (verbose).',
     data_type enum('String', 'Int', 'Float', 'Money', 'Text', 'Date', 'Boolean')    COMMENT 'Controls location of data storage in extended_data table.',
     is_required boolean    COMMENT 'Is a value required for this property.',
     is_active boolean    COMMENT 'Is this property active?',
     validation_id int unsigned NOT NULL   COMMENT 'FK to crm_validation.' 
     
     */
    public function buildQuickForm( ) {
        $this->add( 'text', 'title'      , 'Field Name', CRM_DAO::getAttribute( 'CRM_DAO_ExtProperty', 'title'       ), true );
        $this->add( 'text', 'description', 'Description', CRM_DAO::getAttribute( 'CRM_DAO_ExtProperty', 'description' ), true );
        $this->addElement('select',
                          'data_type',
                          'Data Type',
                          CRM_SelectValues::$extPropertyDataType);
        
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
    }

}

?>