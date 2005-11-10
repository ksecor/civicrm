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
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;    

    /** 
     * the fields needed to build this form 
     * 
     * @var array 
     */ 
    protected $_fields; 

    protected $_contactDetailIds;
    
    /** 
     * pre processing work done here. 
     * 
     * gets session variables for table name, id of entity in table, type of entity and stores them. 
     * 
     * @param  
     * @return void 
     * 
     * @access public 
     * 
     */ 
    function preProcess() 
    { 
        require_once 'CRM/Core/BAO/UFGroup.php';
        $this->_id       = $this->get( 'id'  ); 
        $this->_gid      = $this->get( 'gid' ); 

        if ( $this->_mode == self::MODE_REGISTER || $this->_mode == self::MODE_CREATE ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getRegistrationFields( $this->_action );
        } else if ( $this->_mode == self::MODE_SEARCH ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getListingFields( $this->_action, 
                                                                      CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY ); 
        } else {
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, $this->_action ); 
        } 
        

        //$options = array( );
        //$this->_contact = CRM_Contact_BAO_Contact::contactDetails( $this->_id, $options ); 
        //print_r($this->_contact);
        //print_r($this->_fields);
        if ( $this->_id ) {
            $record['id']            = $record['contact_id'] = $this->_id;
            $this->_contact          = CRM_Contact_BAO_Contact::retrieve( $record , $options, $ids );
            $this->_contactDetailIds = $ids;
            //print_r($options);
            foreach ($this->_fields as $name => $field ) {
                foreach ($options as $key => $value) {
                    $nameValue = explode('-', $name);
                    if (is_numeric($nameValue[1])) {
                        if (is_array($value)) {
                            foreach ($value as $key1 => $value1) {
                                if (is_array($value1)) {
                                    if ( $value1['location_type_id'] == $nameValue[1] ) {
                                        //print_r($value1);
                                        foreach ($value1 as $key2 => $var) {
                                            //print_r($var);
                                            if (is_array($var)) {
                                                foreach ($var as $k1 => $var1) {
                                                    if (is_array($var1)) {
                                                        //set the phone values
                                                        if ($nameValue[0] == 'phone' && $nameValue[2] == $var1['phone_type']) {
                                                            $defaults[$name] = $var1['phone'];
                                                        }
                                                        //set the im values
                                                        if ($nameValue[0] == 'im') {
                                                            $defaults[$name] = $var1['name'];
                                                        }
                                                        //set the emial values
                                                        if ($nameValue[0] == 'email') {
                                                            $defaults[$name] = $var1['email'];
                                                        }
                                                    } else {
                                                        //set the address values
                                                        if ($nameValue[0] === 'country'  && substr($k1,0,7) === 'country') {
                                                            $defaults[$name] = $var1;
                                                        } else if ($nameValue[0] === 'state_province' && substr($k1,0,14)  === 'state_province' ) {
                                                            $defaults[$name] = $var1;
                                                        } else if ( $nameValue[0] == $k1 ) {
                                                            $defaults[$name] = $var1;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        //echo "=========$key===========$value=====<br>";
                        //set the other values
                        
                        if ($key === 'suffix_id') { 
                            $defaults['individual_suffix'] = $value;
                        } else if ($key === 'prefix_id') { 
                            $defaults['individual_prefix'] = $value;
                        } else if ($key === 'gender_id') { 
                            $defaults['gender'] = $value;
                        } else {
                            $defaults[$key] = $value;
                        }
                    }
                }
            }
            
            //get Custom Group tree
            require_once 'CRM/Core/BAO/CustomGroup.php';
            $this->_groupTree = CRM_Core_BAO_CustomGroup::getTree('Individual', $this->_id);
            $this->assign('groupTree', $this->_groupTree); 

            require_once 'CRM/Core/BAO/CustomGroup.php';
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );

            //print_r($options);
            //print_r($defaults);
            $this->setDefaults( $defaults );       
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
        // print_r($this->_fields);
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
            } else if ( $field['name'] === 'group' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,
                                                              CRM_Contact_Form_GroupTag::GROUP);
            } else if ( $field['name'] === 'tag' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_id,
                                                              CRM_Contact_Form_GroupTag::TAG );
            } else if (substr($field['name'], 0, 6) === 'custom') {
                $customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name']);
                
                foreach ($this->_groupTree as $group) {
                    $groupId = $group['id'];
                    foreach ($group['fields'] as $customField) {
                        $fieldId = $customField['id'];                
                        $elementName = $groupId . '_' . $fieldId . '_' . $customField['name']; 
                        CRM_Core_BAO_CustomField::addQuickFormElement($this, $elementName, $fieldId, $inactiveNeeded, true);
                    }
                }
                
                //CRM_Core_BAO_CustomField::addQuickFormElement($this, $name, $customFieldID, $inactiveNeeded, false);
                if ($required) {
                    $this->addRule($elementName, ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else if  ( substr($field['name'],0,5) === 'phone' ) {
                $this->add('text', $name, $field['title'] . " - " . $field['phone_type'], $field['attributes'], $required);
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
     * @return void
     */
    public function postProcess( ) 
    {
        $params = $this->controller->exportValues( $this->_name );

        // CRM_Core_Error::debug( $this->_name, $params );
        // CRM_Core_Error::debug( 'p', $_POST );

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
        
        /*
        $objects = array( 'contact', 'individual', 'location', 'address', 'email', 'phone' );
        $ids = array( ); 
        foreach ( $objects as $name ) { 
            $id = $name . '_id'; 
            if ( $this->_contact->$id ) { 
                $ids[$name] = $this->_contact->$id; 
            } 
        } 
        */
        
        //$params['id'] = $params['contact_id'] = $this->_id;

        //print_r($params);
        $data = array( );
        $locationType = array( );
        $count = 1;
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
                if ($loc == 1 ) {
                    $data['location'][$loc]['is_primary'] = 1;
                }

                if ($keyValue[2]) {
                    $data['location'][$loc]['phone'][$loc]['phone'] = $value;
                    $data['location'][$loc]['phone'][$loc]['phone_type'] = $keyValue[2];
                } else {
                    if ($keyValue[0] == 'email') {
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
                }
            } else {
                if ($key === 'individual_suffix') { 
                    $data['suffix_id'] = $value;
                } else if ($key === 'individual_prefix') { 
                    $data['prefix_id'] = $value;
                } else if ($key === 'gender') { 
                    $data['gender_id'] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }             
        
        $data['contact_type'] = 'Individual';
        //print_r($data);
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact = CRM_Contact_BAO_Contact::create( $data, $this->_contactDetailIds, count($data['location']) );
        
        // Process group / tag / custom field values
        foreach ($params as $key => $value) {
            if ( $key == 'group' ) {
                CRM_Contact_BAO_GroupContact::create( $params['group'], $contact->id );
            } else if ( $key == 'tag' ) {
                require_once 'CRM/Core/BAO/EntityTag.php';
                CRM_Core_BAO_EntityTag::create( $params['tag'], $contact->id );
            } 
        }
        
        // print_r($params);
        require_once 'CRM/Core/BAO/CustomGroup.php';
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );
        //echo $contact->id;
        //print_r($this->_groupTree);
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, 'Individual', $contact->id);
 
        // print_r($this->_contactDetailIds);
        // print_r($contact);
        // return $contact;
        
        //CRM_Contact_BAO_Contact::createFlat( $params, $ids );


    }

}

?>
