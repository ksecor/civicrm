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
require_once 'CRM/ShowHideBlocks.php';

require_once 'CRM/Contact/Form/Contact.php';

require_once 'CRM/Contact/Form/Location.php';

/**
 * This class is used for building the Addcontact form. This class also has the actions that should be done when form is processed.
 * 
 * This class extends the variables and methods provided by the class CRM_Form which by default extends the HTML_QuickForm_SinglePage.
 * @package CRM.  
 */
class CRM_Contact_Form_Individual extends CRM_Form 
{
    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 2;

    /**
     * what blocks should we show and hide. This is constructed partially by
     * buildQuickForm and modified by setDefaults
     * @var CRM_ShowHideBlocks
     */
    protected $_showHideBlocks;

    /**
     * This is the constructor of the class.
     *
     * @access public
     * @param string $name Contains name of the form.
     * @param string $state Used to access the current state of the form.
     * @param constant $mode Used to access the type of the form. Default value is MODE_NONE.
     * @return None
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    
    /**
     * This function sets the default values to the specified form element.
     * 
     * The function uses the $default array to load default values for element names provided as keys. It further calls the setDefaults 
     * method of the HTML_QuickForm by passing to it the array. 
     * This function differentiates between different mode types of the form by implementing the switch strategy based on the form mode 
     * variable.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );
        if ( $this->_mode & self::MODE_ADD ) {
            if ( self::LOCATION_BLOCKS >= 1 ) {
                // set the is_primary location for the first location
                $defaults['location']    = array( );
                
                $locationTypeKeys = array_filter(array_keys( CRM_SelectValues::$locationType ), is_int );
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
            $params['id'] = $params['contact_id'] = $_SESSION['id'];
            $ids = array();
            CRM_Contact_BAO_Contact::getValues( $params, $defaults, $ids );

            unset($params['id']);
            CRM_Contact_BAO_Individual::getValues( $params, $defaults, $ids );

            CRM_Contact_BAO_Location::getValues( $params, $defaults, $ids, self::LOCATION_BLOCKS );

            if ( $this->_mode & self::MODE_UPDATE ) {
                $this->set( 'ids', $ids );
            }
        }
        
        $this->updateShowHideBlocks( $defaults );
        $this->_showHideBlocks->addToTemplate( );
        
        if ( $this->_mode & self::MODE_VIEW ) {
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
    function updateShowHideBlocks( &$defaults ) {
        $this->_showHideBlocks = new CRM_ShowHideBlocks( array('name'              => 1,
                                                               'commPrefs'         => 1,),
                                                         array('notes'        => 1,
                                                               'demographics' => 1,) );
        
        // first do the defaults showing
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHideBlocks,
                                                        self::LOCATION_BLOCKS );
        
        if ( ! ( $this->_mode & self::MODE_ADD ) ) {
            CRM_Contact_Form_Location::updateShowHideBlocks( $this->_showHideBlocks,
                                                             CRM_Array::value( 'location', $defaults ),
                                                             self::LOCATION_BLOCKS );
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
        if ( $this->_mode & self::MODE_VIEW ) {
            return;
        }

        $this->addFormRule( array( 'CRM_Contact_Form_Individual', 'formRule' ) );
    }

    function preProcess( ) {
    }

    /**
     * This function provides the HTML form elements for the add operation of a contact form.
     * 
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access public
     * @return None 
     * @uses CRM_SelectValues Used to obtain static array content for setting select values for select element.
     * @uses CRM_Contact_Form_Location::buildLocationBlock($this, 3) Used to obtain the HTML element for pulgging the Location block. 
     * @uses CRM_Contact_Form_Contact::buildCommunicationBlock($this) Used to obtain elements for plugging the Communication preferences.
     * 
     */
    public function buildQuickForm( ) 
    {
        // assign a few constants used by all display elements
        // we can obsolete this when smarty can access class constans directly
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1 );
        $this->assign( 'blockCount'  , CRM_Contact_Form_Location::BLOCKS + 1 );

        // view mode no longer builds a form :)
        if ($this->_mode == self::MODE_VIEW) {
            return;
        }

        // prefix
        $this->addElement('select', 'prefix', null, CRM_SelectValues::$prefixName);

        $attributes = CRM_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $this->add('text', 'first_name', 'First Name',
                   $attributes['first_name'],
                   true );
        
        // last_name
        $this->add('text', 'last_name', 'Last Name',
                   $attributes['last_name'],
                   true ); 
        
        // suffix
        $this->addElement('select', 'suffix', null, CRM_SelectValues::$suffixName);
        
        // greeting type
        $this->addElement('select', 'greeting_type', 'Greeting type :', CRM_SelectValues::$greeting);
        
        // job title
        $this->addElement('text', 'job_title', 'Job title :',
                          $attributes['job_title']);
        
        // add the communications block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        // radio button for gender
        $genderOptions = array( );
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Female', 'Female');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Male', 'Male');
        $genderOptions[] = HTML_QuickForm::createElement('radio', 'gender', 'Gender', 'Transgender','Transgender');
        $this->addGroup( $genderOptions, 'gender', 'Gender' );
        
        $this->addElement('checkbox', 'is_deceased', null, 'Contact is deceased');
        
        $this->addElement('date', 'birth_date', 'Date of birth', CRM_SelectValues::$date);
        $this->addRule('birth_date', 'Select a valid date.', 'qfDate' );

        /* Entering the compact location engine */ 
        $location =& CRM_Contact_Form_Location::buildLocationBlock($this, self::LOCATION_BLOCKS, $this->_showHideBlocks);

        /* End of locations */

        $this->add('textarea', 'address_note', 'Notes:', array('cols' => '82', 'maxlength' => 255));    
        
        CRM_ShowHideBlocks::links( $this, 'demographics', '[+] show demographics' , '[-] hide demographics'  );
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

        $tempDB = new CRM_Contact_DAO_Contact( );
        $tempDB->query('BEGIN');
        
        $params['contact_type'] = 'Individual';
        $contact = CRM_Contact_BAO_Contact::add( $params, $ids );
        // need to check for error here and abort / rollback if error
        
        $params['contact_id'] = $contact->id;
        
        $individual = CRM_Contact_BAO_Individual::add( $params, $ids );
        // need to check for error here and abort / rollback if error
        
        for ($locationId= 1; $locationId <= self::LOCATION_BLOCKS; $locationId++) { // start of for loop for location
            $location = CRM_Contact_BAO_Location::add( $params, $ids, $locationId );
        }

        $tempDB->query('COMMIT');

    }//end of function

    static function formRule( &$fields ) {
        $errors = array( );
        
        $primaryEmail = null;

        // make sure that at least one field is marked is_primary
        if ( array_key_exists( 'location', $fields ) && is_array( $fields['location'] ) ) {
            $locationKeys = array_keys( $fields['location']);
            $isPrimary = false;
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
            }
            
            if ( ! $isPrimary ) {
                $errors["location[1][is_primary]"] = "One location needs to be marked as primary.";
            }

        }
        
        // make sure that firstName and lastName or a primary email is set
        if (!((array_key_exists( 'first_name', $fields ) && 
               array_key_exists( 'last_name' , $fields )) || (!empty($primaryEmail)))) {
            $errors['first_name'] = "First Name and Last Name OR an email in the Primary Location should be set.";
        }
        
        // add code to make sure that the uniqueness criteria is satisfied

        if ( ! empty( $errors ) ) {
            return $errors;
        }
        return true;
    }

}


    
?>