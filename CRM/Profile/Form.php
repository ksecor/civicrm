<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
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
    
    protected $_skipPermission = false;
    
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
     * to store group_id of the group which is to be assigned to the contact
     * 
     * @var int
     */ 
    protected $_addToGroupID;

    /**
     * Do we allow updates of the contact
     *
     * @var boolean
     */
    public $_isUpdateDupe = false;
    
    /**
     * THe context from which we came from, allows us to go there if redirect not set
     *
     * @var string
     */
    protected $_context;
    
    /**
     * THe contact type for registration case
     *
     * @var string
     */
    protected $_ctype = null;

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
        require_once "CRM/Core/BAO/UFField.php";
        
        $this->_id       = $this->get( 'id'  ); 
        $this->_gid      = $this->get( 'gid' ); 
  
        $this->_context  = CRM_Utils_Request::retrieve( 'context', 'String', $this );
       
        if ( ! $this->_gid ) {
            $this->_gid = CRM_Utils_Request::retrieve('gid', 'Positive', $this, false, 0, 'GET');
        }  
       
        // if we dont have a gid use the default, else just use that specific gid
        if ( ( $this->_mode == self::MODE_REGISTER || $this->_mode == self::MODE_CREATE ) && ! $this->_gid ) {
            $this->_ctype  = CRM_Utils_Request::retrieve( 'ctype', 'String', $this, false, 'Individual', 'REQUEST' );
            $this->_fields  = CRM_Core_BAO_UFGroup::getRegistrationFields( $this->_action, $this->_mode, $this->_ctype );
        } else if ( $this->_mode == self::MODE_SEARCH ) {
            $this->_fields  = CRM_Core_BAO_UFGroup::getListingFields( $this->_action,
                                                                      CRM_Core_BAO_UFGroup::PUBLIC_VISIBILITY | CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY,
                                                                      false,
                                                                      $this->_gid,
                                                                      true, null,
                                                                      $this->_skipPermission ); 
        } else { 
            $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $this->_gid, false, null,
                                                               null, null,
                                                               false, null,
                                                               $this->_skipPermission );
            //if civimail is enable and email field is absent show status
            if ( CRM_Core_Permission::access( 'CiviMail' ) && CRM_Utils_Array::value( 'group', $this->_fields ) ) {
                $emailField = false;
                foreach ( $this->_fields as $name => $values ) {
                    if ( substr( $name, 0, 6 ) == 'email-' ) {
                        $emailField = true;
                    }
                }
                if ( ! $emailField ) {
                    $session =& CRM_Core_Session::singleton( );
                    $status = ts( "Email field should be included in profile if you want to use Group(s) when CiviMail is enabled." ); 
                    $session->setStatus( $status );
                }
            }
        }
        if (! is_array($this->_fields)) {
            $session =& CRM_Core_Session::singleton( );
            CRM_Core_Session::setStatus(ts('This feature is not currently available.'));
            
            return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm', 'reset=1' ) );
        }

        if( $this->_mode != self::MODE_SEARCH ) {
            CRM_Core_BAO_UFGroup::setRegisterDefaults(  $this->_fields, $defaults );
            $this->setDefaults( $defaults );    
        }

       
        $this->setDefaultsValues();
    }
    
    /** 
     * This function sets the default values for the form. Note that in edit/view mode 
     * the default values are retrieved from the database 
     *  
     * @access public 
     * @return void 
     */ 
    function setDefaultsValues( ) 
    {
        $defaults = array( );   
        if ( $this->_id ) {
            CRM_Core_BAO_UFGroup::setProfileDefaults( $this->_id, $this->_fields, $defaults, true );
        }
        
        //set custom field defaults
        require_once "CRM/Core/BAO/CustomField.php";
        foreach ( $this->_fields as $name => $field ) {
            if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID($name) ) {
                
                $htmlType = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                         $customFieldID,
                                                         'html_type',
                                                         'id' );
                
                if ( !isset( $defaults[$name] ) || $htmlType == 'File') {
                    CRM_Core_BAO_CustomField::setProfileDefaults( $customFieldID,
                                                                  $name,
                                                                  $defaults,
                                                                  $this->_id,
                                                                  $this->_mode );
                }
                
                if ( $htmlType == 'File') {
                    $url = CRM_Core_BAO_CustomField::getFileURL( $this->_id, $customFieldID );
                    
                    if ( $url ) {
                        $customFiles[$field['name']]['displayURL'] = "Attached File : {$url['file_url']}";
                        
                        $deleteExtra = "Are you sure you want to delete attached file ?";
                        $fileId      = $url['file_id'];
                        $deleteURL   = CRM_Utils_System::url( 'civicrm/file',
                                                              "reset=1&id={$fileId}&eid=$this->_id&fid={$customFieldID}&action=delete" );
                        $customFiles[$field['name']]['deleteURL'] =
                            "<a href=\"{$deleteURL}\" onclick = \"if (confirm( ' $deleteExtra ' )) this.href+='&amp;confirmed=1'; else return false;\">Delete Attached File</a>";
                    }
                } 
            }
        }
        if ( isset( $customFiles ) ) {
            $this->assign( 'customFiles', $customFiles ); 
        }
        $this->setDefaults( $defaults );
    } 
    
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {   
        $sBlocks = array( );
        $hBlocks = array( );
        
        $config  =& CRM_Core_Config::singleton( );
        
        // we should not allow component and mix profiles in search mode
        if ( $this->_mode != self::MODE_REGISTER ) {
            //check for mix profile fields (eg:  individual + other contact type)
            if ( CRM_Core_BAO_UFField::checkProfileType($this->_gid) ) {
                CRM_Core_Session::setStatus( ts( "This Profile includes fields for more than one record type.") );
            }
            
            $profileType = CRM_Core_BAO_UFField::getProfileType($this->_gid);  

            if(in_array( $profileType, array( "Membership", "Participant", "Contribution" ) ) ){
                CRM_Core_Session::setStatus(ts('Profile is not configured for the selected action.'));
                return 0;
            }
        }
        
        $this->assign( 'mode'        , $this->_mode     );
        $this->assign( 'action'      , $this->_action   );
        $this->assign( 'fields'      , $this->_fields   );
        $this->assign( 'fieldset'    , (isset($this->_fieldset)) ? $this->_fieldset : "" ); 
        
        // do we need inactive options ?
        if ($this->_action & CRM_Core_Action::VIEW ) {
            $inactiveNeeded = true;
        } else {
            $inactiveNeeded = false;
        }
        
        $session  =& CRM_Core_Session::singleton( );

        // should we restrict what we display
        $admin = true;
        if ( $this->_mode == self::MODE_EDIT ) {
            $admin = false;
            // show all fields that are visibile: if we are a admin or the same user or in registration mode
            if ( CRM_Core_Permission::check( 'administer users' ) ||
                 $this->_id == $session->get( 'userID' )                 ) {
                $admin = true;
            } 
        }
        
        $userID = $session->get( 'userID' );
        $anonUser = false; // if false, user is not logged-in. 
        if ( ! $userID ) {
            require_once 'CRM/Core/BAO/LocationType.php';
            $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
            $primaryLocationType = $defaultLocationType->id;
            $anonUser = true; 
        }

        $addCaptcha   = array();
        $emailPresent = false;

        // add the form elements
        foreach ($this->_fields as $name => $field ) {
            // make sure that there is enough permission to expose this field
            if ( ! $admin && $field['visibility'] == 'User and User Admin Only' ) {
                unset( $this->_fields[$name] );
                continue;
            }
            
            // since the CMS manages the email field, suppress the email display if in
            // register mode which occur within the CMS form
            if ( $this->_mode == self::MODE_REGISTER &&
                 substr( $name, 0, 5 ) == 'email' ) {
                unset( $this->_fields[$name] );
                continue;
            }
            
            CRM_Core_BAO_UFGroup::buildProfile($this, $field, $this->_mode );
            
            if ($field['add_to_group_id']) {
                $addToGroupId = $field['add_to_group_id'];
            }
            
            //build show/hide array for uf groups
            // dont do this if gid is set (i.e. only one group)

            if ( $field['collapse_display'] && !in_array("'id_". $field['group_id']  . "_show'" , $sBlocks )) {
                $sBlocks[] = "'id_". $field['group_id']  . "_show'" ; 
                $hBlocks[] = "'id_". $field['group_id'] ."'"; 
            } else if ( !$field['collapse_display'] && !in_array("'id_". $field['group_id']  . "_show'" , $hBlocks )) {
                $hBlocks[] = "'id_". $field['group_id'] . "_show'" ; 
                $sBlocks[] = "'id_". $field['group_id'] ."'";   
            }
            
            //build array for captcha
            if ( $field['add_captcha'] ) {
                $addCaptcha[$field['group_id']] = $field['add_captcha'];
            }
            
            if ( ($name == 'email-Primary') || ($name == 'email-'. isset($primaryLocationType) ? $primaryLocationType : "") ) { 
                $emailPresent = true;
                $this->_mail = $name;
            }
        }
        
        $setCaptcha = false;
        
        // do this only for CiviCRM created forms
        if ( $this->_mode == self::MODE_CREATE ) {
            if (!empty($addCaptcha)) {
                $setCaptcha = true;
            } else if ($this->_gid ) {
                $dao = new CRM_Core_DAO_UFGroup();
                $dao->id = $this->_gid;
                $dao->addSelect( );
                $dao->addSelect( 'add_captcha', 'is_update_dupe' );
                if ( $dao->find( true ) ) {
                    if ( $dao->add_captcha ) {
                        $setCaptcha = true;
                    }
                    if ($dao->is_update_dupe) {
                        $this->_isUpdateDupe = true;
                    }
                }
            }
            
            if ($setCaptcha) {
                require_once 'CRM/Utils/ReCAPTCHA.php';
                $captcha =& CRM_Utils_ReCAPTCHA::singleton( );
                $captcha->add( $this );
                $this->assign( "isCaptcha" , true );
            }
        }

        if ( $this->_mode != self::MODE_SEARCH ) {
            if ( isset($addToGroupId) ) {
                $this->add('hidden', "group[$addToGroupId]", 1 );
                $this->assign( 'addToGroupId' , $addToGroupId );
                $this->_addToGroupID = $addToGroupId;
            }
            
            $showBlocks = implode(",",$sBlocks); 
            $hideBlocks = implode(",",$hBlocks); 
            
            $this->assign( 'showBlocks', $showBlocks ); 
            $this->assign( 'hideBlocks', $hideBlocks ); 
        }

        $action = CRM_Utils_Request::retrieve('action', 'String',$this, false, null );
        if ( $this->_mode == self::MODE_CREATE  ) { 
            require_once 'CRM/Core/BAO/CMSUser.php';
            CRM_Core_BAO_CMSUser::buildForm( $this, $this->_gid , $emailPresent ,$action );
        } else {                                                         
            $this->assign( 'showCMS', false );
        }
        
        $this->assign( 'groupId', $this->_gid ); 
        
        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
        }
   }

    /**
     * global form rule
     *
     * @param array  $fields the input form values
     * @param array  $files  the uploaded files if any
     * @param object $form   the form object
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
    static function formRule( &$fields, &$files, &$form )
    {
        $errors = array( );
        // if no values, return
        if ( empty( $fields ) ) {
            return true;
        }
       
        $cid = $register = null; 

        // hack we use a -1 in options to indicate that its registration 
        if ( $form->_id ) {
            $cid = $form->_id;
            $form->_isUpdateDupe = true;
        }

        if ( $form->_mode == CRM_Profile_Form::MODE_REGISTER ) {
            $register = true; 
        } 

        // dont check for duplicates during registration validation: CRM-375 
        if ( ! $register ) { 
            // fix for CRM-3240
            if ( CRM_Utils_Array::value( 'email-Primary', $fields ) ) {
                $fields['email'] = CRM_Utils_Array::value( 'email-Primary', $fields );
            }

            $session =& CRM_Core_Session::singleton();
            require_once 'CRM/Dedupe/Finder.php';
            $dedupeParams = CRM_Dedupe_Finder::formatParams($fields, 'Individual');
            $ids = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', 'Strict', array($session->get('userID')));
            if ( $ids ) {
                if ( $form->_isUpdateDupe ) {
                    if ( ! $form->_id ) {
                        $form->_id = $ids[0];
                    }
                } else {
                    $errors['_qf_default'] = ts( 'An account already exists with the same information.' );
                }
            }
        }

        foreach ($fields as $key => $value) {
            list($fieldName, $locTypeId, $phoneTypeId) = CRM_Utils_System::explode( '-', $key, 3 );
            if ($fieldName == 'state_province' && $fields["country-{$locTypeId}"]) {
                // Validate Country - State list            
                $countryId = $fields["country-{$locTypeId}"];
                $stateProvinceId = $value;
                
                if ($stateProvinceId && $countryId) {
                    $stateProvinceDAO =& new CRM_Core_DAO_StateProvince();
                    $stateProvinceDAO->id = $stateProvinceId;
                    $stateProvinceDAO->find(true);
                    
                    if ($stateProvinceDAO->country_id != $countryId) {
                        // country mismatch hence display error
                        $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                        $countries =& CRM_Core_PseudoConstant::country();
                        $errors[$key] = "State/Province " . $stateProvinces[$stateProvinceId] . " is not part of ". $countries[$countryId] . ". It belongs to " . $countries[$stateProvinceDAO->country_id] . "." ;
                    }
                }
            }

            if ($fieldName == 'county' && $fields["state_province-{$locTypeId}"]) {
                // Validate County - State list            
                $stateProvinceId = $fields["state_province-{$locTypeId}"];
                $countyId = $value;
                
                if ($countyId && $stateProvinceId) {
                    $countyDAO =& new CRM_Core_DAO_County();
                    $countyDAO->id = $countyId;
                    $countyDAO->find(true);
                    
                    if ($countyDAO->state_province_id != $stateProvinceId) {
                        // state province mismatch hence display error
                        $stateProvinces = CRM_Core_PseudoConstant::stateProvince();
                        $counties =& CRM_Core_PseudoConstant::county();
                        $errors[$key] = "County " . $counties[$countyId] . " is not part of ". $stateProvinces[$stateProvinceId] . ". It belongs to " . $stateProvinces[$countyDAO->state_province_id] . "." ;
                    }
                }
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
       
        if ( $this->_mode == self::MODE_REGISTER ) {
            require_once 'CRM/Core/BAO/Address.php';
            CRM_Core_BAO_Address::setOverwrite( false );
        }

        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        //used to send subcribe mail to the group which user want.
        //if the civimail component is enable.
        $mailingType = array( );
        if ( CRM_Core_Permission::access( 'CiviMail' )  && CRM_Utils_Array::value( 'group', $params ) ) {
            $result = null;
            foreach ( $params as $name => $values ) {
                if ( substr( $name, 0, 6 ) == 'email-' ) {
                    $result['email'] = $values ;
                }
            }
            //array of group id, subscribed by contact
            $contactGroup = array( );
            if( $this->_id ) {
                $contactGroups = new CRM_Contact_DAO_GroupContact();
                $contactGroups->contact_id = $this->_id;
                $contactGroups->status     = 'Added';
                $contactGroups->find();
                $contactGroup = array();
                while( $contactGroups->fetch() ) { 
                    $contactGroup[] = $contactGroups->group_id;
                }
            }
            if ( CRM_Utils_Array::value( 'email' , $result ) ) {
                require_once 'CRM/Contact/DAO/Group.php';
                foreach ( $params['group'] as $key => $val ) {
                    $groupTypes = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group',
                                                               $key, 'group_type', 'id' );
                    $groupType = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                                          substr( $groupTypes, 1, -1 ) );
                    //filter group of mailing type and unset it from params
                    if ( CRM_Utils_Array::key( '2', $groupType ) ) {
                        //if group is already subscribed , ignore it 
                        $groupExist = CRM_Utils_Array::key( $key, $contactGroup );
                        if ( ! isset( $groupExist ) ) {
                            $mailingType[] = $key ;
                            unset( $params['group'][$key] );
                        }
                    }
                }
            }
        }

        $this->_id = CRM_Contact_BAO_Contact::createProfileContact($params, $this->_fields,
                                                                   $this->_id, $this->_addToGroupID,
                                                                   $this->_gid, $this->_ctype,
                                                                   true );
        //mailing type group
        if ( ! empty ( $mailingType ) ) {
            require_once 'CRM/Mailing/Event/BAO/Subscribe.php';
            CRM_Mailing_Event_BAO_Subscribe::commonSubscribe( $mailingType, $result );
        }

        require_once 'CRM/Core/BAO/UFGroup.php'; 
        if ( ! ( $this->_mode == self::MODE_REGISTER ) ) {
            $values = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues($this->_gid,$this->_id,null);
            if ( ! is_null( $values['email'] ) ) {
                CRM_Core_BAO_UFGroup::commonSendMail($this->_id, $values);
            }
        }

        //create CMS user (if CMS user option is selected in profile)
        if ( CRM_Utils_Array::value( 'cms_create_account', $params ) &&
             $this->_mode == self::MODE_CREATE ) {
            $params['contactID'] = $this->_id;
            require_once "CRM/Core/BAO/CMSUser.php";
            if ( ! CRM_Core_BAO_CMSUser::create( $params, $this->_mail ) ) {
                CRM_Core_Session::setStatus( ts('Your profile is not saved and Account is not created.') );
                $transaction->rollback( );
                return CRM_Utils_System::redirect( CRM_Utils_System::url('civicrm/profile/create',
                                                                         'reset=1&gid=' . $this->_gid) );
            }
        }

        $transaction->commit( );
    }

    function getTemplateFileName() {
        if ( $this->_gid ) {
            $templateFile = "CRM/Profile/Form/{$this->_gid}/{$this->_name}.tpl";
            $template =& CRM_Core_Form::getTemplate( );
            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }

}
