<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
     * to store contact details
     * 
     * @var array 
     */ 
    protected $_contact; 
    
    /** 
     * pre processing work done here. 
     * 
     * gets session variables for table name, id of entity in table, type of entity and stores them. 
     * 
     * @param  
     * @return void 
     * 
     * @access public 
     */ 
    function preProcess() 
    { 
        require_once 'CRM/Core/BAO/UFGroup.php';
        $this->_id       = $this->get( 'id'  ); 
        $this->_gid      = $this->get( 'gid' ); 
        if ( ! $this->_gid ) {
            $this->_gid = CRM_Utils_Request::retrieve('gid', $this, false, 0, 'GET');
        }

        // if we dont have a gid use the default, else just use that specific gid
        if ( ( $this->_mode == self::MODE_REGISTER || $this->_mode == self::MODE_CREATE ) && ! $this->_gid ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getRegistrationFields( $this->_action, $this->_mode );
        } else if ( $this->_mode == self::MODE_SEARCH ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getListingFields( $this->_action,
                                                                      CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY, false, $this->_gid ); 
        } else {
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, $this->_action ); 
        } 

        if (!is_array($this->_fields)) {
            $session =& CRM_Core_Session::singleton( );
            CRM_Core_Session::setStatus(ts('This feature is not currently available.'));

            return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm', 'reset=1' ) );
        }

        if ( $this->_id ) {
            $defaults = array( );

            // get the contact details (hier)
            list($contactDetails, $options) = CRM_Contact_BAO_Contact::getHierContactDetails( $this->_id, $this->_fields );
            $this->_contact = $details = $contactDetails[$this->_id];
            //start of code to set the default values
            foreach ($this->_fields as $name => $field ) {
                if (CRM_Utils_Array::value($name, $details )) {
                    //to handle custom data (checkbox) to be written
                    // to handle gender / suffix / prefix
                    if ($name == 'gender') { 
                        $defaults[$name] = $details['gender_id'];
                    } else if ($name == 'individual_prefix') {
                        $defaults[$name] = $details['individual_prefix_id'];
                    } else if ($name == 'individual_suffix') {
                        $defaults[$name] = $details['individual_suffix_id'];
                    } else if ( substr($name, 0, 6) == 'custom') {   
                        $cfID = CRM_Core_BAO_CustomField::getKeyID($name);
                        $cf =& new CRM_Core_BAO_CustomField();
                        $cf->id = $cfID;
                        if ( $cf->find( true ) ) {
                            switch($cf->html_type) {
                            case 'Select Date':
                                $defaults[$name] = CRM_Utils_Date::unformat( $details[$name], '-');
                                break;
                            case 'CheckBox':
                                $data = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $details[$name]);
                                if (is_array($data)) {
                                    foreach($data as $key) {
                                        $checked[$key] = 1;
                                    }
                                    $defaults[$name] = $checked;
                                }
                                break;
                            default:
                                $defaults[$name] = $details[$name];
                            }
                        }                 
                    } else{
                        $defaults[$name] = $details[$name];
                    }
                   
                } else {
                    $nameValue = explode( '-' , $name );
                    foreach ($details as $key => $value) {
                        if (is_numeric($nameValue[1])) {//fixed for CRM-665
                            if ($nameValue[1] == $value['location_type_id'] ) {
                                if (CRM_Utils_Array::value($nameValue[0], $value )) {
                                    //to handle stateprovince and country
                                    if ( $nameValue[0] == 'state_province' ) {
                                        $defaults[$name] = $value['state_province_id'];
                                    } else if ( $nameValue[0] == 'country' ) {
                                        $defaults[$name] = $value['country_id'];
                                    } else if ( $nameValue[0] == 'phone' ) {
                                        $defaults[$name] = $value['phone'][1];
                                    } else if ( $nameValue[0] == 'email' ) {
                                        //adding the first email (currently we don't support multiple emails of same location type)
                                        $defaults[$name] = $value['email'][1];
                                    } else if ( $nameValue[0] == 'im' ) {
                                        //adding the first email (currently we don't support multiple ims of same location type)
                                        $defaults[$name] = $value['im'][1];
                                    } else {
                                        $defaults[$name] = $value[$nameValue[0]];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $this->setDefaults( $defaults );       
            //end of code to set the default values
        }
    } 
    
    /** 
     * This function sets the default values for the form. Note that in edit/view mode 
     * the default values are retrieved from the database 
     *  
     * @access public 
     * @return void 
     */ 
    function &setDefaultValues( ) { 
    } 

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
       
        $this->assign( 'mode'    , $this->_mode     );
        $this->assign( 'action'  , $this->_action   );
        $this->assign( 'fields'  , $this->_fields   );
        $this->assign( 'fieldset', $this->_fieldset ); 

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
                 strpos( $name, 'email' ) !== false ) {
               // CRM_Core_Error::debug( $name, $this->_mode );
                continue;
            }

            $required = ( $this->_mode == self::MODE_SEARCH ) ? false : $field['is_required'];

            //if ( $field['name'] === 'state_province' ) {
            if ( substr($field['name'],0,14) === 'state_province' ) {
                $this->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
            } else if ( substr($field['name'],0,7) === 'country' ) {
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
            } else if ( $field['name'] === 'individual_prefix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualPrefix());
            } else if ( $field['name'] === 'individual_suffix' ){
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualSuffix());
            } else if ($field['name'] === 'preferred_communication_method') {
                $this->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_SelectValues::pcm());
            } else if ( substr($field['name'], 0, 7) === 'do_not_' ) {  
                $this->add('checkbox', $name, $field['title'], $field['attributes'], $required );
            } else if ( $field['name'] === 'group' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,
                                                              CRM_Contact_Form_GroupTag::GROUP,
                                                              true, $required );
            } else if ( $field['name'] === 'tag' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,
                                                              CRM_Contact_Form_GroupTag::TAG, false, $required  );
            } else if (substr($field['name'], 0, 6) === 'custom') {
                $customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name']);
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
        // hack this will not work in mambo/joomla, not sure why we need it
        global $user; 
        if ( isset( $user ) && ! CRM_Utils_Array::value( 'email', $fields ) ) {
            $fields['email'] = $user->mail; 
        }
    
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
            $locationType = array( );
            $count = 1;
            foreach ($fields as $key => $value) {
                $keyValue = explode('-', $key);
                if (is_numeric($keyValue[1])) {
                    if (!in_array($keyValue[1], $locationType)) {
                        $locationType[$count] = $keyValue[1];
                        $count++;
                    }
                    require_once 'CRM/Utils/Array.php';
                    $loc = CRM_Utils_Array::key($keyValue[1], $locationType);
                    
                    $data['location'][$loc]['location_type_id'] = $keyValue[1];
                
                    if ($loc == 1 ) {
                        $data['location'][$loc]['is_primary'] = 1;
                    }
                    
                    
                    if ($keyValue[0] == 'phone') {
                        if ( $keyValue[2] ) {
                            $data['location'][$loc]['phone'][$loc]['phone_type'] = $keyValue[2];
                        } else {
                            $data['location'][$loc]['phone'][$loc]['phone_type'] = '';
                        }
                        $data['location'][$loc]['phone'][$loc]['phone'] = $value;
                    } else if ($keyValue[0] == 'email') {
                        $data['location'][$loc]['email'][$loc]['email'] = $value;
                    } elseif ($keyValue[0] == 'im') {
                        $data['location'][$loc]['im'][$loc]['name'] = $value;
                    } else {
                        if ($keyValue[0] === 'state_province') {
                            $data['location'][$loc]['address']['state_province_id'] = $value;
                        } else if ($keyValue[0] === 'country') {
                            $data['location'][$loc]['address']['country_id'] = $value;
                        } else {
                            $data['location'][$loc]['address'][$keyValue[0]] = $value;
                        }
                    }
                } else {
                    if ($key === 'individual_suffix') { 
                        $data['suffix_id'] = $value;
                    } else if ($key === 'individual_prefix') { 
                        $data['prefix_id'] = $value;
                    } else if ($key === 'gender') { 
                        $data['gender_id'] = $value;
                    } else if (substr($key, 0, 6) === 'custom') {
                        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
                            //fix checkbox
                            if ( $customFields[$customFieldID][3] == 'CheckBox' ) {
                                $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value));
                            }
                            // fix the date field 
                            if ( $customFields[$customFieldID][2] == 'Date' ) {
                                $date =CRM_Utils_Date::format( $value );
                                if ( ! $date ) {
                                    $date = '';
                                }
                                $value = $date;
                            }
                            
                            $data['custom'][$customFieldID] = array( 
                                                                'id'      => $id,
                                                                'value'   => $value,
                                                                'extends' => $customFields[$customFieldID][3],
                                                                'type'    => $customFields[$customFieldID][2],
                                                                'custom_field_id' => $customFieldID,
                                                                );
                        }
                    } else if ($key == 'edit') {
                        continue;
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
            $ids = CRM_Core_BAO_UFGroup::findContact( $data , $cid, true );
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
     * @return void
     */
    public function postProcess( ) 
    {
        $params = $this->controller->exportValues( $this->_name );

        $data = array( );
        $data['contact_type'] = 'Individual';

        //get the custom fields for the contact
        $customFields = CRM_Core_BAO_CustomField::getFields( $data['contact_type'] );

        $locationType = array( );
        $count = 1;
        if ($this->_id ) {
            $primaryLocationType = CRM_Contact_BAO_Contact::getPrimaryLocationType($this->_id);
        }
            
        foreach ($params as $key => $value) {
            $keyValue = explode('-', $key);
            if (is_numeric($keyValue[1])) {
                if (!in_array($keyValue[1], $locationType)) {
                    $locationType[$count] = $keyValue[1];
                    $count++;
                }
                
                require_once 'CRM/Utils/Array.php';
                $loc = CRM_Utils_Array::key($keyValue[1], $locationType);

                $data['location'][$loc]['location_type_id'] = $keyValue[1];
                if ($this->_id) {
                    //get the primary location type
                    if ($keyValue[1] == $primaryLocationType) {
                        $data['location'][$loc]['is_primary'] = 1;
                    } 
                } else {
                    if ($loc == 1 ) {
                        $data['location'][$loc]['is_primary'] = 1;
                    }
                }

                if ($keyValue[0] == 'phone') {
                    if ( $keyValue[2] ) {
                        $data['location'][$loc]['phone'][$loc]['phone_type'] = $keyValue[2];
                    } else {
                        $data['location'][$loc]['phone'][$loc]['phone_type'] = '';
                    }
                    $data['location'][$loc]['phone'][$loc]['phone'] = $value;
                } else if ($keyValue[0] == 'email') {
                    $data['location'][$loc]['email'][$loc]['email'] = $value;
                } elseif ($keyValue[0] == 'im') {
                    $data['location'][$loc]['im'][$loc]['name'] = $value;
                } else {
                    if ($keyValue[0] === 'state_province') {
                        $data['location'][$loc]['address']['state_province_id'] = $value;
                    } else if ($keyValue[0] === 'country') {
                        $data['location'][$loc]['address']['country_id'] = $value;
                    } else {
                        $data['location'][$loc]['address'][$keyValue[0]] = $value;
                    }
                }
            } else {
                if ($key === 'individual_suffix') { 
                    $data['suffix_id'] = $value;
                } else if ($key === 'individual_prefix') { 
                    $data['prefix_id'] = $value;
                } else if ($key === 'gender') { 
                    $data['gender_id'] = $value;
                } else if (substr($key, 0, 6) === 'custom') {
                    if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
                        //fix checkbox
                        if ( $customFields[$customFieldID][3] == 'CheckBox' ) {
                            $value = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value));
                        }
                        // fix the date field 
                        if ( $customFields[$customFieldID][2] == 'Date' ) {
                            $date =CRM_Utils_Date::format( $value );
                            if ( ! $date ) {
                                $date = '';
                            }
                            $value = $date;
                        }

                        //to add the id of custom value if exits
                        //$this->_contact['custom_value_5_id'] = 123; 

                        $str = 'custom_value_' . $customFieldID . '_id';
                        if ($this->_contact[$str]) {
                            $id = $this->_contact[$str];
                        }
                        
                        
                        $data['custom'][$customFieldID] = array( 
                                                                'id'      => $id,
                                                                'value'   => $value,
                                                                'extends' => $customFields[$customFieldID][3],
                                                                'type'    => $customFields[$customFieldID][2],
                                                                'custom_field_id' => $customFieldID,
                                                          );
                    }
                } else if ($key == 'edit') {
                    continue;
                } else {
                    $data[$key] = $value;
                }
            }
        }

        // fix all the custom field checkboxes which are empty
        foreach ($this->_fields as $name => $field ) {
            $cfID = CRM_Core_BAO_CustomField::getKeyID($name); 
            // if there is a custom field of type checkbox and it has not been set
            // then set it to null, thanx to html protocol
            if ( $cfID &&
                 $customFields[$cfID][3] == 'CheckBox' &&
                 CRM_Utils_Array::value( 'custom', $data ) &&
                 ! CRM_Utils_Array::value( $cfID, $data['custom'] ) ) {

                $str = 'custom_value_' . $cfID . '_id'; 
                if ($this->_contact[$str]) { 
                    $id = $this->_contact[$str]; 
                }

                $data['custom'][$cfID] = array(  
                                               'id'      => $id, 
                                               'value'   => '',
                                               'extends' => $customFields[$cfID][3], 
                                               'type'    => $customFields[$cfID][2], 
                                               'custom_field_id' => $cfID,
                                               ); 
            }
        }

        // print_r($this->_contact);
        if ($this->_id) {
            $objects = array( 'contact_id', 'individual_id', 'location_id', 'address_id'  );
            $ids = array( ); 
            foreach ($this->_fields as $name => $field ) {
                $nameValue = explode( '-' , $name );
                foreach ($this->_contact as $key => $value) {
                    if (in_array($key, $objects)) {
                        $ids[substr($key,0, (strlen($key)-3))] = $value;
                        // } else if (is_numeric($key)) {
                    } else if(is_array($value)){ //fixed for CRM-665
                        if ($nameValue[1] == $value['location_type_id'] ) {
                            $locations[$value['location_type_id']] = 1;
                            $loc_no = count($locations);
                            if ($nameValue[0] == 'phone') {
                                $ids['location'][$loc_no]['phone'][1] = $value['phone']['1_id'];
                            } else if ($nameValue[0] == 'email') {
                                $ids['location'][$loc_no]['email'][1] = $value['email']['1_id'];
                            } else if ($nameValue[0] == 'im') {
                                $ids['location'][$loc_no]['im'][1] = $value['im']['1_id'];
                            } else {
                                $ids['location'][$loc_no]['address'] = $value['address_id'];
                            } 
                            $ids['location'][$loc_no]['id'] = $value['location_id'];
                        }
                    }
                    
                }
            }
        }
     
        //set the values for checkboxes (do_not_email, do_not_mail, do_not_trade, do_not_phone)
        $privacy = CRM_Core_SelectValues::privacy( );
        foreach ($privacy as $key => $value) {
            if (array_key_exists($key, $this->_fields)) {
                if ($params[$key]) {
                    $data[$key] = $params[$key];
                } else {
                    $data[$key] = 0;
                }
            }
        }

        // print_r($ids);
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact = CRM_Contact_BAO_Contact::create( $data, $ids, count($data['location']) );

        // Process group and tag  
        if ( CRM_Utils_Array::value('group', $this->_fields )) {
            CRM_Contact_BAO_GroupContact::create( $params['group'], $contact->id );
        }
        
        if ( CRM_Utils_Array::value('tag', $this->_fields )) {
            require_once 'CRM/Core/BAO/EntityTag.php';
            CRM_Core_BAO_EntityTag::create( $params['tag'], $contact->id );
        } 
        
    }
}

?>
