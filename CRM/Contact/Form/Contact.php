<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
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

require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';

require_once 'CRM/Contact/Form/Location.php';
require_once 'CRM/Contact/Form/Individual.php';
require_once 'CRM/Contact/Form/Household.php';
require_once 'CRM/Contact/Form/Organization.php';
require_once 'CRM/Contact/Form/Note.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_Contact extends CRM_Form
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
     * The contact id, used when editing the form
     *
     * @var int
     */
    protected $_contactId;

    /**
     * what blocks should we show and hide.
     *
     * @var CRM_ShowHideBlocks
     */
    protected $_showHide;
    
    /**
     * class constructor
     *
     * @param string $name        Name of the form.
     * @param string $state       The state object associated with this form
     * @param int     $mode       The mode of the form
     *
     * @return CRM_Contact_Form_Contact
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE) {
        parent::__construct($name, $state, $mode);
    }

    function preProcess( ) {
        if ( $this->_mode == self::MODE_ADD ) {
            $this->_contactType = CRM_Array::value( 'c_type', $_REQUEST );
            if ( ! $this->_contactType ) {
                CRM_Error::fatal( 'Invalid contact type' );
            }
            $this->_contactId = null;
        } else {
            // this is update mode, first get the id from the session
            // else get it from the REQUEST
            $ids = $this->get('ids');
            $this->_contactId = CRM_Array::value( 'contact', $ids );
            if ( ! $this->_contactId ) {
                $this->_contactId   = CRM_Array::value( 'cid', $_REQUEST );
            }

            if ( $this->_contactId ) {
                $contact = new CRM_Contact_DAO_Contact( );
                $contact->id = $this->_contactId;
                if ( ! $contact->find( true ) ) {
                    CRM_Error::fatal( "contact does not exist: $this->_contactId" );
                }
                $this->_contactType = $contact->contact_type;
                return;
            }
            CRM_Error::fatal( "Could not get a contact_id and/or contact_type" );
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

        if ( $this->_mode & self::MODE_ADD ) {
            if ( self::LOCATION_BLOCKS >= 1 ) {
                // set the is_primary location for the first location
                $defaults['location']    = array( );
                
                // $locationTypeKeys = array_filter(array_keys( CRM_SelectValues::$locationType ), is_int );
                $locationTypeKeys = array_filter(array_keys( CRM_SelectValues::getLocationType() ), is_int );
                sort( $locationTypeKeys );

                // also set the location types for each location block
                for ( $i = 0; $i < self::LOCATION_BLOCKS; $i++ ) {
                    $defaults['location'][$i+1] = array( );
                    $defaults['location'][$i+1]['location_type_id'] = $locationTypeKeys[$i];
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

            // show notes
            $this->assign( 'note'      , $defaults['note'] );
            $this->assign( 'notesCount', $defaults['notesCount'] );
            $this->assign( 'contactId' , $this->_contactId );
            // also set contact_type, since this is used in showHide routines 
            // to decide whether to display certain blocks (demographics)
            $this->_contactType = CRM_Array::value( 'contact_type', $defaults );
        }
        
        $this->setShowHide( $defaults );

        return $defaults;
    }

    /**
     * Fix what blocks to show/hide based on the default values set
     *
     * @param array @defaults the array of default values
     *
     * @return void
     */
    function setShowHide( &$defaults ) {
        $this->_showHide = new CRM_ShowHideBlocks( array('commPrefs'       => 1,
                                                         'notes[show]'     => 1),
                                                   array('notes'           => 1 ) ) ;

        if ( $this->_contactType == 'Individual' ) {
            $this->_showHide->addShow( 'demographics[show]' );
            $this->_showHide->addHide( 'demographics' );
        }

        // first do the defaults showing
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide,
                                                        self::LOCATION_BLOCKS );

        if ( $this->_mode & self::MODE_UPDATE ) {
            // is there any demographics data?
            if ( CRM_Array::value( 'gender'     , $defaults ) ||
                 CRM_Array::value( 'is_deceased', $defaults ) ||
                 CRM_Array::value( 'birth_date' , $defaults ) ) {
                $this->_showHide->addShow( 'demographics' );
                $this->_showHide->addHide( 'demographics[show]' );
            }

            // is there any notes data?
            if ( CRM_Array::value( 'notesCount', $defaults ) ) {
                $this->_showHide->addShow( 'notes' );
                $this->_showHide->addHide( 'notes[show]' );
            }

            CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                       CRM_Array::value( 'location', $defaults ),
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
        $this->addFormRule( array( 'CRM_Contact_Form_' . $this->_contactType, 'formRule' ) );
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

        eval( 'CRM_Contact_Form_' . $this->_contactType . '::buildQuickForm( $this );' );
        
        // add the communications block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        /* Entering the compact location engine */ 
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, self::LOCATION_BLOCKS, $this->_showHideBlocks);

        /* End of locations */
        
        // add note block
        if ($this->_mode == self::MODE_ADD) {
            $note =& CRM_Contact_Form_Note::buildNoteBlock($this);
        }    
        CRM_ShowHideBlocks::links( $this, 'notes'       , '[+] show contact notes', '[-] hide contact notes' );
            
        /*        
        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        CRM_ShowHideBlocks::links( $this, 'notes'       , '[+] show contact notes', '[-] hide contact notes' );
        */
        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next',
                                                'name'      => 'Save',
                                                'isDefault' => true   ),
                                        array ( 'type'      => 'reset',
                                                'name'      => 'Reset'),
                                        array ( 'type'       => 'cancel',
                                                'name'      => 'Cancel' ),
                                        )
                                  );

    }

       
    /**
     * This function does all the processing of the form for New Contact Individual.
     * Depending upon the mode this function is used to insert or update the Individual
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->exportValues();

        // action is taken depending upon the mode
        $ids = array( );
        if ($this->_mode & self::MODE_UPDATE ) {
            // if update get all the valid database ids
            // from the session
            $ids = $this->get('ids');
        }    

        $params['contact_type'] = $this->_contactType;
        $contact = CRM_Contact_BAO_Contact::create( $params, $ids, self::LOCATION_BLOCKS );

        // here we replace the user context with the url to view this contact
        $config  = CRM_Config::singleton( );
        $session = CRM_Session::singleton( );

        $session->setStatus( 'Your ' . $contact->contact_type . ' contact record has been saved' );

        $returnUserContext = $config->httpBase . 'contact/view?cid=' . $contact->id;
        $session->replaceUserContext( $returnUserContext );
    }//end of function

    public static function buildCommunicationBlock(&$form)
    {
        $privacy = array( );

        // checkboxes for DO NOT phone, email, mail
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_phone', null, 'Do not call');
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_email', null, 'Do not contact by email');
        $privacy[] = HTML_QuickForm::createElement('checkbox', 'do_not_mail' , null, 'Do not contact by postal mail');

        $form->addGroup( $privacy, 'privacy', 'Privacy' );

        // preferred communication method 
        $form->add('select', 'preferred_communication_method', 'Prefers:', CRM_SelectValues::$pcm);
    }

    static function formRule( &$fields, &$errors ) {
        $primaryEmail = null;

        // make sure that at least one field is marked is_primary
        if ( array_key_exists( 'location', $fields ) && is_array( $fields['location'] ) ) {
            $locationKeys = array_keys( $fields['location']);
            $isPrimary  = false;
            $dataExists = false;
            foreach ( $locationKeys as $locationId ) {
                if ( array_key_exists( 'is_primary', $fields['location'][$locationId] ) ) {
                    if ( $fields['location'][$locationId]['is_primary'] ) {
                        if ( $isPrimary ) {
                            $errors["location[$locationId][is_primary]"] = "Only one location can be marked as primary.";
                        }
                        $isPrimary = true;
                    }

                    // only harvest email from the primary locations
                    if ( array_key_exists( 'email', $fields['location'][$locationId] ) &&
                         is_array( $fields['location'][$locationId]['email'] )         &&
                         empty( $primaryEmail ) ) {
                        foreach ( $fields['location'][$locationId]['email'] as $idx => &$email ) {
                            if ( array_key_exists( 'email', $email ) ) {
                                $primaryEmail = $email['email'];
                                break;
                            }
                        }
                    }
                }
                if ( self::locationDataExists( $fields['location'][$locationId] ) ) {
                    $dataExists = true;
                    if ( ! CRM_Array::value( 'location_type_id', $fields['location'][$locationId] ) ) {
                        $errors["location[$locationId][location_type_id]"] = 'The Location Type should be set if there is any location information';
                    }
                }
            }

            if ( $dataExists && ! $isPrimary ) {
                $errors["location[1][is_primary]"] = "One location should be marked as primary.";
            }
        }
        return $primaryEmail;
    }

    static function locationDataExists( &$fields ) {
        static $skipFields = array( 'location_type_id', 'is_primary', 'phone_type', 'provider_id' );
        foreach ( $fields as $name => &$value ) {
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
