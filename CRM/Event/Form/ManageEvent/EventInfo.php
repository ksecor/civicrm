<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
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
                $eventType = CRM_Core_DAO::getFieldValue("CRM_Event_DAO_Event", $this->_id,"event_type_id");
            } else {
                $eventType = "Event";
            }
        }     
        
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $this->_id, 0,$this->_eventId);
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

        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, $viewMode, $inactiveNeeded );
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
        $this->applyFilter('__ALL__', 'trim');
                   
        $urlParams = "reset=1&context=event";
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE) ) {
            $urlParams .= "&action=update&id={$this->_id}&subPage=EventInfo";
             $eventId = $this->_id ;
        } else if ( $this->_action & ( CRM_Core_Action::COPY) ) {
            $urlParams .= "&action=copy&id={$this->_id}&subPage=EventInfo";
        } else {
            $urlParams .= "&action=add";
        }

        $url = CRM_Utils_System::url( 'civicrm/admin/event',
                                      $urlParams, true, null, false );
      
        $this->assign("refreshURL",$url);
        $this->add('text','title',ts('Title'));
        $this->addRule( 'title', ts('Event Title is already exist in Database.'), 'objectExists', array( 'CRM_Event_DAO_Event', $eventId, 'title' ) );
        require_once 'CRM/Core/OptionGroup.php';
        $event = CRM_Core_OptionGroup::values('event_type');
        
        $this->add('select','event_type_id',ts('Event Type'),array('' => ts('- select -')) + $event, true, array('onchange' => "reload(true)"));
        
        $this->add('textarea','summary',ts('Event Summary'), array("rows"=>4,"cols"=>60));
        
        $this->add('textarea','description',ts('Full description'), array("rows"=>4,"cols"=>60));
        
        $this->addElement('checkbox', 'is_public', ts('Public?') );
        $this->addElement('checkbox', 'is_map', ts('Is Map?') );
        $this->add('date', 'start_date', ts('Start Date and Time'), CRM_Core_SelectValues::date('datetime'),true);
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date and Time'), CRM_Core_SelectValues::date('datetime'));
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text','max_participants', ts('Max Number of Participants'));
        $this->addRule('max_participants', ts(' is a numeric field') , 'numeric');
        $this->add('text','event_full_text', ts('Event full text'));
        
        $this->addElement('checkbox', 'is_active', ts('Enabled?') );
   
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $this->_groupTree );
        } else {
            CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        }

        parent::buildQuickForm();
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
        $params['start_date'] = CRM_Utils_Date::format($params['start_date']);
        $params['end_date'  ] = CRM_Utils_Date::format($params['end_date']);
        $params['is_map'    ] = CRM_Utils_Array::value('is_map', $params, false);
        $params['is_active' ] = CRM_Utils_Array::value('is_active', $params, false);
        $params['is_public' ] = CRM_Utils_Array::value('is_public', $params, false);

        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['event_id'] = $this->_id;
        }
        require_once 'CRM/Event/BAO/Event.php';
        $event =  CRM_Event_BAO_Event::create($params ,$ids);
        
        CRM_Core_Session::setStatus( ts('The event "%1" has been saved.', array(1 => $event->title)) );
        
        //this is used in copy mode
        if ($this->_action & CRM_Core_Action::COPY ) {
            $this->set( 'eventId', $event->id );
        } else if ( $this->_action & CRM_Core_Action::ADD ) {
            $this->set( 'eId', $event->id );
        }

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
