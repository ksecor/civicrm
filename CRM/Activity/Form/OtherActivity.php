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

require_once 'CRM/Activity/Form.php';

/**
 * This class generates form components for OtherActivity
 * 
 */
class CRM_Activity_Form_OtherActivity extends CRM_Activity_Form
{

    /**
     * variable to store BAO name
     *
     */
    public $_BAOName = 'CRM_Core_BAO_OtherActivity';
    public $description = array(); 

    public function preProcess()
    {
        parent::preProcess();
        $params = array('id' => $this->_id);
        $defaults = array();
        $bao =& new CRM_Core_BAO_OtherActivity();
        $bao->retrieve($params, $defaults);
        $this->description = CRM_Core_BAO_ActivityType::getActivityDescription();
        $this->assign('ActivityTypeDescription',$this->description);
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
        $activityType = CRM_Core_PseudoConstant::activityType(true);
        $activityType[''] = ts('- select Activity Type -') ;
        asort($activityType);
        $this->applyFilter('__ALL__', 'trim');
        $this->addElement('select', 'activity_type', ts('Activity Type'),$activityType ,array('onchange'=>'activity_get_description()'));
        $this->addRule('activity_type',ts('Please select the Activity Type.'), 'required');
        $description = $this->addElement('text', 'description', ts('Description'),CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_ActivityType', 'description' ),false);
        $this->addElement('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'subject' ) );
        $this->addRule( 'subject', ts('Please enter a valid subject.'), 'required' );

        $this->addElement('date', 'scheduled_date_time', ts('Date and Time'), CRM_Core_SelectValues::date('datetime'));
        $this->addRule('scheduled_date_time', ts('Select a valid date.'), 'qfDate');
        $this->addRule( 'scheduled_date_time', ts('Please select Scheduled Date.'), 'required' );
        
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes());

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'location' ) );
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'details' ) );
        
        $status =& $this->add('select','status',ts('Status'), CRM_Core_SelectValues::activityStatus());
        $this->addRule( 'status', ts('Please select status.'), 'required' );
        

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
            CRM_Core_BAO_OtherActivity::del( $this->_id);
           
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
            $ids['otherActivity'] = $this->_id;
        }
        
        $otherActivity = CRM_Core_BAO_OtherActivity::add($params, $ids);
      
        $activityType = CRM_Core_PseudoConstant::activityType(true);
        // print_r(CRM_Core_BAO_ActivityType::getActivityDescription());
        
        if($otherActivity->status=='Completed'){
            // we need to insert an activity history record here
            $params = array('entity_table'     => 'civicrm_contact',
                            'entity_id'        => $this->_contactId,
                            'activity_type'    => $activityType[$params['activity_type']],
                            'module'           => 'CiviCRM',
                            'callback'         => 'CRM_Activity_Form_OtherActivity::showOtherActivityDetails',
                            'activity_id'      => $otherActivity->id,
                            'activity_summary' => $otherActivity->subject,
                            'activity_date'    => $otherActivity->scheduled_date_time
                            );
            
            
            if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) {
                return false;
            }
        }
             
        if($otherActivity->status=='Completed'){
            CRM_Core_Session::setStatus( ts('Activity "%1" has been logged to Activity History.', array( 1 => $otherActivity->subject)) );
        } else if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_Session::setStatus( ts("Selected Activity is deleted sucessfully."));
        }   else{
            CRM_Core_Session::setStatus( ts('Activity "%1" has been saved.', array( 1 => $otherActivity->subject)) );
        }
    }//end of function

    /**
     * compose the url to show details of this specific OtherActivity
     *
     * @param int $id
     *
     * @static
     * @access public
     *
     */
    static function showOtherActivityDetails( $id )
    {
        return CRM_Utils_System::url('civicrm/contact/view/otheract', "action=view&id=$id&status=true&history=1");
    }


}

?>
