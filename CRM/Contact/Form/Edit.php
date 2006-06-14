<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';

require_once 'CRM/Core/BAO/LocationType.php';

require_once 'CRM/Utils/Recent.php';

require_once 'CRM/Contact/Form/Location.php';
require_once 'CRM/Contact/Form/Individual.php';
require_once 'CRM/Contact/Form/Household.php';
require_once 'CRM/Contact/Form/Organization.php';
require_once 'CRM/Contact/Form/Note.php';
require_once 'CRM/Contact/Form/GroupTag.php';

require_once 'CRM/Contact/BAO/GroupContact.php';
require_once 'CRM/Core/BAO/EntityTag.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomOption.php';

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
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 2;

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
    protected $_contactId;

    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;    

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

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {

        // reset action from the session
        $this->_action              = CRM_Utils_Request::retrieve('action', 'String', 
                                                                  $this, false, 'add' );

        $this->_dedupeButtonName    = $this->getButtonName( 'refresh', 'dedupe'    );
        $this->_duplicateButtonName = $this->getButtonName( 'next'   , 'duplicate' );

        if ( $this->_action == CRM_Core_Action::ADD ) {
            $this->_contactType = CRM_Utils_Request::retrieve( 'ct', 'String',
                                                               $this, true, null, 'REQUEST' );
            $this->_contactSubType = CRM_Utils_Request::retrieve( 'cst','String', 
                                                           CRM_Core_DAO::$_nullObject,false,null,'GET' );
            if ( $this->_contactSubType ) {
                CRM_Utils_System::setTitle( ts( 'New %1', array(1 => $this->_contactSubType ) ) );
            } else {
                CRM_Utils_System::setTitle( ts( 'New %1', array(1 => $this->_contactType ) ) );
            }
            $this->_contactId = null;
        } else {
            // this is update mode, first get the id from the session
            // else get it from the REQUEST
            $ids = $this->get('ids');
            $this->_contactId = CRM_Utils_Array::value( 'contact', $ids );
            if ( ! $this->_contactId ) {
                $this->_contactId   = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
            }

            if ( $this->_contactId ) {
                $contact =& new CRM_Contact_DAO_Contact( );
                $contact->id = $this->_contactId;
                if ( ! $contact->find( true ) ) {
                    CRM_Utils_System::statusBounce( ts('contact does not exist: %1', array(1 => $this->_contactId)) );
                }
                $this->_contactType = $contact->contact_type;

                // check for permissions
                if ( ! CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                    CRM_Utils_System::statusBounce( ts('You do not have the necessary permission to edit this contact.') );
                }

                list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
                CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName ); 
                return;
            }
            CRM_Utils_System::statusBounce( ts('Could not get a contact_id and/or contact_type') );
        }            
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );
        
        if ( $this->_action & CRM_Core_Action::ADD ) {
            if ( self::LOCATION_BLOCKS >= 1 ) {
                // set the is_primary location for the first location
                $defaults['location']    = array( );
                
                $locationTypeKeys = array_filter(array_keys( CRM_Core_PseudoConstant::locationType() ), 'is_int' );
                sort( $locationTypeKeys );
                
                // also set the location types for each location block
                for ( $i = 0; $i < self::LOCATION_BLOCKS; $i++ ) {
                    $defaults['location'][$i+1] = array( );
                    //$defaults['location'][$i+1]['location_type_id'] = $locationTypeKeys[$i];
                    if ( $i == 0 ) {
                        $defaultLocation =& new CRM_Core_BAO_LocationType();
                        $locationType = $defaultLocation->getDefault();
                        $defaults['location'][$i+1]['location_type_id'] = $locationType->id;
                    } else {
                        $defaults['location'][$i+1]['location_type_id'] = $locationTypeKeys[$i];
                    }
                    $defaults['location'][$i+1]['address'] = array( );
                    
                    $config          =& CRM_Core_Config::singleton( );
                    $countryIsoCodes =& CRM_Core_PseudoConstant::countryIsoCode();
                    $defaultCountryId = array_search($config->defaultContactCountry, $countryIsoCodes);
                    $defaults['location'][$i+1]['address']['country_id'] = $defaultCountryId;
                }
                $defaults['location'][1]['is_primary'] = true;
            }
        } else {
            // this is update mode
            // get values from contact table
            $params['id'] = $params['contact_id'] = $this->_contactId;
            $ids = array();
            $contact = CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );

            $this->set( 'ids', $ids );

            $this->assign( 'contactId' , $this->_contactId );
            // also set contact_type, since this is used in showHide routines 
            // to decide whether to display certain blocks (demographics)
            $this->_contactType = CRM_Utils_Array::value( 'contact_type', $defaults );

            // set the group and tag ids
            CRM_Contact_Form_GroupTag::setDefaults( $this->_contactId,                      
                                                    $defaults, 
                                                    CRM_Contact_Form_GroupTag::ALL );
        }
        
        // use most recently posted values if any to display show hide blocks
        //$params = $this->controller->exportValues( $this->_name );
        
        $params = $_POST;  //fix for CRM-907

        if ( ! empty( $params ) ) {
            $this->setShowHide( $params, true );
        } else {
            $this->setShowHide( $defaults, false );
        }

        // do we need inactive options ?
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }

        CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );

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
    function setShowHide( &$defaults, $force ) {
        $this->_showHide =& new CRM_Core_ShowHideBlocks( array('commPrefs'       => 1),
                                                         '') ;
        if ( $this->_contactType == 'Individual' ) {
            $this->_showHide->addShow( 'demographics[show]' );
            $this->_showHide->addHide( 'demographics' );
        }

        // first do the defaults showing
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide,
                                                        self::LOCATION_BLOCKS );
 
        if ( $this->_action & CRM_Core_Action::ADD ) {
            // notes are only included in the template for New Contact
            $this->_showHide->addShow( 'notes[show]' );
            $this->_showHide->addHide( 'notes' );
        }

        //add group and tags
        $contactGroup = $contactTag = array( );
        if ($this->_contactId) {
            $contactGroup =& CRM_Contact_BAO_GroupContact::getContactGroup( $this->_contactId, 'Added' );
            $contactTag   =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $this->_contactId);
        }
        
        if ( empty($contactGroup) || empty($contactTag) ) {
            $this->_showHide->addShow( 'group[show]' );
            $this->_showHide->addHide( 'group' );
        } else {
            $this->_showHide->addShow( 'group' );
            $this->_showHide->addHide( 'group[show]' );
        }



        // is there any demographics data?
        if ( CRM_Utils_Array::value( 'gender_id'     , $defaults ) ||
             CRM_Utils_Array::value( 'is_deceased', $defaults ) ||
             CRM_Utils_Array::value( 'birth_date' , $defaults ) ) {
            $this->_showHide->addShow( 'demographics' );
            $this->_showHide->addHide( 'demographics[show]' );
        }
        if ( $force ) {
            $locationDefaults = CRM_Utils_Array::value( 'location', $defaults );
            CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                       $locationDefaults,
                                                       self::LOCATION_BLOCKS );
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
        $this->addFormRule( array( 'CRM_Contact_Form_' . $this->_contactType, 'formRule' ), $this->_contactId );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        // assign a few constants used by all display elements
        // we can obsolete this when smarty can access class constans directly
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1 );
        $this->assign( 'blockCount'   , CRM_Contact_Form_Location::BLOCKS + 1 );
        $this->assign( 'contact_type' , $this->_contactType );

        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_" . $this->_contactType) . ".php");
        eval( 'CRM_Contact_Form_' . $this->_contactType . '::buildQuickForm( $this );' );
        
        // add the communications block
        self::buildCommunicationBlock($this);

        /* Entering the compact location engine */ 
        $location =& CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS );

        /* End of locations */
        
        // add note block
        if ($this->_action & CRM_Core_Action::ADD) {
            $note =& CRM_Contact_Form_Note::buildNoteBlock($this);
        }

        //add tags and groups block
        $groupTag =& CRM_Contact_Form_GroupTag::buildGroupTagBlock($this, $this->_contactId,
                                                                   CRM_Contact_Form_GroupTag::ALL );

        //Custom Group Inline Edit form
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree($this->_contactType, $this->_contactId);
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
          
        $config  =& CRM_Core_Config::singleton( );
        CRM_Core_ShowHideBlocks::links( $this, 'notes', '' , '' );

        // add the dedupe button
        $this->addElement('submit', 
                          $this->_dedupeButtonName,
                          ts( 'Check for Matching Contact(s)' ) );
        $this->addElement('submit', 
                          $this->_duplicateButtonName,
                          ts( 'Save Duplicate Contact' ) );

        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
       
        $this->addButtons( array(
                                 array ( 'type'      => $buttonType,
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'      => $buttonType,
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


        // action is taken depending upon the mode
        $ids = array();
        if ($this->_action & CRM_Core_Action::UPDATE) {
            // if update get all the valid database ids
            // from the session
            $ids = $this->get('ids');
        }

        $params['contact_type'] = $this->_contactType;
        if( ! $params['is_deceased'] == 1 ) { 
            $params['deceased_date'] = null;
        }
      
        $contact = CRM_Contact_BAO_Contact::create($params, $ids, self::LOCATION_BLOCKS);
       
        //add contact to gruoup
        CRM_Contact_BAO_GroupContact::create( $params['group'], $params['contact_id'] );

        //add contact to tags
        CRM_Core_BAO_EntityTag::create( $params['tag'], $params['contact_id'] );
        
        
        // here we replace the user context with the url to view this contact
        $config  =& CRM_Core_Config::singleton( );
        $session =& CRM_Core_Session::singleton( );
        CRM_Core_Session::setStatus(ts('Your %1 contact record has been saved.', array(1 => $contact->contact_type_display)));

        $buttonName = $this->controller->getButtonName( );
        if ( $buttonName == $this->getButtonName( 'next', 'new' ) ) {
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

        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, $this->_contactType, $contact->id);
    
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
        require_once 'CRM/Core/OptionGroup.php';
        //$form->add('select', 'preferred_communication_method', ts('Prefers'), CRM_Core_SelectValues::pcm());
        //$form->addCheckBox('preferred_communication_method',ts('Prefers'),CRM_Core_OptionGroup::values( 'preferred_communication_method', true ), null, null);
        $comm = CRM_Core_OptionGroup::values( 'preferred_communication_method', true); 
     
        $commPreff = array();
        foreach ( $comm as $k => $v ) {
            $commPreff[] = HTML_QuickForm::createElement('advcheckbox', $v , null, $k );
        }
        $form->addGroup($commPreff, 'preferred_communication_method', ts('Prefers'));

        $form->add('select', 'preferred_mail_format', ts('Mail Format'), CRM_Core_SelectValues::pmf());
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
        $primaryEmail = null;

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
                                $primaryEmail = $email['email'];
                                break;
                            }
                        }
                    }
                }
                if ( self::locationDataExists( $fields['location'][$locationId] ) ) {
                    $dataExists = true;
                    if ( ! CRM_Utils_Array::value( 'location_type_id', $fields['location'][$locationId] ) ) {
                        $errors["location[$locationId][location_type_id]"] = ts('The Location Type should be set if there is any location information');
                    }
                }
                //  for checking duplicate location type.
                if (CRM_Core_BAO_Location::dataExists( $fields, $locationId, $ids )) {
                    if ($locTypeId == $fields['location'][$locationId]['location_type_id']) {
                        $errors["location[$locationId][location_type_id]"] = ts('Two locations cannot have same location type');
                    }
                    $locTypeId = $fields['location'][$locationId]['location_type_id'];
                }
            }

            if ( $dataExists && ! $isPrimary ) {
                $errors["location[1][is_primary]"] = ts('One location should be marked as primary.');
            }
        }
        return $primaryEmail;
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

?>
