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
        $this->_contactId   = CRM_Array::value( 'cid', $_GET );
        if ( $this->_contactId ) {
            $contact = new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_contactId;
            if ( ! $contact->find( true ) ) {
                CRM_Error::fatal( "contact does not exist: $this->_contactId" );
            }
            $this->_contactType = $contact->contact_type;
        } else {
            $this->_contactType = $_GET['c_type'];
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
        } else if ( $this->_mode & ( self::MODE_VIEW | self::MODE_UPDATE ) ) {
            // get the id from the session that has to be modified
            // get the values for $_SESSION['id']

            // get values from contact table
            $params['id'] = $params['contact_id'] = $this->_contactId;
            $ids = array();
            CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );

            unset($params['id']);
            eval( 'CRM_Contact_BAO_' . $this->_contactType . '::getValues( $params, $defaults, $ids );' );

            CRM_Contact_BAO_Location::getValues( $params, $defaults, $ids, self::LOCATION_BLOCKS );

            if ( $this->_mode & self::MODE_UPDATE ) {
                $this->set( 'ids', $ids );
            }

            // also set contact_type, since this is used in showHide routines 
            // to decide whether to display certain blocks (demographics)
            $this->_contactType = CRM_Array::value( 'contact_type', $defaults );
        }
        
        $this->setShowHide( $defaults );
        
        if ( $this->_mode & self::MODE_VIEW ) {
            CRM_Contact_BAO_Contact::resolveDefaults( $defaults );
            $this->assign( $defaults );
        }
        
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

         // view has a simpler block structure based on data
        if ( $this->_mode & self::MODE_VIEW ) {
            $this->_showHide->addHide( 'commPrefs[show]' );
            if ( array_key_exists( 'location', $defaults ) ) {
                $numLocations = count( $defaults['location'] );
                if ( $numLocations > 0 ) {
                    $this->_showHide->addShow( 'location[1]' );
                    $this->_showHide->addHide( 'location[1][show]' );
                }
                for ( $i = 1; $i < $numLocations; $i++ ) {
                    $locationIndex = $i + 1;
                    $this->_showHide->addShow( "location[$locationIndex][show]" );
                    $this->_showHide->addHide( "location[$locationIndex]" );
                }
            }
        } else {
            // first do the defaults showing
            CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide,
                                                            self::LOCATION_BLOCKS );
            
            if ( $this->_mode & self::MODE_UPDATE ) {
                CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                           CRM_Array::value( 'location', $defaults ),
                                                           self::LOCATION_BLOCKS );
            }
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
        if ( $this->_mode & self::MODE_VIEW ) {
            return;
        }

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

        // view mode no longer builds a form :)
        if ($this->_mode == self::MODE_VIEW) {
            return;
        }

        eval( 'CRM_Contact_Form_' . $this->_contactType . '::buildQuickForm( $this );' );
        
        // add the communications block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        /* Entering the compact location engine */ 
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, self::LOCATION_BLOCKS, $this->_showHideBlocks);

        /* End of locations */
        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        CRM_ShowHideBlocks::links( $this, 'notes'       , '[+] show contact notes', '[-] hide contact notes' );

        if ($this->_mode != self::MODE_VIEW) {
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

        if ($this->_mode == self::MODE_VIEW) {
            $this->freeze();
        }

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
        // no processing for a view form
        if ( $this->_mode == self::MODE_VIEW ) {
            return;
        }

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

        $returnUserContext = $config->httpBase . 'contact/view/' . $contact->id;
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

}

?>
