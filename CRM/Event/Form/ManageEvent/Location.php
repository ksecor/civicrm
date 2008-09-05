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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';
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
     * @var array
     */
    protected $_locationEvents = array( );
    
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
        $params   = array( );
        if ( isset( $eventId ) ) {
            $params = array( 'entity_id' => $eventId ,'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $defaults);
            
            $isShowLocation = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                           $eventId,
                                                           'is_show_location',
                                                           'id' );
            
        }
        
        $defaults['is_show_location'] = $isShowLocation;
       
        if ( ! empty( $_POST ) ) {
            $this->setShowHide( $_POST, true );
        } else {
            if ( ! empty( $defaults ) ) {
                $this->setShowHide( $defaults, true );
            } else {
                $this->setShowHide( $defaults, false );
            }
        }
        
        //set defaults for country-state dojo widget
        if ( ! empty ( $defaults['location'] ) ) {
            $countries      =& CRM_Core_PseudoConstant::country( );
            $stateProvinces =& CRM_Core_PseudoConstant::stateProvince( false, false );
            
            foreach ( $defaults['location'] as $key => $value ) {
                if ( isset( $value['address'] ) ) {
                    
                    // hack, check if we have created a country element
                    if ( isset( $this->_elementIndex[ "location[$key][address][country_id]" ] ) ) {
                        $countryValue = $this->getElementValue( "location[$key][address][country_id]" );
                        
                        if ( !$countryValue && isset($value['address']['country_id']) ) {
                            $countryValue = $value['address']['country_id'];
                        }
                        
                        $this->assign( "country_{$key}_value"   ,  $countryValue );
                    }
                    
                    if ( isset( $this->_elementIndex[ "location[$key][address][state_province_id]" ] ) ) {
                        $stateValue = $this->getElementValue( "location[$key][address][state_province_id]" );
                        
                        if ( !$stateValue && isset($value['address']['state_province_id']) ) {
                            $stateValue = $value['address']['state_province_id'];
                        }

                        $this->assign( "state_province_{$key}_value", $stateValue );
                    }
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
        if ( $this->_action & CRM_Core_Action::ADD ) {
            require_once 'CRM/Event/BAO/Event.php';
            //get all events those having location block.
            $this->_locationEvents = CRM_Event_BAO_Event::getLocationEvents( );
            
            if ( !empty( $this->_locationEvents ) ) {
                $this->assign( 'locEvents', true );
                foreach ( $this->_locationEvents as $eventId => $value ) {
                    if ( $eventId != $this->_id ) {
                        $events[$eventId] = $value['title'];
                    }
                }
                $optionTypes = array( '1' => ts( 'Create new location' ),
                                      '2' => ts( 'Reuse existing location' ) );
                $extra = array( 'onclick' => "showLocFields();");
                $this->addRadio( 'option_type',
                                 ts("Choose Location"),
                                 $optionTypes,
                                 $extra,
                                 '<br/>', false
                                 );
                $this->add( 'select',
                            'loc_event_id',
                            ts( 'Use Location' ),
                            array( '' => ts( '- select -' ) ) + $events );
            }
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
        $params = array( );
        $params = $this->exportValues( );
        
        //if we are copying location from other event.
        if ( $this->_action & CRM_Core_Action::ADD &&
             CRM_Utils_Array::value( 'option_type' , $params ) == 2 &&
             CRM_Utils_Array::value( 'loc_event_id', $params ) ) { 
            
            //submitted location block id.
            $locBlockId = $this->_locationEvents[$params['loc_event_id']]['locBlockId'];
            
            if ( $this->_id ) {
                $updateLocBlockId = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $this->_id, 'loc_block_id' );
            }
            
            //copy event location block.
            require_once 'CRM/Core/BAO/Location.php';
            $copyLocBlockId = CRM_Core_BAO_Location::copyLocBlock( $locBlockId, $updateLocBlockId );
            $params['loc_block_id'] = $copyLocBlockId;
            
        } else {
            $params['entity_table'] = 'civicrm_event';
            $params['entity_id'] = $this->_id;
            
            //set the location type to default location type
            require_once 'CRM/Core/BAO/LocationType.php';
            
            $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
            $params['location'][1]['location_type_id'] = $defaultLocationType->id;
            $params['location'][1]['is_primary'] = 1;
            
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::create($params, true, 'event');
            $params['loc_block_id'] = $location['id'];
        }
        
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

