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
class CRM_Profile_Form extends CRM_Core_Form
{
    const
        MODE_REGISTER = 1,
        MODE_SEARCH   = 2,
        MODE_CREATE   = 4,
        MODE_EDIT     = 8;

    protected $_mode;

    /** 
     * The contact id that we are editing 
     * 
     * @var int 
     */ 
    protected $_id; 
 
    /** 
     * The group id that we are editing
     * 
     * @var int 
     */ 
    protected $_gid; 
 
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
        $this->_id      = $this->get( 'id'  ); 
        $this->_gid     = $this->get( 'gid' ); 
        if ( $this->_mode == self::MODE_REGISTER || $this->_mode == self::MODE_CREATE ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getRegistrationFields( $this->_action ); 
        } else if ( $this->_mode == self::MODE_SEARCH ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getListingFields( $this->_action, 
                                                                      CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY ); 
        } else {
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, $this->_action ); 
        } 
        $this->_contact = CRM_Contact_BAO_Contact::contactDetails( $this->_id ); 
    } 
    
    /** 
     * This function sets the default values for the form. Note that in edit/view mode 
     * the default values are retrieved from the database 
     *  
     * @access public 
     * @return None 
     */ 
    function &setDefaultValues( ) { 
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

        if ($this->_mode & self::MODE_EDIT) {
            $group =& new CRM_Core_DAO_UFGroup();
            $group->id = $this->_id;
            if ($group->find(true)) {
                $this->assign('help_pre',  $group->help_pre);
                $this->assign('help_post', $group->help_post);
            }
        }

        // do we need inactive options ?
        if ($this->_action & CRM_Core_Action::VIEW ) {
            $inactiveNeeded = true;
        } else {
            $inactiveNeeded = false;
        }

        // should we restrict what we display
        $admin = true;
        if ( $this->_mode == self::MODE_EDIT ) {
            $admin = false;
            $session  =& CRM_Core_Session::singleton( );
            // show all fields that are visibile: if we are a admin or the same user or in registration mode
            if ( CRM_Utils_System::checkPermission( 'administer users' ) ||
                 $this->_id == $session->get( 'userID' )                 ) {
                $admin = true;
            }
        }
        
        // add the form elements
        foreach ($this->_fields as $name => $field ) {
            // make sure that there is enough permission to expose this field
            if ( ! $admin && $field['visibility'] == 'User and User Admin Only' ) {
                continue;
            }

            // since the CMS manages the email field, suppress the email display if in
            // edit or register mode which occur within the CMS form
            if ( ( $this->_mode == self::MODE_REGISTER || $this->_mode == self::MODE_EDIT ) &&
                 $name == 'email' ) {
                continue;
            }

            $required = ( $this->_mode == self::MODE_SEARCH ) ? false : $field['is_required'];

            if ( $field['name'] === 'state_province' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
            } else if ( $field['name'] === 'country' ) {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
            } else if ( $field['name'] === 'birth_date' ) {  
                $this->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('birth') );  
            } else if ( $field['name'] === 'gender' ) {  
                $genderOptions = array( );   
                $gender = CRM_Core_PseudoConstant::gender();   
                foreach ($gender as $key => $var) {   
                    $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);   
                }   
                $this->addGroup($genderOptions, $field['name'], $field['title'] );  
            } else if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) {
                CRM_Core_BAO_CustomField::addQuickFormElement($this, $name, $customFieldID, $inactiveNeeded, false);
                if ($required) {
                    $this->addRule($name, ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else {
                $this->add('text', $name, $field['title'], $field['attributes'], $required );
            }
            
            if ( $field['rule'] ) {
                $this->addRule( $name, ts( 'Please enter a valid %1', array( 1 => $field['title'] ) ), $field['rule'] );
            }
        }

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
        }

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
    static function formRule( &$fields, &$files, $options = null ) {
        $errors = array( );

        // if no values, return
        if ( empty( $fields ) ) {
            return true;
        }

        // hack add the email, does not work in registration, we need the real user object
        global $user; 
        $fields['email'] = $user->mail; 
        $cid = $register = null; 

        // hack we use a -1 in options to indicate that its registration 
        if ( $options ) { 
            $options = (int ) $options; 
            if ( $options > 0 ) { 
                $cid = $options; 
            } else { 
                $register = true; 
            } 
        }  

        $cid = null;
        if ( $options ) {
            $cid = (int ) $options;
        }

        // dont check for duplicates during registration validation: CRM-375 
        if ( ! $register ) { 
            $ids = CRM_Core_BAO_UFGroup::findContact( $fields, $cid, true );
            if ( $ids ) {
                $errors['_qf_default'] = ts( 'An account already exists with the same information.' );
            }
        }

        // Validate Country - State list
        $countryId = $fields['country'];
        $stateProvinceId = $fields['state_province'];

        if ($stateProvinceId && $countryId) {
            $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $stateProvinceId;
            $stateProvinceDAO->find(true);

            if ($stateProvinceDAO->country_id != $countryId) {
                // country mismatch hence display error
                $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                $countries =& CRM_Core_PseudoConstant::country();
                $errors['state_province'] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
            }
        }

        return empty($errors) ? true : $errors;
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

        // hack the params for now
        if ( CRM_Utils_Array::value( 'country', $params ) ) {
            $params['country_id'] = $params['country'];
        }
        if ( CRM_Utils_Array::value( 'state_province', $params ) ) {
            $params['state_province_id'] = $params['state_province'];
        }
        if ( CRM_Utils_Array::value( 'gender', $params ) ) {
            $params['gender_id'] = $params['gender'];
        }
            
        if ( empty( $params ) ) {
            return;
        }

        $objects = array( 'contact', 'individual', 'location', 'address', 'email', 'phone' );
        $ids = array( ); 
        foreach ( $objects as $name ) { 
            $id = $name . '_id'; 
            if ( $this->_contact->$id ) { 
                $ids[$name] = $this->_contact->$id; 
            } 
        } 
        
        CRM_Core_DAO::transaction( 'BEGIN' ); 

        $params['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add   ( $params, $ids );

        $params['contact_id'] = $contact->id;
        CRM_Contact_BAO_Individual::add( $params, $ids );

        $locationType   =& CRM_Core_BAO_LocationType::getDefault( ); 
        $locationTypeId =  $locationType->id;

        $location =& new CRM_Core_DAO_Location( );
        $location->location_type_id = $locationTypeId;
        $location->entity_table = 'civicrm_contact';
        $location->entity_id    = $contact->id;
        if ( $location->find( true ) ) {
            if ( ! $location->is_primary ) {
                $location->is_primary = true;
            }
        } else {
            $location->is_primary = true;
        }
        $location->save( );
       
        $address =& new CRM_Core_BAO_Address();
        CRM_Core_BAO_Address::fixAddress( $params );
            
        if ( ! $address->copyValues( $params ) ) {
            $address->id = CRM_Utils_Array::value( 'address', $ids );
            $address->location_id = $location->id;
            $address->save( );
        }

        $phone =& new CRM_Core_BAO_Phone();
        if ( ! $phone->copyValues( $params ) ) {
            $phone->id = CRM_Utils_Array::value( 'phone', $ids );
            $phone->location_id = $location->id;
            $phone->is_primary = true;
            $phone->save( );
        }
        
        $email =& new CRM_Core_BAO_Email();
        if ( ! $email->copyValues( $params ) ) {
            $email->id = CRM_Utils_Array::value( 'email', $ids );
            $email->location_id = $location->id;
            $email->is_primary = true;
            $email->save( );
        }

        /* Process custom field values */
        foreach ($params as $key => $value) {
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
    }

}

?>
