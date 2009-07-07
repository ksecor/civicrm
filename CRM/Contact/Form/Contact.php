<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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
    
    public $_editOptions = array( );

    protected $_blocks;

    protected $_values = array( );
    
    public $_action;

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
        $this->_duplicateButtonName = $this->getButtonName( 'next'   , 'duplicate' );
        
        if ( !$this->get( 'maxLocationBlocks' ) ) {
            // find the system config related location blocks
            require_once 'CRM/Core/BAO/Preferences.php';
            $this->_maxLocationBlocks = CRM_Core_BAO_Preferences::value( 'location_count' );
            $this->set( 'maxLocationBlocks',  $this->_maxLocationBlocks );
        }
        
        
        $this->_addBlockName  = CRM_Utils_Array::value( 'block', $_GET );
        $additionalblockCount = CRM_Utils_Array::value( 'count', $_GET );
        $this->assign( "addBlock", false );
        if ( $this->_addBlockName && $additionalblockCount ) {
            $this->assign( "addBlock", true );
            $this->assign( "blockName", $this->_addBlockName );
            $this->assign( "blockId",  $additionalblockCount );
            $this->set( $this->_addBlockName."_Block_Count", $additionalblockCount );
        }
        
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
            $this->assign( 'contactType', $this->_contactType );
            $session->pushUserContext(CRM_Utils_System::url());
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
                
                $this->assign( 'contactType', $this->_contactType );
                
                // check for permissions
                require_once 'CRM/Contact/BAO/Contact/Permission.php';
                if ( ! CRM_Contact_BAO_Contact_Permission::allow( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                    CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to edit this contact.') );
                }
                
                list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
                
                CRM_Utils_System::setTitle( $displayName, $contactImage . ' ' . $displayName ); 
                
                // need this for custom data in edit mode
                $this->assign('entityID', $this->_contactId );
                
                $session->pushUserContext(CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid='. $this->_contactId ));
                
                // get values from contact table
                $params = array( 'id'         => $this->_contactId,
                                 'contact_id' => $this->_contactId ) ;
                $contact = CRM_Contact_BAO_Contact::retrieve( $params, $this->_values );
                $this->set( 'values', $this->_values );
            } else {
                CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
            }
        }
                    
        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, 
                                                                       false, 'name', true, 'AND v.filter = 0' );
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
        
        // make blocks semi-configurable
        $this->_blocks = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, 
                                                                 false, 'name', true, 'AND v.filter = 1' );
        $this->assign( 'blocks', $this->_blocks );
        
        if ( array_key_exists( 'CustomData', $this->_editOptions ) ) {
            //only custom data has preprocess hence directly call it
            CRM_Custom_Form_Customdata::preProcess( $this, null, null, 1, $this->_contactType, $this->_contactId );
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
        $defaults = $this->_values;
        $params   = array( );
        
        if ( $this->_action & CRM_Core_Action::ADD ) {
        } else {
            //need to set default blocks.
            $loadDefaultBlocks = false;
            $defaultBlocksCount = array( );
            foreach ( array_merge( array( 'Address' => 1 ), $this->_blocks ) as $blockName => $active ) {
                $name = strtolower($blockName);
                if ( is_array( $defaults[$name] ) ) {
                    $loadDefaultBlocks = true;
                    $defaultBlocksCount[$blockName] = count( $defaults[$name] );
                }
            }
            $this->assign( 'loadDefaultBlocks',  $loadDefaultBlocks  );
            $this->assign( 'defaultBlocksCount', $defaultBlocksCount );
            
            if ( isset( $this->_elementIndex[ "shared_household" ] ) ) {
                $sharedHousehold = $this->getElementValue( "shared_household" );
                if ( $sharedHousehold ) {
                    $this->assign('defaultSharedHousehold', $sharedHousehold );
                } elseif ( CRM_Utils_Array::value('mail_to_household_id', $defaults) ) {
                    $defaults['use_household_address'] = true;
                    $this->assign('defaultSharedHousehold', $defaults['mail_to_household_id'] );
                }
                $defaults['shared_household_id'] = $defaults['mail_to_household_id'];
                $this->assign( 'sharedHouseholdAddress', $defaults['address'][1]['display'] );
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
        
        return $defaults;
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
           return true;
        }
        
        $this->addFormRule( array( 'CRM_Contact_Form_Contact', 'formRule'), $this );
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
    static function formRule( &$fields, $files, &$form )
    {
        return $errors = array( );
        
        eval("\$formErrors = CRM_Contact_Form_Edit_".$form->_contactType."::formRule( \$fields, \$files );");
        $errors = array_merge($errors, $formErrors);
        
        $primaryID = false;
        foreach ( $form->_blocks as $name => $active ) {
            if ( $active ) {
                $name = strlower($name);
                foreach ( $fields[$name] as $count => $values ) {
                    if ( in_array($name, array('email', 'openid')) && !empty($values[$name]) ) {
                        $primaryID = true;
                    }
                    
                    if ( CRM_Utils_Array::value('is_primary', $values) && empty($values[$name]) ) {
                        $errors[$name][$count][$name] = ts('Primary %1 should not be empty.', array( 1 => $name) );
                    } 
                }
            }
        }
        
        if ( $form->_contactType == 'Individual' ) {   
            if ( !$primaryID && CRM_Utils_Array::value('missingRequired', $errors ) ) {
                $errors['_qf_default'] = ts('First Name and Last Name OR an email OR an OpenID in the Primary Location should be set.'); 
            }  
        }
        
        return empty($errors) ? true : $errors;
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
            if ( !in_array( $name, array( 'Address' ) ) ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name ) . ".php");
                eval( 'CRM_Contact_Form_Edit_' . $name . '::buildQuickForm( $this );' );
            }
        }
        
        //build 1 instance of all blocks, without using ajax ...
        $buildAJAXBlocks = $this->_blocks;
        if ( array_key_exists( 'Address', $this->_editOptions ) ) {
            $buildAJAXBlocks['Address'] = $this->_editOptions['Address'];
            
        }
        foreach ( $buildAJAXBlocks as $blockName => $label ) {
            require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $blockName ) . ".php");
            $instances  = explode( ',', CRM_Utils_Array::value( "hidden_".$blockName ."_Instances", $_POST, 1 ) );
            foreach ( $instances as $instance ) {
                if ( $instance > 1 ) {
                    $this->assign( "addBlock", true );
                    $this->assign( 'blockName', $blockName );
                }
                $this->assign( "blockId", $instance  );
                $this->set( $blockName."_Block_Count", $instance );
                eval( 'CRM_Contact_Form_Edit_' . $blockName . '::buildQuickForm( $this );' ); 
            }
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
        
        //get the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        //get the related id for shared / current employer
        if ( CRM_Utils_Array::value( 'shared_household_id',$params ) ) {
            $params['shared_household'] = $params['shared_household_id'];
        }
        $params['current_employer'] = $params['current_employer_id'];
                
        //if email/postal greeting or addressee is not of the type customized, 
        //unset previously set custom value,CRM-4575
        $elements = array( 'email_greeting_id'  => 'email_greeting_custom', 
                           'postal_greeting_id' => 'postal_greeting_custom', 
                           'addressee_id'       => 'addressee_custom' );
        foreach( $elements as $field => $customField ) {
            if ( CRM_Utils_Array::value( $field, $params ) != 4) {
                $params[$customField] = "";
            }
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
    //    crm_core_error::debug( 'debug', $params);
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
        
//         if ( $this->_contactType == 'Household' && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
//             //TO DO: commented because of schema changes
//             CRM_Contact_Form_Household::synchronizeIndividualAddresses( $contact->id );
//         }

        if ( array_key_exists( 'TagsAndGroups', $this->_editOptions ) ) {
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


    function sampleParams( ) {
        //sample params array
        $params = array( 'contact_id'          => 102,
                         'prefix_id'           => 3,
                         'first_name'          => 'firstName',
                         'middle_name'         => '',
                         'last_name'           => 'lastName',
                         'suffix_id'           => 2,
                         'nick_name'           => '',
                         'job_title'           => '',
                         'current_employer'    => '',
                         'contact_source'      => '',
                         'external_identifier' => '', 
                         'hidden_Email_Count'  => 2,
                         'hidden_Phone_Count'  => 2,
                         'hidden_IM_Count'     => 2,
                         'hidden_OpenID_Count' => 2,
                         'hidden_Address_Count'=> 2,
                         'email' => array ( 1 => array (
                                                        'email'            => 'email_one@y.com',
                                                        'location_type_id' => 1,
                                                        'on_hold'          => false,
                                                        'is_bulkmail'      => 1,
                                                        'is_primary'       => 1,
                                                        ),
                                            2 => array (
                                                        'email'            => 'email_two@y.com',
                                                        'location_type_id' => 5,
                                                        'on_hold'          => 1,
                                                        'is_bulkmail'      => false,
                                                        'is_primary'       => false,
                                                        ) 
                                            ),
                         'phone' => array ( 1 => array ( 
                                                        'phone'            => 1111111,
                                                        'phone_type_id'    => 1,
                                                        'location_type_id' => 1,
                                                        'is_primary'       => true
                                                        ),
                                            2 => array ( 
                                                        'phone'            => 2222222,
                                                        'phone_type_id'    => 2,
                                                        'location_type_id' => 5,
                                                        'is_primary'       => false
                                                        ),
                                            ),
                         'im' => array ( 1 => array ( 'name'               => 'im_one',
                                                      'provider_id'        => 3,
                                                      'location_type_id'   => 1,
                                                      'is_primary'         => true
                                                      ),
                                         2 => array ( 'name'               => 'im_two',
                                                      'provider_id'        => 4,
                                                      'location_type_id'   => 5,
                                                      'is_primary'         => false
                                                      ),
                                         ),
                         'openid' => array ( 1 => array ( 'openid'           => 'http://civicrm.org/', 
                                                          'is_primary'       => 1, 
                                                          'location_type_id' => 1,
                                                          ),
                                             2 => array ( 'openid'           => 'http://civicrm.org/blog', 
                                                          'is_primary'       => false, 
                                                          'location_type_id' => 5,
                                                          ),
                                             ),
                         'address' => array ( 1 => array ( 'location_type_id'       => 1,
                                                           'is_primary'             => 1,
                                                           'street_address'         => 'Street Address 1',
                                                           'supplemental_address_1' => "Addt'l Address 1 1",
                                                           'city'                   => 'City 1',
                                                           'postal_code'            => '12345',
                                                           'postal_code_suffix'     => '123',
                                                           'state_province_id'      => '1004',
                                                           'country_id'             => '1228',
                                                           ),
                                              2 => array ( 'location_type_id'       => 5,
                                                           'is_billing'             => 1,
                                                           'street_address'         => 'Street Address 2',
                                                           'supplemental_address_1' => "Addt'l Address 1 2",
                                                           'city'                   => 'City 2',
                                                           'postal_code'            => 12345,
                                                           'postal_code_suffix'     => 123,
                                                           'state_province_id'      => 1000,
                                                           'country_id'             => 1228,
                                                           ),
                                              ),
                         'privacy' => array ( 'do_not_phone' => false,
                                              'do_not_email' => false,
                                              'do_not_mail' => false,
                                              'do_not_sms' => false,
                                              'do_not_trade' => false, 
                                              ),
                         'preferred_communication_method' => array ( 1 => true,
                                                                     2 => true,
                                                                     3 => true,
                                                                     4 => true,
                                                                     5 => true,
                                                                     ),
                         );

        return $params; 
    }
    
}


