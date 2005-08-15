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
            $this->_fields  = CRM_Core_BAO_UFGroup::getUFRegistrationFields( $this->_action );
        } else {
            $this->_fields  = CRM_Core_BAO_UFGroup::getUFFields( $this->_gid, false, $this->_action );
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
        $this->assign( 'action',  $this->_action );
        $this->assign( 'fields', $this->_fields );

        // do we need inactive options ?
        if ($this->_action & CRM_Core_Action::VIEW ) {
            $inactiveNeeded = true;
        } else {
            $inactiveNeeded = false;
        }

        // should we restrict what we display
        $admin = false;
        $session  =& CRM_Core_Session::singleton( );
        if ( CRM_Utils_System::checkPermission( 'administer users' ) ||
             $this->_id == $session->get( 'userID' ) ) {
            $admin = true;
        }
        
        // add the form elements
        foreach ($this->_fields as $name => $field ) {
            // make sure that there is enough permission to expose this field
            if ( ! $admin && $field['visibility'] == 'User and User Admin Only' ) {
                continue;
            }

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
                                array ('type'      => 'submit',
                                       'name'      => ts('Save'),
                                       'isDefault' => true)
                                )
                          );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
        }

        $this->addFormRule( array( 'CRM_UF_Form_Dynamic', 'formRule' ), $this->_id );
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
        global $user;
        $fields['edit']['email'] = $user->mail;
        $cid = null;
        if ( $options ) {
            $cid = (int ) $options;
        }
        $ids = CRM_Core_BAO_UFGroup::findContact( $fields['edit'], $cid, true );
        if ( $ids ) {
            $urls = array( );
            foreach ( explode( ',', $ids ) as $id ) {
                $displayName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', $id, 'display_name' );
                $urls[] = '<a href="' . CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&action=update&cid=' . $id ) .
                    '">' . $displayName . '</a>';
            }
            $url = implode( ', ',  $urls );
            $errors['edit[first_name]'] = ts( 'One matching contact was found. You can edit it here: %1', array( 1 => $url, 'count' => count( $ids ), 'plural' => '%count matching contacts were found. You can edit them here: %1' ) );
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
        $defaults = array();

        if ( $this->_contact ) {
            foreach ( $this->_fields as $name => $field ) {
                $objName = $field['name'];
                if ( $objName == 'state_province_id' ) {
                    $states =& CRM_Core_PseudoConstant::stateProvince( );
                    if ( $this->_contact->state ) {
                        $defaults[$name] = array_search( $this->_contact->state, $states );
                    }
                } else if ( $objName == 'country_id' ) {
                    $country =& CRM_Core_PseudoConstant::country( );
                    if ( $this->_contact->country ) {
                        $defaults[$name] = array_search( $this->_contact->country, $country );
                    }
                } else if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($objName)) {

                    // make sure the custom field exists
                    $cf =& new CRM_Core_BAO_CustomField();
                    $cf->id = $cfID;
                    if ( ! $cf->find( true ) ) {
                        continue;
                    }

                    // make sure the custom value exists
                    $cv =& new CRM_Core_BAO_CustomValue();
                    $cv->custom_field_id = $cfID;
                    $cv->entity_table = 'civicrm_contact';
                    $cv->entity_id = $this->_id;
                    if ( ! $cv->find( true ) ) {
                        continue;
                    }

                    switch($cf->html_type) {

                    case "Radio":
                        if($cf->data_type == 'Boolean') {
                            $customValue = $cv->getValue(true)? 'yes' : 'no';
                        } else {
                            $customValue = $cv->getValue(true);
                        }
                        $defaults[$name] = $customValue;
                        break;
                            
                    case "CheckBox":
                        $customOption = CRM_Core_BAO_CustomOption::getCustomOption($cf->id);    
                        $value = $cv->getValue(true);
                        $checkedData = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value);
                        foreach($customOption as $val) {
                            $checkVal = $val['value'];
                            $checkName = $name.'['.$checkVal.']';
                            if (in_array($val['value'], $checkedData)) {
                                $defaults[$checkName] = 1;
                            } else {
                                $defaults[$checkName] = 0;
                            }
                        }
                        break;

                    case "Select Date":
                        $date = CRM_Utils_Date::unformat($cv->getValue(true));
                        $customValue = $date;
                        $defaults[$name] = $customValue;
                        break;

                    default:
                        $customValue = $cv->getValue(true);
                        $defaults[$name] = $customValue;
                        break;
                    }
                }
            } else {
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

        $edit = CRM_Utils_Array::value( 'edit', $params );
        if ( ! $edit ) {
            return;
        }

        $edit['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add   ( $edit, $ids );

        $edit['contact_id'] = $contact->id;
        CRM_Contact_BAO_Individual::add( $edit, $ids );
        if ( CRM_Utils_Array::value( 'location', $ids ) ) {
            $address =& new CRM_Core_BAO_Address();
            CRM_Core_BAO_Address::fixAddress( $edit );
            
 	    if ( ! $address->copyValues( $edit ) ) {
                $address->id = CRM_Utils_Array::value( 'address', $ids );
                $address->location_id = CRM_Utils_Array::value( 'location', $ids );
                $address->save( );
            }

            $phone =& new CRM_Core_BAO_Phone();
            if ( ! $phone->copyValues( $edit ) ) {
                $phone->id = CRM_Utils_Array::value( 'phone', $ids );
                $phone->location_id = CRM_Utils_Array::value( 'location', $ids );
                $phone->is_primary = true;
                $phone->save( );
            }

            $email =& new CRM_Core_BAO_Email();
            if ( ! $email->copyValues( $edit ) ) {
                $email->id = CRM_Utils_Array::value( 'email', $ids );
                $email->location_id = CRM_Utils_Array::value( 'location', $ids );
                $email->is_primary = true;
                $email->save( );
            }

        }

        /* Process custom field values */
        foreach ($params['edit'] as $key => $value) {
            if (($cfID = CRM_Core_BAO_CustomField::getKeyID($key)) == null) {
                continue;
            }
            $custom_field_id = $cfID;
            $cf =& new CRM_Core_BAO_CustomField();
            $cf->id = $custom_field_id;
            $cf->find();
            while($cf->fetch()) {
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
    }
}

?>
