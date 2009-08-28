<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contact/Form/Location.php';
require_once 'CRM/Custom/Form/CustomData.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_Contact extends CRM_Core_Form
{
    /**
     * The contact type of the form
     *
     * @var string
     */
    public $_contactType;
    
    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactSubType;
    
    /**
     * The contact id, used when editing the form
     *
     * @var int
     */
    public $_contactId;
    
    /**
     * the default group id passed in via the url
     *
     * @var int
     */
    public $_gid;
    
    /**
     * the default tag id passed in via the url
     *
     * @var int
     */
    public $_tid;
    
    /**
     * name of de-dupe button
     *
     * @var string
     * @access protected
     */
    protected $_dedupeButtonName;
    
    /**
     * name of optional save duplicate button
     *
     * @var string
     * @access protected
     */
    protected $_duplicateButtonName;
    
    protected $_editOptions = array( );
    
    public $_blocks;
    
    public $_values = array( );
    
    public $_action;
    /**
     * The array of greetings with option group and filed names
     *
     * @var array
     */
    public $_greetings;
    
    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( )
    {
        $this->_action  = CRM_Utils_Request::retrieve('action', 'String',$this, false, 'add' );
        
        $this->_dedupeButtonName    = $this->getButtonName( 'refresh', 'dedupe'    );
        $this->_duplicateButtonName = $this->getButtonName( 'upload',  'duplicate' );
        
        $session = & CRM_Core_Session::singleton( );
        if ( $this->_action == CRM_Core_Action::ADD ) {
            // check for add contacts permissions
            require_once 'CRM/Core/Permission.php';
            if ( ! CRM_Core_Permission::check( 'add contacts' ) ) {
                CRM_Utils_System::permissionDenied( );
                return;
            }
            $this->_contactType = CRM_Utils_Request::retrieve( 'ct', 'String',
                                                               $this, true, null, 'REQUEST' );
            if ( ! in_array( $this->_contactType,
                             array( 'Individual', 'Household', 'Organization' ) ) ) {
                CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
            }
            
            $this->_contactSubType = CRM_Utils_Request::retrieve( 'cst','String', 
                                                                  CRM_Core_DAO::$_nullObject,
                                                                  false,null,'GET' );
            $this->_gid = CRM_Utils_Request::retrieve( 'gid', 'Integer',
                                                       CRM_Core_DAO::$_nullObject,
                                                       false, null, 'GET' );
            $this->_tid = CRM_Utils_Request::retrieve( 'tid', 'Integer',
                                                       CRM_Core_DAO::$_nullObject,
                                                       false, null, 'GET' );
            if ( $this->_contactSubType ) {
                CRM_Utils_System::setTitle( ts( 'New %1', array(1 => $this->_contactSubType ) ) );
            } else {
                $title = ts( 'New Individual' );
                if ( $this->_contactType == 'Household' ) {
                    $title = ts( 'New Household' );
                } else if ( $this->_contactType == 'Organization' ) {
                    $title = ts( 'New Organization' );
                }
                CRM_Utils_System::setTitle( $title );
            }
            $session->pushUserContext(CRM_Utils_System::url('civicrm/dashboard', 'reset=1'));
            $this->_contactId = null;
        } else {
            //update mode
            if ( ! $this->_contactId ) {
                $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
            }
            
            if ( $this->_contactId ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                $contact =& new CRM_Contact_DAO_Contact( );
                $contact->id = $this->_contactId;
                if ( ! $contact->find( true ) ) {
                    CRM_Core_Error::statusBounce( ts('contact does not exist: %1', array(1 => $this->_contactId)) );
                }
                $this->_contactType = $contact->contact_type;
                $this->_contactSubType = $contact->contact_sub_type;
                
                // check for permissions
                require_once 'CRM/Contact/BAO/Contact/Permission.php';
                if ( ! CRM_Contact_BAO_Contact_Permission::allow( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                    CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to edit this contact.') );
                }
                
                list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
                
                CRM_Utils_System::setTitle( $displayName, $contactImage . ' ' . $displayName ); 
                $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid='. $this->_contactId ));
                
                $values = $this->get( 'values');
                // get contact values.
                if ( !empty( $values ) ) {
                    $this->_values = $values;
                } else {
                    $params = array( 'id'         => $this->_contactId,
                                     'contact_id' => $this->_contactId ) ;
                    $contact = CRM_Contact_BAO_Contact::retrieve( $params, $this->_values, true );
                    $this->set( 'values', $this->_values );
                }
            } else {
                CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
            }
        }
        
        $this->_editOptions = $this->get( 'contactEditOptions' ); 
        if ( CRM_Utils_System::isNull( $this->_editOptions ) ) {
            require_once 'CRM/Core/BAO/Preferences.php';
            $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, 
                                                                           false, 'name', true, 'AND v.filter = 0' );
            $this->set( 'contactEditOptions', $this->_editOptions );
        }
        
        // build demographics only for Individual contact type
        if ( $this->_contactType != 'Individual' &&
             array_key_exists( 'Demographics', $this->_editOptions ) ) {
            unset( $this->_editOptions['Demographics'] );
        }
        
        // in update mode don't show notes
        if ( $this->_contactId && array_key_exists( 'Notes', $this->_editOptions ) ) {
            unset( $this->_editOptions['Notes'] );
        }
        
        
        $this->assign( 'editOptions', $this->_editOptions );
        $this->assign( 'contactType', $this->_contactType );
        
        // get the location blocks.
        $this->_blocks = $this->get( 'blocks' );
        if ( CRM_Utils_System::isNull( $this->_blocks ) ) {
            $this->_blocks = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, 
                                                                     false, 'name', true, 'AND v.filter = 1' );
            $this->set( 'blocks', $this->_blocks );
        }
        $this->assign( 'blocks', $this->_blocks );
        
        if ( array_key_exists( 'CustomData', $this->_editOptions ) ) {
            //only custom data has preprocess hence directly call it
            CRM_Custom_Form_CustomData::preProcess( $this, null, null, 1, $this->_contactType, $this->_contactId );
        }
        
        // this is needed for custom data.
        $this->assign( 'entityID', $this->_contactId );
        
        // also keep the convention.
        $this->assign( 'contactId', $this->_contactId );
        
        // location blocks.
        CRM_Contact_Form_Location::preProcess( $this );
    }
    
    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = $this->_values;
        $params   = array( );
        
        if ( $this->_action & CRM_Core_Action::ADD ) {
            if ( array_key_exists( 'TagsAndGroups', $this->_editOptions ) ) {
                // set group and tag defaults if any
                if ( $this->_gid ) {
                    $defaults['group'][$this->_gid] = 1;
                }
                if ( $this->_tid ) {
                    $defaults['tag'][$this->_tid] = 1;
                }
            }
        } else {
            if ( isset( $this->_elementIndex[ "shared_household" ] ) ) {
                $sharedHousehold = $this->getElementValue( "shared_household" );
                if ( $sharedHousehold ) {
                    $this->assign('defaultSharedHousehold', $sharedHousehold );
                } elseif ( CRM_Utils_Array::value('mail_to_household_id', $defaults) ) {
                    $defaults['use_household_address'] = true;
                    $this->assign('defaultSharedHousehold', $defaults['mail_to_household_id'] );
                }
                $defaults['shared_household_id'] = CRM_Utils_Array::value( 'mail_to_household_id', $defaults );
                if ( array_key_exists(1, $defaults['address']) ) {
                    $this->assign( 'sharedHouseholdAddress', $defaults['address'][1]['display'] );
                }
            }
            require_once 'CRM/Contact/BAO/Relationship.php';
            $currentEmployer = CRM_Contact_BAO_Relationship::getCurrentEmployer( array( $this->_contactId ) );
            $defaults['current_employer_id'] = CRM_Utils_Array::value( 'org_id', $currentEmployer[$this->_contactId] );
            $this->assign( 'currentEmployer', $defaults['current_employer_id'] );            
        }
        
        // set defaults for blocks ( custom data, address, communication preference, notes, tags and groups )
        foreach( $this->_editOptions as $name => $label ) {                
            if ( !in_array( $name, array( 'Address', 'Notes' ) ) ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name ) . ".php");
                eval( 'CRM_Contact_Form_Edit_' . $name . '::setDefaultValues( $this, $defaults );' );
            }
        }
        
        //set location type and country to default for each block
        $this->blockSetDefaults( $defaults );
        
        return $defaults;
    }
    
    /*
     * do the set default related to location type id, 
     * primary location,  default country
     *
     */
    function blockSetDefaults( &$defaults ) {
        $locationTypeKeys = array_filter(array_keys( CRM_Core_PseudoConstant::locationType() ), 'is_int' );
        sort( $locationTypeKeys );
        
        // get the default location type
        require_once 'CRM/Core/BAO/LocationType.php';
        
        $locationType = CRM_Core_BAO_LocationType::getDefault( );
        
        // unset primary location type
        $primaryLocationTypeIdKey = CRM_Utils_Array::key( $locationType->id, $locationTypeKeys );
        unset( $locationTypeKeys[ $primaryLocationTypeIdKey ] );
        
        // reset the array sequence
        $locationTypeKeys = array_values( $locationTypeKeys );
        
        // get default phone and im provider id.
        require_once 'CRM/Core/OptionGroup.php';
        $defPhoneTypeId  = key( CRM_Core_OptionGroup::values( 'phone_type', false, false, false, ' AND is_default = 1' ) );
        $defIMProviderId = key( CRM_Core_OptionGroup::values( 'instant_messenger_service', 
                                                              false, false, false, ' AND is_default = 1' ) );
        
        $allBlocks = $this->_blocks;
        if ( array_key_exists( 'Address', $this->_editOptions ) ) {
            $allBlocks['Address'] = $this->_editOptions['Address'];
        }
        
        $config =& CRM_Core_Config::singleton( );
        foreach ( $allBlocks as $blockName => $label ) {
            $name = strtolower( $blockName );
            if ( array_key_exists( $name, $defaults ) && !CRM_Utils_System::isNull( $defaults[$name] ) ) continue;
            for( $instance = 1; $instance<= $this->get( $blockName ."_Block_Count" ); $instance++ ) {
                //set location to primary for first one.
                if ( $instance == 1 ) {
                    $defaults[$name][$instance]['is_primary']       = true;
                    $defaults[$name][$instance]['location_type_id'] = $locationType->id;
                } else {
                    $locTypeId = isset( $locationTypeKeys[$instance-1] )?$locationTypeKeys[$instance-1]:$locationType->id;
                    $defaults[$name][$instance]['location_type_id'] = $locTypeId; 
                }
                
                //set default country
                if ( $name == 'address' && $config->defaultContactCountry ) {
                    $defaults[$name][$instance]['country_id'] = $config->defaultContactCountry;
                }
                
                //set default phone type.
                if ( $name == 'phone' && $defPhoneTypeId ) {
                    $defaults[$name][$instance]['phone_type_id'] = $defPhoneTypeId;
                }
                
                //set default im provider.
                if ( $name == 'im' && $defIMProviderId ) {
                    $defaults[$name][$instance]['provider_id'] = $defIMProviderId;
                }
            }
        }
        
        // set defaults for country-state widget
        if ( CRM_Utils_Array::value( 'address', $defaults ) && is_array( $defaults['address'] ) ) {
            require_once 'CRM/Contact/Form/Edit/Address.php';
            foreach ( $defaults['address'] as $blockId => $values ) {
                CRM_Contact_Form_Edit_Address::fixStateSelect( $this,
                                                               "address[$blockId][country_id]",
                                                               "address[$blockId][state_province_id]",
                                                               CRM_Utils_Array::value( 'country_id',
                                                                                       $values, $config->defaultContactCountry ) );
                
            }
        }
        
    }
    
    /**
     * This function is used to add the rules (mainly global rules) for form.
     * All local rules are added near the element
     *
     * @return None
     * @access public
     * @see valid_date
     */
    function addRules( )
    {
        // skip adding formRules when custom data is build
        if ( $this->_addBlockName || ($this->_action & CRM_Core_Action::DELETE) ) {
			return;
		}
        
        $this->addFormRule( array( 'CRM_Contact_Form_Edit_'. $this->_contactType,   'formRule' ), $this->_contactId );
        if ( array_key_exists('CommunicationPreferences', $this->_editOptions) ) {
            $this->addFormRule( array( 'CRM_Contact_Form_Edit_CommunicationPreferences','formRule' ), $this );
        }
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields     posted values of the form
     * @param array $errors     list of errors to be posted back to the form
     * @param int   $contactId  contact id if doing update.
     *
     * @return $primaryID emal/openId
     * @static
     * @access public
     */
    static function formRule( &$fields, &$errors, $contactId = null )
    {
        $config =& CRM_Core_Config::singleton( );
        if ( $config->civiHRD && ! isset( $fields['tag'] ) ) {
            $errors["tag"] = ts('Please select at least one tag.');
        }
        
        // validations.
        //1. for each block only single value can be marked as is_primary = true.
        //2. location type id should be present if block data present.
        //3. check open id across db and other each block for duplicate.
        //4. at least one location should be primary.
        //5. also get primaryID from email or open id block.
        
        // take the location blocks.
        $blocks = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, 
                                                          false, 'name', true, 'AND v.filter = 1' );
        $otherEditOptions = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null,
                                                                    false, 'name', true, 'AND v.filter = 0');
        //get address block inside.
        if ( array_key_exists( 'Address', $otherEditOptions ) ) {
            $blocks['Address'] = $otherEditOptions['Address'];
        }
        
        $openIds = array( );
        $primaryID = false;
        foreach ( $blocks as $name => $label ) {
            $dataExists = $isPrimary = 0;
            $name = strtolower( $name );
            if ( is_array( $fields[$name] ) ) {
                foreach ( $fields[$name] as $instance => $blockValues ) {
                    $dataExists += self::blockDataExists( $blockValues );
                    if ( !$dataExists && $name == 'address' &&  $instance == 1 ) {
                        $dataExists = CRM_Utils_Array::value( 'use_household_address', $fields );
                    }
                    
                    if ( CRM_Utils_Array::value( 'is_primary', $blockValues ) ) {
                        $isPrimary++;
                        if ( $isPrimary > 1 ) {
                            $errors["{$name}[$instance][is_primary]"] = ts('Only one %1 can be marked as primary.', 
                                                                           array( 1 => $label ) );
                        }
                    }
                    
                    if ( $dataExists && !CRM_Utils_Array::value( 'location_type_id', $blockValues ) ) {
                        $errors["{$name}[$instance][location_type_id]"] = 
                            ts('The Location Type should be set if there is  %1 information.', array( 1=> $label ) );
                    }
                    
                    if ( $isPrimary && !$primaryID 
                         && in_array( $name, array( 'email', 'openid' ) ) && CRM_Utils_Array::value( $name, $blockValues ) ) {
                        $primaryID = $blockValues[$name];
                    }
                    
                    if ( $name == 'openid' && CRM_Utils_Array::value( $name, $blockValues ) ) {
                        require_once 'CRM/Core/DAO/OpenID.php';
                        $oid =& new CRM_Core_DAO_OpenID( );
                        $oid->openid = $openIds[$instance] = CRM_Utils_Array::value( $name, $blockValues );
                        $cid = isset($contactId) ? $contactId : 0;
                        if ( $oid->find(true) && ($oid->contact_id != $cid) ) {
                            $errors["{$name}[$instance][openid]"] = ts('%1 already exist.', array( 1 => $blocks['OpenID'] ) );
                        }
                    }
                }
                
                if ( $dataExists && !$isPrimary ) {
                    $errors["{$name}[1][is_primary]"] = ts('One %1 should be marked as primary.', array( 1 => $label ) );
                }
            }
        }
        
        //do validations for all opend ids they should be distinct.
        if ( !empty( $openIds ) && ( count( array_unique($openIds) ) != count($openIds) ) ) {
            foreach ( $openIds as $instance => $value ) {
                if ( !array_key_exists( $instance, array_unique($openIds) ) ) {
                    $errors["openid[$instance][openid]"] = ts('%1 already used.', array( 1 => $blocks['OpenID'] ) );
                }
            }
        }
        
        return $primaryID;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        //load form for child blocks
        if ( $this->_addBlockName ) {
            require_once( str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $this->_addBlockName ) . ".php");
            return eval( 'CRM_Contact_Form_Edit_' . $this->_addBlockName . '::buildQuickForm( $this );' );
        }
        
        //build contact type specific fields
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $this->_contactType) . ".php");
        eval( 'CRM_Contact_Form_Edit_' . $this->_contactType . '::buildQuickForm( $this, $this->_action );' );
        
        // build edit blocks ( custom data, demographics, communication preference, notes, tags and groups )
        foreach( $this->_editOptions as $name => $label ) {                
            if ( $name == 'Address' ) {
                $this->_blocks['Address'] = $this->_editOptions['Address'];
                continue;
            }
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name ) . ".php");
            eval( 'CRM_Contact_Form_Edit_' . $name . '::buildQuickForm( $this );' );
        }
        
        // build location blocks.
        CRM_Contact_Form_Location::buildQuickForm( $this );
        
        // add the dedupe button
        $this->addElement('submit', 
                          $this->_dedupeButtonName,
                          ts( 'Check for Matching Contact(s)' ) );
        $this->addElement('submit', 
                          $this->_duplicateButtonName,
                          ts( 'Save Matching Contact' ) );
        $this->addElement('submit', 
                          $this->getButtonName( 'next', 'sharedHouseholdDuplicate' ),
                          ts( 'Save With Duplicate Household' ) );
        
        // make this form an upload since we dont know if the custom data injected dynamically
        // is of type file etc $uploadNames = $this->get( 'uploadNames' );
        $this->addButtons( array(
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save and New'),
                                         'subName'   => 'new' ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }
    
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // check if dedupe button, if so return.
        $buttonName = $this->controller->getButtonName( );
        if ( $buttonName == $this->_dedupeButtonName ) {
            return;
        }
        
        //get the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );            
        
        //get the related id for shared / current employer
        if ( CRM_Utils_Array::value( 'shared_household_id',$params ) ) {
            $params['shared_household'] = $params['shared_household_id'];
        }
        if ( is_Numeric ( CRM_Utils_Array::value( 'current_employer_id', $params ) 
			&& CRM_Utils_Array::value( 'current_employer', $params ) ) ) {
			$params['current_employer'] = $params['current_employer_id'];
		}
      
        $params['contact_type'] = $this->_contactType;
        if ( $this->_contactId ) {
            $params['contact_id'] = $this->_contactId;
        }
        
        //make deceased date null when is_deceased = false
        if ( $this->_contactType == 'Individual' && 
             CRM_Utils_Array::value( 'Demographics',  $this->_editOptions ) &&
             !CRM_Utils_Array::value( 'is_deceased', $params ) ) {
            $params['is_deceased']        = false;
            $params['deceased_date']['M'] = null;
            $params['deceased_date']['d'] = null;
            $params['deceased_date']['Y'] = null;
        }
        
        // action is taken depending upon the mode
        require_once 'CRM/Utils/Hook.php';
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            CRM_Utils_Hook::pre( 'edit', $params['contact_type'], $params['contact_id'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', $params['contact_type'], null, $params );
        }
        
        require_once 'CRM/Core/BAO/CustomField.php';
        $customFields = CRM_Core_BAO_CustomField::getFields( $params['contact_type'], false, true );
        $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                   $customFields,
                                                                   $this->_contactId,
                                                                   $params['contact_type'],
                                                                   true );
        
        if ( array_key_exists( 'CommunicationPreferences',  $this->_editOptions ) ) {
            // this is a chekbox, so mark false if we dont get a POST value
            $params['is_opt_out'] = CRM_Utils_Array::value( 'is_opt_out', $params, false );
        }
        
        // copy household address, if use_household_address option (for individual form) is checked
        if ( $this->_contactType == 'Individual' ) {
            if ( CRM_Utils_Array::value( 'use_household_address', $params ) && 
                 CRM_Utils_Array::value( 'shared_household',$params ) ) {
                if ( is_numeric( $params['shared_household'] ) ) {
                    CRM_Contact_Form_Edit_Individual::copyHouseholdAddress( $params );
                }
                CRM_Contact_Form_Edit_Individual::createSharedHousehold( $params );
            } else { 
                $params['mail_to_household_id'] = 'null';
            }
        } else {
            $params['mail_to_household_id'] = 'null';
        }
        
        // cleanup unwanted location blocks
        if ( CRM_Utils_Array::value( 'contact_id', $params ) && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            require_once 'CRM/Core/BAO/Location.php';
            CRM_Core_BAO_Location::cleanupContactLocations( $params );
        }
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact =& CRM_Contact_BAO_Contact::create( $params, true,false );
        
        if ( $this->_contactType == 'Individual' && ( CRM_Utils_Array::value( 'use_household_address', $params )) &&
             CRM_Utils_Array::value( 'mail_to_household_id',$params ) ) {
            // add/edit/delete the relation of individual with household, if use-household-address option is checked/unchecked.
            CRM_Contact_Form_Edit_Individual::handleSharedRelation($contact->id , $params );
        }
        
        if ( $this->_contactType == 'Household' && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            //TO DO: commented because of schema changes
            require_once 'CRM/Contact/Form/Edit/Household.php';
            CRM_Contact_Form_Edit_Household::synchronizeIndividualAddresses( $contact->id );
        }
        
        if ( array_key_exists( 'TagsAndGroups', $this->_editOptions ) ) {
            //add contact to tags
            require_once 'CRM/Core/BAO/EntityTag.php';
            CRM_Core_BAO_EntityTag::create( $params['tag'], $params['contact_id'] );
        }
        
        // here we replace the user context with the url to view this contact
        $session =& CRM_Core_Session::singleton( );
        CRM_Core_Session::setStatus(ts('Your %1 contact record has been saved.', array(1 => $contact->contact_type_display)));
        
        $buttonName = $this->controller->getButtonName( );
        if ( ($buttonName == $this->getButtonName( 'next', 'new' ) ) ||
             ($buttonName == $this->getButtonName( 'upload', 'new' ) ) ) {
            require_once 'CRM/Utils/Recent.php';
            
            // add the recently viewed contact
            $displayName = CRM_Contact_BAO_Contact::displayName( $contact->id );
            CRM_Utils_Recent::add( $displayName,
                                   CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $contact->id ),
                                   $contact->id,
                                   $this->_contactType,
                                   $contact->id,
                                   $displayName );
            $session->replaceUserContext(CRM_Utils_System::url('civicrm/contact/add', 'reset=1&ct=' . $contact->contact_type ) );
        } else {
            $session->replaceUserContext(CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $contact->id));
        }
        
        // now invoke the post hook
        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Utils_Hook::post( 'edit', $params['contact_type'], $contact->id, $contact );
        } else {
            CRM_Utils_Hook::post( 'create', $params['contact_type'], $contact->id, $contact );
        }
    }
    
    /**
     * is there any real significant data in the hierarchical location array
     *
     * @param array $fields the hierarchical value representation of this location
     *
     * @return boolean true if data exists, false otherwise
     * @static
     * @access public
     */
    static function blockDataExists( &$fields ) {
        if ( !is_array( $fields ) ) return false;
        
        static $skipFields = array( 'location_type_id', 'is_primary', 'phone_type_id', 'provider_id', 'country_id' );
        foreach ( $fields as $name => $value ) {
            $skipField = false;
            foreach ( $skipFields as $skip ) {
                if ( strpos( "[$skip]", $name ) !== false ) {
                    if($name == 'phone') continue;
                    $skipField = true;
                    break;
                }
            }
            if ( $skipField ) {
                continue;
            }
            if ( is_array( $value ) ) {
                if ( self::blockDataExists( $value ) ) {
                    return true;
                }
            } else {
                if ( ! empty( $value ) ) {
                    return true;
                }
            }
        }
        
        return false;
    }
}


