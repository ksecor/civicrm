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
class CRM_Profile_Form_Edit extends CRM_Core_Form
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
        $this->_fields  = CRM_Core_BAO_UFGroup::getRegistrationFields( $this->_action );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign( 'action', $this->_action );
        $this->assign( 'fields', $this->_fields );

        // do we need inactive options ?
        if ($this->_action & CRM_Core_Action::VIEW ) {
            $inactiveNeeded = true;
        } else {
            $inactiveNeeded = false;
        }

        // add the form elements
        foreach ($this->_fields as $name => $field ) {
            if ( $field['name'] === 'state_province_id' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $field['is_required']);
            } else if ( $field['name'] === 'country_id' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $field['is_required']);
            } else if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::addQuickFormElement($this, $name, $customFieldID, $inactiveNeeded, false);
                if ($field['is_required']) {
                    $this->addRule($name, ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else {
                $this->add('text', $name, $field['title'], $field['attributes'], $field['is_required'] );
            }
            
            if ( $field['rule'] ) {
                $this->addRule( $name, ts( 'Please enter a valid %1', array( 1 => $field['title'] ) ), $field['rule'] );
            }
        }

        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel'),
                                       'isDefault' => true)
                                )
                          );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
        }
        
        $this->addFormRule( array( 'CRM_Profile_Form_Edit', 'formRule' ), $this->_id );
    }

    /**
     * global form rule
     *
     * @param array $fields the input form values
     * @param array $files   the uploaded files if any
     * @param array $options additional user data
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
    static function formRule( &$fields, &$files, $options ) {
        $errors = array( );

        // if no values, return
        if ( ! CRM_Utils_Array::value( 'edit', $fields ) ) {
            return true;
        }

        // dirty and temporal workaround for CRM-144
        $fieldName = null;
        foreach ( $fields['edit'] as $name => $dontCare ) {
            $fieldName = 'edit[' . $name . ']';
            break;
        }

        // hack add the email, does not work in registration, we need the real user object
        $cid = null;
        if ( $options ) {
            $cid = (int ) $options;
        }
        $ids = CRM_Core_BAO_UFGroup::findContact( $fields['edit'], $cid, true );
        if ( $ids ) {
            $errors['_qf_default'] = ts( 'An account already exists with the same information.' );
        }
        
        // Validate Country - State list
        $countryId = $fields['edit']['country_id'];
        $stateProvinceId = $fields['edit']['state_province_id'];

        if ($stateProvinceId && $countryId) {
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $stateProvinceId;
            $stateProvinceDAO->find(true);
            
            if ($stateProvinceDAO->country_id != $countryId) {
                // country mismatch hence display error
                $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                $countries = CRM_Core_PseudoConstant::country();
                $errors['edit[state_province_id]'] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
            }
        }

        return empty($errors) ? true : $errors;
    }
    

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array( );
        $defaults['edit[custom_16]'] = $defaults['edit[custom_20]'] = date( "Y-m-d" );
        $defaults['edit[custom_11]'] = $defaults['edit[state_province_id]'] = 1017;
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
        $params = $this->controller->exportValues( $this->_name );

        $objects = array( 'contact', 'individual', 'location', 'address', 'email', 'phone' );
        $ids = array( );

        $edit = CRM_Utils_Array::value( 'edit', $params );
        if ( ! $edit ) {
            return;
        }

        CRM_Core_DAO::transaction( 'BEGIN' ); 

        $edit['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add   ( $edit, $ids );

        $edit['contact_id'] = $contact->id;
        CRM_Contact_BAO_Individual::add( $edit, $ids );

        $locationType   =& CRM_Core_BAO_LocationType::getDefault( ); 
        $locationTypeId =  $locationType->id;

        $location =& new CRM_Core_BAO_Location( );
        $location->location_type_id = $locationTypeId;
        $location->is_primary = 1;
        $location->entity_table = 'civicrm_contact';
        $location->entity_id    = $contact->id;
        $location->save( );
        
        $address =& new CRM_Core_BAO_Address();
        CRM_Core_BAO_Address::fixAddress( $edit );
            
        if ( ! $address->copyValues( $edit ) ) {
            $address->id = CRM_Utils_Array::value( 'address', $ids );
            $address->location_id = $location->id;
            $address->save( );
        }

        $phone =& new CRM_Core_BAO_Phone();
        if ( ! $phone->copyValues( $edit ) ) {
            $phone->id = CRM_Utils_Array::value( 'phone', $ids );
            $phone->location_id = $location->id;
            $phone->is_primary = true;
            $phone->save( );
        }
        
        $email =& new CRM_Core_BAO_Email();
        if ( ! $email->copyValues( $edit ) ) {
            $email->id = CRM_Utils_Array::value( 'email', $ids );
            $email->location_id = $location->id;
            $email->is_primary = true;
            $email->save( );
        }

        /* Process custom field values */
        foreach ($params['edit'] as $key => $value) {
            if (($cfID = CRM_Core_BAO_CustomField::getKeyID($key)) == null) {
                continue;
            }
            $custom_field_id = $cfID;
            $cf =& new CRM_Core_BAO_CustomField();
            $cf->id = $custom_field_id;
            if ( $cf->find( true ) ) {
                switch($cf->html_type) {
                case 'Select Date':
                    $date = CRM_Utils_Date::format( $value );
                    if ( ! $date ) {
                        $date = '';
                    }
                    $customValue = $date;
                    break;
                case 'CheckBox':
                    $customValue = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value));
                    break;
                default:
                    $customValue = $value;
                }
            }
            
            CRM_Core_BAO_CustomValue::updateValue($contact->id, $custom_field_id, $customValue);
        }

        CRM_Core_DAO::transaction( 'COMMIT' ); 

        CRM_Core_Session::setStatus(ts('Thank you. Your contact information has been saved.'));
    }
}

?>
