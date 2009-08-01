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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
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
            $this->assign( 'entityID', $this->_id );
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

        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        // Show waitlist features or event_full_text if max participants set
        if ( CRM_Utils_Array::value('max_participants', $defaults) ) {
            $this->_showHide->addShow( 'id-waitlist' );
            if ( $defaults['has_waitlist'] ) {
                $this->_showHide->addShow( 'id-waitlist-text' );
                $this->_showHide->addHide( 'id-event_full' );
            } else {
                $this->_showHide->addHide( 'id-waitlist-text' );
                $this->_showHide->addShow( 'id-event_full' );
            }
        } else {
            $this->_showHide->addHide( 'id-event_full' );
            $this->_showHide->addHide( 'id-waitlist' );
            $this->_showHide->addHide( 'id-waitlist-text' );
        }

        $this->_showHide->addToTemplate( );
        $this->assign('elemType', 'table-row');

        $this->assign('description', CRM_Utils_Array::value('description', $defaults ) ); 

        // Provide suggested text for event full and waitlist messages if they're empty
        $defaults['event_full_text'] = CRM_Utils_Array::value('event_full_text', $defaults, ts('This event is currently full.') );
        $defaults['waitlist_text'] = CRM_Utils_Array::value('waitlist_text', $defaults, ts('This event is currently full. However you can register now and get added to a waiting list. You will be notified if spaces become available.') );

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

        if ($this->_isTemplate) {
            $this->add('text', 'template_title', ts('Template Title'), $attributes['template_title'], true);
        }

        if ($this->_action & CRM_Core_Action::ADD) {
            require_once 'CRM/Event/PseudoConstant.php';
            $eventTemplates =& CRM_Event_PseudoConstant::eventTemplates();
            $this->add('select', 'template_id', ts('From Template'), array('' => ts('- select -')) + $eventTemplates,
                       false, array('onchange' => "window.location += '&template_id=' + this.value"));
        }

        // add event title, make required if this is not a template
        $this->add('text', 'title', ts('Event Title'), $attributes['event_title'], !$this->_isTemplate);

        require_once 'CRM/Core/OptionGroup.php';
        $event = CRM_Core_OptionGroup::values('event_type');
        
        $this->add('select',
                   'event_type_id',
                   ts('Event Type'),
                   array('' => ts('- select -')) + $event,
                   true, 
                   array('onChange' => "buildCustomData( 'Event', this.value );") );
        
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
        $this->addElement('checkbox', 'is_map', ts('Include Map to Event Location?') );
         
        $this->add( 'date', 'start_date',
                    ts('Start Date'),
                    CRM_Core_SelectValues::date('activityDatetime') );
        $this->addRule('start_date', ts('Please select a valid start date.'), 'qfDate');

        $this->add('date', 'end_date',
                   ts('End Date / Time'),
                   CRM_Core_SelectValues::date('activityDatetime')
                   );
        $this->addRule('end_date', ts('Please select a valid end date.'), 'qfDate');
     
        $this->add('text','max_participants', ts('Max Number of Participants'),
                    array('onchange' => "if (this.value != '') {show('id-waitlist','table-row'); showHideByValue('has_waitlist','0','id-waitlist-text','table-row','radio',false); showHideByValue('has_waitlist','0','id-event_full','table-row','radio',true); return;} else {hide('id-event_full','table-row'); hide('id-waitlist','table-row'); hide('id-waitlist-text','table-row'); return;}"));
        $this->addRule('max_participants', ts('Max participants should be a positive number') , 'positiveInteger');

        require_once 'CRM/Event/PseudoConstant.php';
        $participantStatuses =& CRM_Event_PseudoConstant::participantStatus();
        if (in_array('On waitlist', $participantStatuses) and in_array('Pending from waitlist', $participantStatuses)) {
            $this->addElement('checkbox', 'has_waitlist', ts('Offer a Waitlist?'), null, array( 'onclick' => "showHideByValue('has_waitlist','0','id-event_full','table-row','radio',true); showHideByValue('has_waitlist','0','id-waitlist-text','table-row','radio',false);" ));
            $this->add('textarea', 'waitlist_text',   ts('Waitlist Message'), $attributes['waitlist_text']);
        }

        $this->add('textarea', 'event_full_text', ts('Message if Event Is Full'),          $attributes['event_full_text']);
        
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

        if (!$values['is_template']) {
            if ( CRM_Utils_System::isNull( $values['start_date'] ) ) {
                $errors['start_date'] = ts( 'Start Date and Time are required fields' );
            } else {
                $start = CRM_Utils_Date::format( $values['start_date'] );
                $end   = CRM_Utils_Date::format( $values['end_date'  ] );
                if ( ($end < $start) && ($end != 0) ) {
                    $errors['end_date'] = ts( 'End date should be after Start date' );
                }
            }
        }
        
        //CRM-4286
        if ( strstr( $values['title'], '/' ) ) {
            $errors['title'] = ts( "Please do not use '/' in Event Title." );
        }
        
        return $errors;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $this->controller->exportValues( $this->_name );
        
        //format params
        $params['start_date']      = CRM_Utils_Date::format($params['start_date']);
        $params['end_date'  ]      = CRM_Utils_Date::format($params['end_date']);
        $params['has_waitlist']    = CRM_Utils_Array::value('has_waitlist', $params, false);
        $params['is_map'    ]      = CRM_Utils_Array::value('is_map', $params, false);
        $params['is_active' ]      = CRM_Utils_Array::value('is_active', $params, false);
        $params['is_public' ]      = CRM_Utils_Array::value('is_public', $params, false);
        $params['default_role_id'] = CRM_Utils_Array::value('default_role_id', $params, false);
        $params['id']              = $this->_id;

        //new event, so lets set the created_id
        if ( $this->_action & CRM_Core_Action::ADD ) { 
            $session =& CRM_Core_Session::singleton( );
            $params['created_id']   = $session->get( 'userID' );
            $params['created_date'] = date('YmdHis');
        }   
        
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Event', false, false, 
                                                             CRM_Utils_Array::value( 'event_type_id', $params ) );
        $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                   $customFields,
                                                                   $this->_id,
                                                                   'Event' );

        require_once 'CRM/Event/BAO/Event.php';

        // copy all not explicitely set $params keys from the template (if it should be sourced)
        if ($params['template_id']) {
            $defaults = array();
            $templateParams = array('id' => $params['template_id']);
            CRM_Event_BAO_Event::retrieve($templateParams, $defaults);
            unset($defaults['id']);
            foreach ($defaults as $key => $value) {
                if (!isset($params[$key])) $params[$key] = $value;
            }
        }

        $event =  CRM_Event_BAO_Event::create( $params );

        // now that we have the event’s id, do some more template-based stuff
        if ($params['template_id']) {
            // copy event fees
            $ogParams = array('name' => "civicrm_event.amount.$event->id");
            $defaults = array();
            require_once 'CRM/Core/BAO/OptionGroup.php';
            if (is_null(CRM_Core_BAO_OptionGroup::retrieve($ogParams, $defaults))) {
                CRM_Core_BAO_OptionGroup::copyValue('event', $params['template_id'], $event->id);
            }

            // copy price sets if any
            require_once 'CRM/Core/BAO/PriceSet.php';
            $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event', $params['template_id'] );
            if ( $priceSetId ) {
                CRM_Core_BAO_PriceSet::addTo( 'civicrm_event', $event->id, $priceSetId );
            }

            // link profiles if none linked
            $ufParams = array('entity_table' => 'civicrm_event', 'entity_id' => $event->id);
            require_once 'CRM/Core/BAO/UFJoin.php';
            if (!CRM_Core_BAO_UFJoin::findUFGroupId($ufParams)) {
                CRM_Core_DAO::copyGeneric('CRM_Core_DAO_UFJoin',
                                          array('entity_id' => $params['template_id'], 'entity_table' => 'civicrm_event'),
                                          array('entity_id' => $event->id));
            }

            // if no Tell-a-Friend defined, check whether there’s one for template and copy if so
            $tafParams = array('entity_table' => 'civicrm_event', 'entity_id' => $event->id);
            require_once 'CRM/Friend/BAO/Friend.php';
            if (!CRM_Friend_BAO_Friend::getValues($tafParams)) {
                $tafParams['entity_id'] = $params['template_id'];
                if (CRM_Friend_BAO_Friend::getValues($tafParams)) {
                    $tafParams['entity_id'] = $event->id;
                    CRM_Friend_BAO_Friend::addTellAFriend($tafParams);
                }
            }
        }
        
        $this->set( 'id', $event->id );

        if ( $this->_action & CRM_Core_Action::ADD ) {
            CRM_Utils_System::redirect( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 
                                                               "action=update&reset=1&subPage=Location&id={$event->id}" ) );
        }
        parent::endPostProcess( );
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

