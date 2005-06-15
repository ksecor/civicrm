<?php
/**
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
  */
class CRM_UF_Form_Dynamic extends CRM_Core_Form
{
    /**
     * The contact id that we are editing
     *
     * @var int
     */
    protected $_id;

    /**
     * The title of the category we are editing
     *
     * @var string
     */
    protected $_title;

    /**
     * the fields needed to build this form
     *
     * @var array
     */
    protected $_fields;

    /**
     * The contact object being edited
     *
     * @var object
     */
    protected $_contact;

    /**
     * pre processing work done here.
     *
     * gets session variables for table name, id of entity in table, type of entity and stores them.
     *
     * @param none
     * @return none
     *
     * @access public
     *
     */
    function preProcess()
    {
        $this->_id      = $this->get( 'id' );
        $this->_gid     = $this->get( 'gid' );
        if ( $this->get( 'register' ) ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getUFRegistrationFields( );
        } else {
            $this->_fields  = CRM_Core_BAO_UFGroup::getUFFields( $this->_gid );
        }
        
        $this->_contact = CRM_Contact_BAO_Contact::contactDetails( $this->_id );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign( 'fields', $this->_fields );

        // add the form elements
        // CRM_Core_Error::debug( 'f', $this->_fields );
        foreach ($this->_fields as $name => $field ) {
            if ( $field['name'] === 'state_province_id' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $field['is_required']);
            } else if ( $field['name'] === 'country_id' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $field['is_required']);
            } else {
                $this->add('text', $name, $field['title'], $field['attributes'], $field['is_required'] );
            }
            
            if ( $field['rule'] ) {
                $this->addRule( $name, ts( 'Please enter a valid %1', array( 1 => $field['title'] ) ), $field['rule'] );
            }
        }
        
        $this->addButtons(array(
                                array ('type'      => 'submit',
                                       'name'      => ts('Save'),
                                       'isDefault' => true)
                                )
                          );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
        }

    }
    

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();
        
        if ( $this->_contact ) {
            foreach ( $this->_fields as $name => $field ) {
                $objName = $field['name'];
                $defaults[$name] = $this->_contact->$objName;
            }
        }

        return $defaults;
    }

       
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $params = $this->controller->exportValues( 'Dynamic' );

        $objects = array( 'contact', 'individual', 'location', 'address', 'email', 'phone' );
        $ids = array( );
        foreach ( $objects as $name ) {
            $id = $name . '_id';
            if ( $this->_contact->$id ) {
                $ids[$name] = $this->_contact->$id;
            }
        }

        $edit = $params['edit'];
        $edit['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add   ( $edit, $ids );
        $edit['contact_id'] = $contact->id;
        CRM_Contact_BAO_Individual::add( $edit, $ids );
        if ( CRM_Utils_Array::value( 'location', $ids ) ) {
            $address =& new CRM_Contact_BAO_Address();
            if ( ! $address->copyValues( $edit ) ) {
                $address->id = CRM_Utils_Array::value( 'address', $ids );
                $address->location_id = CRM_Utils_Array::value( 'location', $ids );
                $address->save( );
            }

            $phone =& new CRM_Contact_BAO_Phone();
            if ( ! $phone->copyValues( $edit ) ) {
                $phone->id = CRM_Utils_Array::value( 'phone', $ids );
                $phone->location_id = CRM_Utils_Array::value( 'location', $ids );
                $phone->is_primary = true;
                $phone->save( );
            }

            $email =& new CRM_Contact_BAO_Email();
            if ( ! $email->copyValues( $edit ) ) {
                $email->id = CRM_Utils_Array::value( 'email', $ids );
                $email->location_id = CRM_Utils_Array::value( 'location', $ids );
                $email->is_primary = true;
                $email->save( );
            }

        }
    }
}

?>
