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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';
require_once 'CRM/Event/BAO/Event.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components for processing Event Location 
 * civicrm_event_page. 
 */
class CRM_Event_Form_ManageEvent_Location extends CRM_Event_Form_ManageEvent
{

    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;
    
    /**
     * the variable, for storing the location array
     *
     * @var array
     */
    protected $_locationIds = array();

    /**
     * the variable, for storing location block id with event
     *
     * @var int
     */
    protected $_oldLocBlockId = null;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        parent::preProcess( );
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
        $eventId = $this->_id;

        $defaults = array( );
        if ( isset( $eventId ) ) {
            $params = array( 'entity_id' => $eventId ,'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $defaults);
            
            $params = array( 'id' => $eventId );
            CRM_Event_BAO_Event::retrieve( $params, $defaults );
            $this->_oldLocBlockId = $defaults['loc_event_id'] = $defaults['loc_block_id'];

            $countLocUsed = CRM_Event_BAO_Event::countEventsUsingLocBlockId( $defaults['loc_block_id'] );
            if ( $countLocUsed > 1 ) {
                $this->assign('locUsed', true);
            }
        }
       
        if ( ! empty( $_POST ) ) {
            $this->setShowHide( $_POST, true );
        } else {
            if ( ! empty( $defaults ) ) {
                $this->setShowHide( $defaults, true );
            } else {
                $this->setShowHide( $defaults, false );
            }
        }
        
        if ( ! empty ( $defaults['location'] ) ) {
            $config = CRM_Core_Config::singleton( );
            foreach ( $defaults['location'] as $key => $value ) {
                CRM_Contact_Form_Address::fixStateSelect( $this,
                                                          "location[$key][address][country_id]",
                                                          "location[$key][address][state_province_id]",
                                                          CRM_Utils_Array::value( 'country_id',
                                                                                  CRM_Utils_Array::value( 'address',
                                                                                                          $value ),
                                                                                  $config->defaultContactCountry ) );
            }
        }
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $defaults['location_option'] = $this->_oldLocBlockId ? 2 : 1;
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
        $this->_showHide =& new CRM_Core_ShowHideBlocks( array(),'') ;
        
        $prefix =  array( 'phone','email' );
        CRM_Contact_Form_Location::setShowHideDefaults( $this->_showHide, self::LOCATION_BLOCKS, $prefix, false);
        if ( $force ) {
            $locationDefaults = CRM_Utils_Array::value( 'location', $defaults );
            $config =& CRM_Core_Config::singleton( );
            CRM_Contact_Form_Location::updateShowHide( $this->_showHide,
                                                       $locationDefaults,
                                                       self::LOCATION_BLOCKS, $prefix, false );
        }
        $this->_showHide->addToTemplate( );
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Event_Form_ManageEvent_Location', 'formRule' ) );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) 
    {
        $errors = array( );
        // check for state/country mapping
        CRM_Contact_Form_Address::formRule($fields, $errors);

        return empty($errors) ? true : $errors;
    }    

    /** 
     *  function to build location block 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);
        
        //hack the address sequence so that state province always comes after country
        $config =& CRM_Core_Config::singleton( );
        $addressSequence = $config->addressSequence();
        $key = array_search( 'country', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'state_province', $addressSequence);
        unset($addressSequence[$key]);

        $addressSequence = array_merge( $addressSequence, array ( 'country', 'state_province' ) );
        $this->assign( 'addressSequence', $addressSequence );

        require_once 'CRM/Contact/Form/Location.php';

        //blocks to be displayed
        $locationCompoments = array('Phone', 'Email');
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS + 1, true, $locationCompoments);
        $this->addElement('advcheckbox', 'is_show_location', ts('Show Location?') );
        $this->assign( 'index' , 1 );
        $this->assign( 'blockCount'   , CRM_Contact_Form_Location::BLOCKS + 1);
        
        //fix for CRM-1971
        $this->assign( 'action', $this->_action );

        // get the list of location blocks being used by other events
        $locationEvents = CRM_Event_BAO_Event::getLocationEvents( );
        
        $events = array();
        if ( !empty( $locationEvents ) ) {
            $this->assign( 'locEvents', true );
            $optionTypes  = array ( '1' => ts( 'Create new location' ),
                                    '2' => ts( 'Use existing location' ) );
            
            $this->addRadio( 'location_option', ts("Choose Location"), $optionTypes,
                             array( 'onclick' => "showLocFields();"), '<br/>', false );
            
            $this->add( 'select', 'loc_event_id', ts( 'Use Location' ), $locationEvents );
        }
        parent::buildQuickForm();
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $params = $this->exportValues( );
        
        $delteOldBlock = false;

        // if 'use existing location' option is selected -
        if ( ( $params['location_option'] == 2 ) &&
             ( $params['loc_event_id'] != $this->_oldLocBlockId ) ) {
            // if new selected loc is different from old loc, update the loc_block_id 
            // so that loc update would affect the selected loc and not the old one.
            $delteOldBlock = true;
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Event', $this->_id, 
                                         'loc_block_id', $params['loc_event_id'] );
        }

        // if 'create new loc' option is selected, set the loc_block_id for this event to null 
        // so that an update would result in creating a new loc.
        if ( $this->_oldLocBlockId && ($params['location_option'] == 1) ) {
            $delteOldBlock = true;
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Event', $this->_id, 
                                         'loc_block_id', 'null' );
        }

        // if 'create new loc' optioin is selected OR selected new loc is different 
        // from old one, go ahead and delete the old loc provided thats not being 
        // used by any other event
        if ( $this->_oldLocBlockId && $delteOldBlock ) {
            CRM_Event_BAO_Event::deleteEventLocBlock($this->_oldLocBlockId, $this->_id);
        }
        
        // get ready with location block params
        $params['entity_table'] = 'civicrm_event';
        $params['entity_id']    = $this->_id;
            
        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        $params['location'][1]['location_type_id'] = $defaultLocationType->id;
        $params['location'][1]['is_primary']       = 1;
        
        // create/update event location
        require_once 'CRM/Core/BAO/Location.php';
        $location = CRM_Core_BAO_Location::create($params, true, 'event');
        $params['loc_block_id'] = $location['id'];
        
        // finally update event params
        $params['id'] = $this->_id;
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::add( $params );

    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Location');
    }
}

