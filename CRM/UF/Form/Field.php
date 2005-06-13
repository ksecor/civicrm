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
 * $Id: Field.php 1419 2005-06-10 12:18:04Z shot $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_UF_Form_Field extends CRM_Core_Form {
    /**
     * the uf group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_gid;

    /**
     * The field id, used when editing the field
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * The set of fields that we can view/edit in the user field framework
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /**
     * The set of fields sent to the select element
     *
     * @var array
     * @access protected
     */
    protected $_selectFields;

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , $this);

        $this->_fields =& CRM_Contact_BAO_Contact::importableFields( );

        $this->_selectFields = array( );
        foreach ($this->_fields as $name => $field ) {
            if ( $name ) {
                $this->_selectFields[$name] = $field['title'];
            }
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param none
     * @access public
     * @return None
     */
    function setDefaultValues()
    {
        $defaults = array();
        
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_UFField::retrieve($params, $defaults);

            $defaults['field_name'] = array_search( $defaults['field_name'], $this->_selectFields );
            $this->_gid = $defaults['uf_group_id'];
        } else {
            $defaults['is_active'] = 1;
        }
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        // field name
        $this->add( 'select', 'field_name', ts('CiviCRM Field Name'), $this->_selectFields, true );
        $this->add( 'select', 'visibility', ts('Visibility'        ), CRM_Core_SelectValues::ufVisibility( ), true );

        // listings title
        $this->add('text', 'listings_title', ts('Listings_Title'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFField', 'listings_title') );
        $this->addRule('listings_title', ts('Please enter a valid label for this field.'), 'title');
        
        $this->add( 'checkbox', 'is_required'    , ts( 'Required?'                     ) );
        $this->add( 'checkbox', 'is_active'      , ts( 'Active?'                       ) );
        $this->add( 'checkbox', 'is_view'        , ts( 'View Only?'                    ) );
        $this->add( 'checkbox', 'is_registration', ts( 'Display in Registration Form?' ) );
        $this->add( 'checkbox', 'is_match'       , ts( 'Key to match contacts?'        ) );
        
        // add buttons
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'reset',
                                       'name'      => ts('Reset')),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/uf/group/field?reset=1&action=browse&gid=" . $this->_gid . "'"));
        }
    }

    /**
     * Process the form
     *
     * @param none
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues('Field');

        // set values for custom field properties and save
        $ufField                 =& new CRM_Core_DAO_UFField();
        $ufField->field_name     = $this->_selectFields[$params['field_name']];
        $ufField->listings_title = $params['listings_title'];
        $ufField->visibility     = $params['visibility'];

        $ufField->is_required     = CRM_Utils_Array::value( 'is_required'    , $params, false );
        $ufField->is_active       = CRM_Utils_Array::value( 'is_active'      , $params, false );
        $ufField->is_view         = CRM_Utils_Array::value( 'is_view'        , $params, false );
        $ufField->is_registration = CRM_Utils_Array::value( 'is_registration', $params, false );
        $ufField->is_match        = CRM_Utils_Array::value( 'is_match'       , $params, false );

        if ($this->_action & CRM_Core_Action::UPDATE) {
            $ufField->id = $this->_id;
        }

        // need the FKEY - uf group id
        $ufField->uf_group_id = $this->_gid;

        $ufField->save();
        
        CRM_Core_Session::setStatus(ts('Your user framework field "%1" has been saved', array(1 => $ufField->label)));
    }

}

?>
