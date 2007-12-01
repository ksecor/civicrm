<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent_EventInfo extends CRM_Event_Form_ManageEvent
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( )
    {
        parent::preProcess( );
        $eventType = CRM_Utils_Request::retrieve( 'etype', 'Positive', $this );        
        
        if ( ! $eventType ) {
            if ( $this->_id ) {
                $eventType = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                          $this->_id,
                                                          'event_type_id' );
            } else {
                $eventType = 'Event';
            }
        }     
        $showLocation = false;
        require_once 'CRM/Core/BAO/CustomGroup.php';    
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $this->_id, 0, $eventType);       

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
        $defaults = parent::setDefaultValues();

        $etype = CRM_Utils_Request::retrieve( 'etype', 'Positive', $this );
        if ( $etype ) {
            $defaults["event_type_id"] = $etype;
        }
        if( !isset ( $defaults['start_date'] ) ) {
            $defaultDate = array( );
            CRM_Utils_Date::getAllDefaultValues( $defaultDate );
            $defaultDate['i'] = (int ) ( $defaultDate['i'] / 15 ) * 15;
            $defaults['start_date'] = $defaultDate;
        }

        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
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
        $this->_first = true;
        $this->applyFilter('__ALL__', 'trim');
        $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
                   
        $urlParams = "reset=1&context=event";
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE) ) {
            $urlParams .= "&action=update&id={$this->_id}&subPage=EventInfo";
             $eventId = $this->_id ;
        } else {
            $urlParams .= "&action=add";
        }

        $url = CRM_Utils_System::url( CRM_Utils_System::currentPath( ),
                                      $urlParams, true, null, false );
     
        $this->assign("refreshURL",$url);
        $this->add('text','title',ts('Event Title'), $attributes['event_title'], true);

        require_once 'CRM/Core/OptionGroup.php';
        $event = CRM_Core_OptionGroup::values('event_type');

        
        $this->add('select','event_type_id',ts('Event Type'),array('' => ts('- select -')) + $event, true, 
                   array('onChange' => "if (this.value) reload(true); else return false"));
 
        $participantRole = CRM_Core_OptionGroup::values('participant_role');
        $this->add('select','default_role_id',ts('Participant Role'), $participantRole , true); 
        
        $participantListing = CRM_Core_OptionGroup::values('participant_listing');
        $this->add('select','participant_listing_id',ts('Participant Listing'), $participantListing , true); 
        
       $this->add('textarea','summary',ts('Event Summary'), array("rows"=>4,"cols"=>60));
        
        $this->add('textarea','description',ts('Complete Description'), array("rows"=>4,"cols"=>60));
        
        $this->addElement('checkbox', 'is_public', ts('Public Event?') );
        $this->addElement('checkbox', 'is_map', ts('Include Map Link?') );
         
        $this->add('date', 'start_date',
                   ts('Start Date'),
                   CRM_Core_SelectValues::date('datetime'),
                   true);  
        $this->addRule('start_date', ts('Please select a valid start date.'), 'qfDate');

        $this->add('date', 'end_date',
                   ts('End Date / Time'),
                   CRM_Core_SelectValues::date('datetime')
                   );
        $this->addRule('end_date', ts('Please select a valid end date.'), 'qfDate');
     
        $this->add('text','max_participants', ts('Max Number of Participants'));
        $this->addRule('max_participants', ts(' is a numeric field') , 'numeric');
        $this->add('textarea','event_full_text', ts('Message if Event is Full'), array("rows"=>2,"cols"=>60));
        
        $this->addElement('checkbox', 'is_active', ts('Is this Event Active?') );
        
        $this->addFormRule( array( 'CRM_Event_Form_ManageEvent_EventInfo', 'formRule' ) );

        if ($this->_action & CRM_Core_Action::VIEW ) { 
            CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $this->_groupTree );
        } else {
            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        }
        
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
        
        $ids['event_id']      = $this->_id;
        
        // format custom data
        // get mime type of the uploaded file
        if ( !empty($_FILES) ) {
            foreach ( $_FILES as $key => $value) {
                $files = array( );
                if ( $params[$key] ) {
                    $files['name'] = $params[$key];
                }
                if ( $value['type'] ) {
                    $files['type'] = $value['type']; 
                }
                $params[$key] = $files;
            }
        }
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
?>
