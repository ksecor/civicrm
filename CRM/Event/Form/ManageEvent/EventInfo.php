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
    function preProcess( ) {
       
        parent::preProcess( );
        $this->_eventId = CRM_Utils_Request::retrieve( 'subType', 'Positive',
                                                       $this );
        
        if ( ! $this->_eventId ) {
            if ( $this->_id ) {
                $this->_eventId = CRM_Core_DAO::getFieldValue("CRM_Event_DAO_Event",$this->_id,"event_type_id");
            } else {
                $this->_eventId = "Event";
            }
        }     

        require_once 'CRM/Core/BAO/CustomGroup.php';
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Event", $this->_id, 0,$this->_eventId);
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
        
        $this->add('text','title',ts('Title'),array( 'size' => 50),true);
        
        $urlParams = "reset=1&context=event";
        if ( $this->_id ) {
            $urlParams .= "&action=update&id={$this->_id}&subPage=EventInfo";
        } else {
            $urlParams .= "&action=add";
        }
        
        $url = CRM_Utils_System::url( 'civicrm/admin/event',
                                      $urlParams, true, null, false );
      
        $this->assign("refreshURL",$url);
        $this->add('text','title',ts('Title'));
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
        
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
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
        $params = $id = array();
        $params = $this->exportValues( );
        
        $id['event_id'] = $this->_id;
        
        // store the submitted values in an array
        $params['start_date']    = CRM_Utils_Date::format($params['start_date']);
        $params['end_date']      = CRM_Utils_Date::format($params['end_date']);
        
        require_once 'CRM/Event/BAO/Event.php';
        $event =  CRM_Event_BAO_Event::create($params ,$id);
        CRM_Core_Session::setStatus( ts('The event "%1" has been saved.', array(1 => $event->title)) );

        $this->set( 'eid', $event->id );
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
