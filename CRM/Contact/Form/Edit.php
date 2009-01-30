<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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
require_once 'CRM/Custom/Form/CustomData.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_Edit extends CRM_Core_Form
{
    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactType;

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
    protected $_gid;

    /**
     * the default tag id passed in via the url
     *
     * @var int
     */
    protected $_tid;
    
    /**
     * the group tree data
     *
     * @var array
     */
    public $_groupTree;    

    /**
     * what blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

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

    protected $_maxLocationBlocks = 0;

    protected $_editOptions = array( );

    protected $_showCommBlock = true;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_cdType = CRM_Utils_Array::value( 'type', $_GET );
        
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
 
        $session = & CRM_Core_Session::singleton( ); 
        // reset action from the session
        $this->_action              = CRM_Utils_Request::retrieve('action', 'String', 
                                                                  $this, false, 'add' );
        
        $this->_dedupeButtonName    = $this->getButtonName( 'refresh', 'dedupe'    );
        $this->_duplicateButtonName = $this->getButtonName( 'next'   , 'duplicate' );
    
        // find the system config related location blocks
        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_maxLocationBlocks = CRM_Core_BAO_Preferences::value( 'location_count' );

        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options' );

        $configItems = array( 'CommBlock',
                              'Demographics',
                              'TagsAndGroups',
                              'Notes' );

        foreach ( $configItems as $c ) {
            $varName = '_show' . $c;
            $this->$varName = $this->_editOptions[$c];
            $this->assign( substr( $varName, 1 ), $this->$varName );
        }

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
            $session->pushUserContext(CRM_Utils_System::url());
            $this->_contactId = null;
        } else {
            // this is update mode, first get the id from the session
            // else get it from the REQUEST
          
            if ( ! $this->_contactId ) {
                $this->_contactId   = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
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
				
                // need this for custom data in edit mode	
                $this->assign('entityID', $this->_contactId );
                
                //get the no of locations for the contact
                $this->_maxLocationBlocks = CRM_Contact_BAO_Contact::getContactLocations( $this->_contactId );
                $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid='. $this->_contactId ));
            } else {
                CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
            }
        }
        
        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            CRM_Custom_Form_CustomData::preProcess( $this, 'null', 'null', 1, $this->_contactType, $this->_contactId );
            CRM_Custom_Form_CustomData::buildQuickForm( $this );
            CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }
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
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::setDefaultValues( $this );
        }

        $defaults = array( );
        $params   = array( );

        $config =& CRM_Core_Config::singleton( );

        if ( $this->_action & CRM_Core_Action::ADD ) {
            if ( $this->_showTagsAndGroups ) {
                // set group and tag defaults if any
                if ( $this->_gid ) {
                    $defaults['group'][$this->_gid] = 1;
                }
                if ( $this->_tid ) {
                    $defaults['tag'][$this->_tid] = 1;
                }
            }

            if ( $this->_maxLocationBlocks >= 1 ) {
                // set the is_primary location for the first location
                $defaults['location']    = array( );
                
                $locationTypeKeys = array_filter(array_keys( CRM_Core_PseudoConstant::locationType() ), 'is_int' );
                sort( $locationTypeKeys );
                                             
                // get the default location type
                require_once 'CRM/Core/BAO/LocationType.php';
                $locationType    = CRM_Core_BAO_LocationType::getDefault();
                
                // unset primary location type
                $primaryLocationTypeIdKey = CRM_Utils_Array::key( $locationType->id, $locationTypeKeys );
                unset( $locationTypeKeys[ $primaryLocationTypeIdKey ] );
                
                // reset the array sequence
                $locationTypeKeys = array_values( $locationTypeKeys );
                                
                // also set the location types for each location block
                for ( $i = 0; $i < $this->_maxLocationBlocks; $i++ ) {
                    $defaults['location'][$i+1] = array( );
                    if ( $i == 0 ) {
                        $defaults['location'][$i+1]['location_type_id'] = $locationType->id;
                    } else {
                        //set default location type (if more than 1 location type ) since the default
                        //country is set to location type,other wise, it show validation error.
                        $defaults['location'][$i+1]['location_type_id'] = $locationTypeKeys[$i-1];
                    }
                    $defaults['location'][$i+1]['address'] = array( );
                    if ( $config->defaultContactCountry ) {
                        $defaults['location'][$i+1]['address']['country_id'] = $config->defaultContactCountry;
                        $locationID = $i+1;
                    }
                }
            }
            require_once 'CRM/Core/OptionGroup.php';
            $defaults['greeting_type_id'] = CRM_Core_OptionGroup::values( 'greeting_type', true, null, 
                                                                       null, ' AND v.is_default = 1' );
        } else {
            // this is update mode
            // get values from contact table
            $params['id'] = $params['contact_id'] = $this->_contactId;
            $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults );
            
            $locationExists = array( );
            
            foreach( $contact->location as $index => $loc) {
                $locationExists[] = CRM_Utils_Array::value( 'location_type_id', $loc );
                //to get the billing location
                $defaults['location'][$index]['is_billing'] = CRM_Utils_Array::value( 'is_billing' ,
                                                                                      $defaults['location'][$index]['address'] );
            }
            $this->assign( 'locationExists' , $locationExists );
            
            $this->assign( 'contactId' , $this->_contactId );
            // also set contact_type, since this is used in showHide routines 
            // to decide whether to display certain blocks (demographics)
            $this->_contactType = CRM_Utils_Array::value( 'contact_type', $defaults );

            if ( $this->_showTagsAndGroups ) {
                // set the group and tag ids
                CRM_Contact_Form_GroupTag::setDefaults( $this->_contactId,                      
                                                        $defaults, 
                                                        CRM_Contact_Form_GroupTag::ALL );
            }          
        }
      
        // set the default for 'use_household_address' checkbox and Select-Household.
        if ( isset( $this->_elementIndex[ "shared_household" ] ) ) {
            $sharedHousehold = $this->getElementValue( "shared_household" );
            if ( $sharedHousehold ) {
                $this->assign('defaultSharedHousehold', $sharedHousehold );
            } elseif ( CRM_Utils_Array::value('mail_to_household_id', $defaults) ) {
                $defaults['use_household_address'] = true;
                $this->assign('defaultSharedHousehold', $defaults['mail_to_household_id'] );
            }
        }

        //check primary for first location
        $defaults['location'][1]['is_primary'] = true;
        
        if ( ! empty( $_POST ) ) {
            $this->setShowHide( $_POST, true );
        } else {
            if ( $this->_action & CRM_Core_Action::ADD ) {
                $this->setShowHide( $defaults, false );
            } else {
                $this->setShowHide( $defaults, true );
            }
        }

        // do we need inactive options ?
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }

        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            require_once 'CRM/Contact/BAO/Relationship.php';
            $currentEmployer = CRM_Contact_BAO_Relationship::getCurrentEmployer( array( $this->_contactId ) );
            $this->assign( 'currentEmployer',  CRM_Utils_Array::value( 'org_id', $currentEmployer[$this->_contactId] ) );
        }

        //set defaults for country-state widget
        if ( ! empty ( $defaults['location'] ) ) {
            foreach ( $defaults['location'] as $key => $value ) {
                CRM_Contact_Form_Address::fixStateSelect( $this,
                                                          "location[$key][address][country_id]",
                                                          "location[$key][address][state_province_id]",
                                                          CRM_Utils_Array::value( 'country_id',
                                                                                  CRM_Utils_Array::value( 'address',
                                                                                                          $value ),
                                                                                  $config->defaultContactCountry ) );

                if ( isset( $value['address'] ) &&
                     isset( $value['address']['display']) ) {
                    $this->assign( "location_{$key}_address_display", 
                                   str_replace("\n", "<br/>", $value['address']['display']) );
                }
            }
        }

        return $defaults;
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param array   $defaults the array of default values
     * @param boolean $force    should we set show hide based on input defaults
     *
     * @return void
     */
    function setShowHide( &$defaults, $force ) 
    {
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );

        if ( $this->_showCommBlock ) {
            $this->_showHide->addShow( 'commPrefs' );
        }

        if ( $this->_showDemographics &&
             $this->_contactType == 'Individual' ) {
            $this->_showHide->addShow( 'id_demographics_show' );
            $this->_showHide->addHide( 'id_demographics' );
        }

        // first do the defaults showing
        $config =& CRM_Core_Config::singleton( );
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide,
                                                        $this->_maxLocationBlocks );
 
        if ( $this->_showNotes && 
             ( $this->_action & CRM_Core_Action::ADD ) ) {
            // notes are only included in the template for New Contact
            $this->_showHide->addShow( 'id_notes_show' );
            $this->_showHide->addHide( 'id_notes' );
        }

        if ( $this->_showTagsAndGroups ) {
            //add group and tags
            $contactGroup = $contactTag = array( );
            if ($this->_contactId) {
                $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $this->_contactId, 'Added' );
                $contactTag   =& CRM_Core_BAO_EntityTag::getTag($this->_contactId);
            }
            
            if ( empty($contactGroup) || empty($contactTag) ) {
                $this->_showHide->addShow( 'group_show' );
                $this->_showHide->addHide( 'group' );
            } else {
                $this->_showHide->addShow( 'group' );
                $this->_showHide->addHide( 'group_show' );
            }
        }

        // is there any demographic data?
        if ( $this->_showDemographics ) {
            if ( CRM_Utils_Array::value( 'gender_id'  , $defaults ) ||
                 CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
                $this->_showHide->addShow( 'id_demographics' );
                $this->_showHide->addHide( 'id_demographics_show' );
            }
        }

        if ( $force ) {
            $locationDefaults = CRM_Utils_Array::value( 'location', $defaults );
            $config =& CRM_Core_Config::singleton( );
            CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                       $locationDefaults,
                                                       $this->_maxLocationBlocks );
        }
        
        $this->_showHide->addToTemplate( );
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
        if ( $this->_cdType ) {
			return;
		}
        $this->addFormRule( array( 'CRM_Contact_Form_' . $this->_contactType, 'formRule' ), $this->_contactId );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }

        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Contact');
        $this->assign('customDataSubType',  $this->_contactType );

        require_once 'CRM/Contact/Form/Location.php';

        // assign a few constants used by all display elements
        // we can obsolete this when smarty can access class constans directly
        $config =& CRM_Core_Config::singleton( );
        $this->assign( 'locationCount', $this->_maxLocationBlocks + 1 );
        $this->assign( 'blockCount'   , CRM_Contact_Form_Location::BLOCKS + 1 );
        $this->assign( 'contact_type' , $this->_contactType );
        
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_" . $this->_contactType) . ".php");
        eval( 'CRM_Contact_Form_' . $this->_contactType . '::buildQuickForm( $this, $this->_action );' );

        // add the communications block
        if ( $this->_showCommBlock ) {
            self::buildCommunicationBlock($this);
        }

        // greeting type
        $this->addElement('select', 'greeting_type_id', ts('Greeting'), array('' => ts('- select -')) + CRM_Core_PseudoConstant::greeting(), array( 'onchange' => " showGreeting();") );

        // custom greeting
        $this->addElement('text', 'custom_greeting', ts('Custom Greeting'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'custom_greeting' ));

        //hack the address sequence so that state province always comes after country
        $addressSequence = $config->addressSequence();
        $key = array_search( 'country', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'state_province', $addressSequence);
        unset($addressSequence[$key]);

        $addressSequence = array_merge( $addressSequence, array ( 'country', 'state_province' ) );
        $this->assign( 'addressSequence', $addressSequence );

        $location =& CRM_Contact_Form_Location::buildLocationBlock( $this, $this->_maxLocationBlocks );
        
        // add note block
        if ( $this->_showNotes &&
             ( $this->_action & CRM_Core_Action::ADD ) ) {
            require_once 'CRM/Contact/Form/Note.php';
            $note =& CRM_Contact_Form_Note::buildNoteBlock($this);
        }

        //add tags and groups block
        require_once 'CRM/Contact/Form/GroupTag.php';
        $groupTag =& CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_contactId, CRM_Contact_Form_GroupTag::ALL );

        if ( $this->_showNotes ) {
            CRM_Core_ShowHideBlocks::links( $this, 'notes', '' , '' );
        }

        // add the dedupe button
        $this->addElement('submit', 
                          $this->_dedupeButtonName,
                          ts( 'Check for Matching Contact(s)' ) );
        $this->addElement('submit', 
                          $this->_duplicateButtonName,
                          ts( 'Save Matching Contact' ) );
        $this->addElement('submit', 
                          $this->getButtonName( 'next'   , 'sharedHouseholdDuplicate' ),
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
        
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        //if greeting type is not customized, unset previously set custom greeting.
        if ( CRM_Utils_Array::value('greeting_type_id', $params) != 4 ) {
            $params['custom_greeting'] = "";
        }
 
        $params['contact_type'] = $this->_contactType;
        
        if ( $this->_contactId ) {
            $params['contact_id'] = $this->_contactId;
        }

        if ( $this->_showDemographics && ($this->_contactType == 'Individual') ) {
            if( ! isset( $params['is_deceased'] ) || $params['is_deceased'] != 1 ) { 
                $params['deceased_date']['M'] = $params['deceased_date']['d'] = 
                    $params['deceased_date']['Y'] = null;
                $params['is_deceased'] = 0;
            }
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
 
        if ( $this->_showCommBlock ) {
            // this is a chekbox, so mark false if we dont get a POST value
            $params['is_opt_out'] = CRM_Utils_Array::value( 'is_opt_out', $params, false );
        }
        
        // copy household address, if use_household_address option (for individual form) is checked
        if ( $this->_contactType == 'Individual' ) {
            if ( CRM_Utils_Array::value( 'use_household_address', $params ) && 
                 CRM_Utils_Array::value( 'shared_household',$params ) ) {
                if ( is_numeric( $params['shared_household'] ) ) {
                    CRM_Contact_Form_Individual::copyHouseholdAddress( $params );
                }
                CRM_Contact_Form_Individual::createSharedHousehold( $params );
            } else { 
                $params['mail_to_household_id'] = 'null';
            }
        } else {
            $params['mail_to_household_id'] = 'null';
        }

        // cleanup unwanted location types
        if ( CRM_Utils_Array::value( 'contact_id', $params ) && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            require_once 'CRM/Core/BAO/Location.php';
            CRM_Core_BAO_Location::cleanupContactLocations( $params );
        }
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact =& CRM_Contact_BAO_Contact::create($params, true,false );
               
        if ( $this->_contactType == 'Individual' && ( CRM_Utils_Array::value( 'use_household_address', $params )) &&
             CRM_Utils_Array::value( 'mail_to_household_id',$params ) ) {
            // add/edit/delete the relation of individual with household, if use-household-address option is checked/unchecked.
            CRM_Contact_Form_Individual::handleSharedRelation($contact->id , $params );
        }
        
        if ( $this->_contactType == 'Household' && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            //TO DO: commented because of schema changes
            CRM_Contact_Form_Household::synchronizeIndividualAddresses( $contact->id );
        }

        if ( $this->_showTagsAndGroups ) {
            //add contact to group
            require_once 'CRM/Contact/BAO/GroupContact.php';
            CRM_Contact_BAO_GroupContact::create( $params['group'], $params['contact_id'] );
            
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
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $contact->id );
            CRM_Utils_Recent::add( $displayName,
                                   CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $contact->id ),
                                   $contactImage,
                                   $contact->id );
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
     * Create communication preferences block for the contact.
     *
     * @param object $form - CRM_Core_Form (or it's subclass)
     * @return none
     *
     * @access public
     * @static
     */
    public static function buildCommunicationBlock(&$form)
    {
        // since the pcm - preferred comminication method is logically
        // grouped hence we'll use groups of HTML_QuickForm

        $privacy = array();
       
        // checkboxes for DO NOT phone, email, mail
        // we take labels from SelectValues
        $t = CRM_Core_SelectValues::privacy();
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_phone', null, $t['do_not_phone']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_email', null, $t['do_not_email']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_mail' , null, $t['do_not_mail']);
        $privacy[] = HTML_QuickForm::createElement('advcheckbox', 'do_not_trade', null, $t['do_not_trade']);

        $form->addGroup($privacy, 'privacy', ts('Privacy'), '&nbsp;');

        // preferred communication method 
        require_once 'CRM/Core/PseudoConstant.php';
        $comm = CRM_Core_PseudoConstant::pcm(); 

        $commPreff = array();
        foreach ( $comm as $k => $v ) {
            $commPreff[] = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
        }
        $form->addGroup($commPreff, 'preferred_communication_method', ts('Method'));

        $form->add('select', 'preferred_mail_format', ts('Email Format'), CRM_Core_SelectValues::pmf());

        $form->add('checkbox', 'is_opt_out', ts( 'NO BULK EMAILS (User Opt Out)' ) );
    }


    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     * @param array $errors list of errors to be posted back to the form
     *
     * @return void
     * @static
     * @access public
     */
    static function formRule(&$fields, &$errors)
    {

        $config =& CRM_Core_Config::singleton( );

        if ( $config->civiHRD && ! isset( $fields['tag']) ) {
            $errors["tag"] = ts('Please select at least one tag.');
        }

        $primaryID = false;

        // make sure that at least one field is marked is_primary
        if ( array_key_exists( 'location', $fields ) && is_array( $fields['location'] ) ) {
            $locationKeys = array_keys( $fields['location']);
            $isPrimary  = false;
            $dataExists = false;
            $locTypeId = false;
            foreach ( $locationKeys as $locationId ) {
                if ( array_key_exists( 'is_primary', $fields['location'][$locationId] ) ) {
                    if ( $fields['location'][$locationId]['is_primary'] ) {
                        if ( $isPrimary ) {
                            $errors["location[$locationId][is_primary]"] = ts('Only one location can be marked as primary.');
                        }
                        $isPrimary = true;
                    }

                    // only harvest email from the primary locations
                    if ( array_key_exists( 'email', $fields['location'][$locationId] ) &&
                         is_array( $fields['location'][$locationId]['email'] )         &&
                         empty( $primaryEmail ) ) {
                        foreach ( $fields['location'][$locationId]['email'] as $idx => $email ) {
                            if ( array_key_exists( 'email', $email ) ) {
                                $primaryID = $email['email'];
                                break;
                            }
                        }
                    }

                    if ( ! $primaryID ) {
                        // harvest OpenID from the primary locations if email is not found
                        if ( array_key_exists( 'openid', $fields['location'][$locationId] ) &&
                             is_array( $fields['location'][$locationId]['openid'] )         &&
                             empty( $primaryID ) ) {
                            foreach ( $fields['location'][$locationId]['openid'] as $idx => $openId ) {
                                if ( array_key_exists( 'openid', $openId ) ) {
                                    $primaryID = $openId['openid'];
                                    break;
                                }
                            }
                        }
                    }
                }

                if ( ( isset( $fields['use_household_address'] ) && $locationId == 1 ) ||
                     self::locationDataExists( $fields['location'][$locationId] ) ) {
                    $dataExists = true;
                    if ( ! CRM_Utils_Array::value( 'location_type_id', $fields['location'][$locationId] ) ) {
                        $errors["location[$locationId][location_type_id]"] = ts('The Location Type should be set if there is any location information.');
                    }
                }
                require_once 'CRM/Core/BAO/Location.php';
                // for checking duplicate location type.
                if ( CRM_Core_BAO_Location::dataExists( $fields ) ) {
                    if ( $locTypeId ) {
                        if ( $locTypeId == $fields['location'][$locationId]['location_type_id'] ) {
                            $errors["location[$locationId][location_type_id]"] = ts('Two locations cannot have same location type.');
                        }
                    }
                    $locTypeId = $fields['location'][$locationId]['location_type_id'];
                }
            }

            if ( $dataExists && ! $isPrimary ) {
                $errors["location[1][is_primary]"] = ts('One location should be marked as primary.');
            }
        }
        return $primaryID;
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
    static function locationDataExists( &$fields ) {
        static $skipFields = array( 'location_type_id', 'is_primary', 'phone_type', 'provider_id' );
        foreach ( $fields as $name => $value ) {
            $skipField = false;
            foreach ( $skipFields as $skip ) {
                if ( strpos( "[$skip]", $name ) !== false ) {
                    $skipField = true;
                    break;
                }
            }
            if ( $skipField ) {
                continue;
            }
            if ( is_array( $value ) ) {
                if ( self::locationDataExists( $value ) ) {
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


