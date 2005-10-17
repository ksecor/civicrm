<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Activity/Form.php';

/**
 * This class generates form components for Call
 * 
 */
class CRM_Activity_Form_Phonecall extends CRM_Activity_Form
{

    /**
     * variable to store BAO name
     *
     */
    public $_BAOName = 'CRM_Core_BAO_Phonecall';


    public function preProcess()
    {
        require_once 'CRM/Core/BAO/Phonecall.php';
        parent::preProcess();
        $params = array('id' => $this->_id);
        $defaults = array();
        $bao =& new CRM_Core_BAO_Phonecall();
        $bao->retrieve($params, $defaults);
        if ( CRM_Utils_Array::value( 'scheduled_date_time', $defaults ) ) {
            $this->assign('scheduled_date_time', $defaults['scheduled_date_time']);
        }
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    { 

        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }
        $this->applyFilter('__ALL__', 'trim');
        $contactPhone[''] = ts('Select Phone Number');
        if ( is_array(CRM_Core_BAO_Phone::getphoneNumber($this->_contactId))) {
            $contactPhone = CRM_Core_BAO_Phone::getphoneNumber($this->_contactId);
        }
        
        $this->add('text', 'subject', ts('Subject'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'subject' ));
        $this->addRule( 'subject', ts('The Field Subject should not be Empty'), 'required' );
        $this->add('date', 'scheduled_date_time', ts('Date and Time'),CRM_Core_SelectValues::date('datetime'));
        //$this->addRule( 'scheduled_date_time', ts('Please enter a valid date and time for this call.'), 'qfDate' );
        $this->addRule( 'scheduled_date_time', ts('Call Date and Time are required.'), 'required' );

        $this->add('select','phone_id',ts('Phone Number'), $contactPhone );
        $this->add('text', 'phone_number'  , ' ' . ts('OR New Phone') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'phone_number' ));
        $this->add('select', 'duration_hours', '', CRM_Core_SelectValues::getHours());
        $this->add('select', 'duration_minutes', '', CRM_Core_SelectValues::getMinutes());
        
        $status =& $this->add('select','status',ts('Status'),CRM_Core_SelectValues::ActivityStatus(true));
        $this->addRule( 'status', ts('Please select status.'), 'required' );
        
        $this->add('textarea', 'details'       , ts('Details')       ,CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'details' ));
        
        
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            return;
        }
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            CRM_Core_BAO_Phonecall::del( $this->_id);
           
        }

         // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );       
        $ids = array();
        
        $dateTime = $params['scheduled_date_time'];

        $dateTime = CRM_Utils_Date::format($dateTime);
        
        // store the date with proper format
        $params['scheduled_date_time']= $dateTime;
        
        // store the contact id and current drupal user id
        $params['source_contact_id'] = $this->_userId;
        $params['target_entity_id'] = $this->_contactId;
        $params['target_entity_table'] = 'civicrm_contact';
        
        //set parent id if exists for follow up activities
        if ($this->_pid) {
            $params['parent_id'] = $this->_pid;            
        }
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['call'] = $this->_id;
        }
      
        $call = CRM_Core_BAO_Phonecall::add($params, $ids);
        if($call->status=='Completed'){
            // we need to insert an activity history record here
            $params = array('entity_table'     => 'civicrm_contact',
                            'entity_id'        => $this->_contactId,
                            'activity_type'    => ts('Phone Call'),
                            'module'           => 'CiviCRM',
                            'callback'         => 'CRM_Activity_Form_Phonecall::showCallDetails',
                            'activity_id'      => $call->id,
                            'activity_summary' => $call->subject,
                            'activity_date'    => $call->scheduled_date_time
                            );
            
            
            if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) {
        
                return false;
           
            }
        }
      
        // print_r($params);
        if($call->status=='Completed'){
            CRM_Core_Session::setStatus( ts('Phone Call "%1" has been logged to Activity History.', array( 1 => $call->subject)) );
        } else if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_Session::setStatus( ts("Selected Phone Call is deleted sucessfully.")); 

        }else{
            CRM_Core_Session::setStatus( ts('Phone Call "%1" has been saved.', array( 1 => $call->subject)) );
        }
    }



    /**
     * compose the url to show details of this specific Call
     *
     * @param int $id
     * @param int $activityHistoryId
     *
     * @static
     * @access public
     */
    static function showCallDetails( $id, $activityHistoryId )
    {
        //require_once 'CRM/Core/DAO/Phonecall.php'; 
        //$dao =& new CRM_Core_DAO_Phonecall( ); 
        //echo $dao->id = $id; 

        $params   = array( );
        $defaults = array( );
        $params['id'          ] = $activityHistoryId;
        $params['entity_table'] = 'civicrm_contact';
        
        require_once 'CRM/Core/BAO/History.php'; 
        $history   = CRM_Core_BAO_History::retrieve($params, $defaults);
        $contactId = CRM_Utils_Array::value('entity_id', $defaults);

        //if ( $dao->find( true ) ) { 
        if ( $contactId ) {
            //return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=2&cid={$dao->source_contact_id}&action=view&id=$id&status=true&history=1"); 
            return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=2&cid=$contactId&action=view&id=$id&status=true&history=1"); 
        } else { 
            return CRM_Utils_System::url('civicrm' ); 
        } 
    }

}

?>
