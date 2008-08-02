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
require_once "CRM/Core/BAO/CustomGroup.php";
require_once "CRM/Custom/Form/CustomData.php";
require_once "CRM/Core/BAO/CustomField.php";

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent_EventInfo extends CRM_Event_Form_ManageEvent
{
    /**
     * Event type
     */
    protected $_eventType = null;
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( )
    {
        //custom data related code
        $this->_cdType     = CRM_Utils_Array::value( 'type', $_GET );
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
        }
        parent::preProcess( );
                
        if ( $this->_id ) {
            $eventType = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                      $this->_id,
                                                      'event_type_id' );
        } else {
            $eventType = 'null';
        }
        
        $showLocation = false;
        // when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            CRM_Custom_Form_Customdata::preProcess( $this );
            CRM_Custom_Form_Customdata::buildQuickForm( $this );
            CRM_Custom_Form_Customdata::setDefaultValues( $this );
        }
        
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
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
        $defaults = parent::setDefaultValues();
        
        // in update mode, we need to set custom data subtype to tpl
        if ( CRM_Utils_Array::value( 'event_type_id' ,$defaults ) ) {
            $this->assign('customDataSubType',  $defaults["event_type_id"] );
        }

        if( !isset ( $defaults['start_date'] ) ) {
            $defaultDate = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaultDate );
            $defaultDate['i'] = (int ) ( $defaultDate['i'] / 15 ) * 15;
            $defaults['start_date'] = $defaultDate;
        }
        $this->assign('description', CRM_Utils_Array::value('description', $defaults ) ); 
        
        return $defaults;
    }
    
    /** 
     * Function to build the form 
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
        $this->assign('customDataType', 'Event');
        if ( $this->_eventType ) {
            $this->assign('customDataSubType',  $this->_eventType );
        }
        $this->assign('entityId',  $this->_id );
        
        $this->_first = true;
        $this->applyFilter('__ALL__', 'trim');
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
        
        $this->add('text','title',ts('Event Title'), $attributes['event_title'], true);

        require_once 'CRM/Core/OptionGroup.php';
        $event = CRM_Core_OptionGroup::values('event_type');
        
        $this->add('select',
                   'event_type_id',
                   ts('Event Type'),
                   array('' => ts('- select -')) + $event,
                   true, 
                   array('onChange' => "buildCustomData( this.value );") );
        
        $participantRole = CRM_Core_OptionGroup::values('participant_role');
        $this->add('select',
                   'default_role_id',
                   ts('Participant Role'),
                   $participantRole,
                   true); 
        
        $participantListing = CRM_Core_OptionGroup::values('participant_listing');
        $this->add('select',
                   'participant_listing_id',
                   ts('Participant Listing'),
                   array('' => ts('Disabled')) + $participantListing ,
                   false );
        
        $this->add('textarea','summary',ts('Event Summary'), $attributes['summary']);
        $this->addWysiwyg( 'description', ts('Complete Description'),$attributes['event_description']);
        $this->addElement('checkbox', 'is_public', ts('Public Event?') );
        $this->addElement('checkbox', 'is_map', ts('Include Map Link?') );
         
        $this->add( 'date', 'start_date',
                    ts('Start Date'),
                    CRM_Core_SelectValues::date('datetime') );
        $this->addRule('start_date', ts('Please select a valid start date.'), 'qfDate');

        $this->add('date', 'end_date',
                   ts('End Date / Time'),
                   CRM_Core_SelectValues::date('datetime')
                   );
        $this->addRule('end_date', ts('Please select a valid end date.'), 'qfDate');
     
        $this->add('text','max_participants', ts('Max Number of Participants'));
        $this->addRule('max_participants', ts('is a positive field') , 'positiveInteger');
        $this->add('textarea','event_full_text', ts('Message if Event is Full'), $attributes['event_full_text']);
        
        $this->addElement('checkbox', 'is_active', ts('Is this Event Active?') );
        
        $this->addFormRule( array( 'CRM_Event_Form_ManageEvent_EventInfo', 'formRule' ) );
        
        parent::buildQuickForm();
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
    static function formRule( &$values ) 
    {
        $errors = array( );
        if ( ! $values['start_date'] ) {
            $errors['start_date'] = ts( 'Start Date and Time are required fields' );
            return $errors;
        }

        $start = CRM_Utils_Date::format( $values['start_date'] );
        $end   = CRM_Utils_Date::format( $values['end_date'  ] );
        if ( ($end < $start) && ($end != 0) ) {
            $errors['end_date'] = ts( 'End date should be after Start date' );
            return $errors;
        }

        return true;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $ids = array();
        $params = $this->controller->exportValues( $this->_name );
        
        //format params
        $params['start_date']      = CRM_Utils_Date::format($params['start_date']);
        $params['end_date'  ]      = CRM_Utils_Date::format($params['end_date']);
        $params['is_map'    ]      = CRM_Utils_Array::value('is_map', $params, false);
        $params['is_active' ]      = CRM_Utils_Array::value('is_active', $params, false);
        $params['is_public' ]      = CRM_Utils_Array::value('is_public', $params, false);
        $params['default_role_id'] = CRM_Utils_Array::value('default_role_id', $params, false);
        $ids['event_id']           = $this->_id;

        $customData = array( );
        foreach ( $params as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                             $value, 'Event', null, $this->_id);
            }
        }
        
        if (! empty($customData) ) {
            $params['custom'] = $customData;
        }

        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Event' );

        if ( ! empty( $customFields ) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                     ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                 '', 'Event', null, $this->_id);
                }
            }
        }
        
        require_once 'CRM/Event/BAO/Event.php';
        $event =  CRM_Event_BAO_Event::create($params ,$ids);
        
        $this->set( 'id', $event->id );

    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Information and Settings');
    }
}

