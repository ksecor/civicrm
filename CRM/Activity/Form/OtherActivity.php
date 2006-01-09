<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Activity/Form.php';
require_once 'CRM/Core/BAO/OtherActivity.php';
require_once 'CRM/Core/BAO/ActivityType.php';
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
        $activityType = CRM_Core_PseudoConstant::activityType(false);

        $this->applyFilter('__ALL__', 'trim');
        $this->add('select', 'activity_type_id', ts('Activity Type'), array(ts('- select activity type -')) + $activityType, 
                   array('onChange' => 'activity_get_description( )'), true );

        $this->add('text', 'description', ts('Description'),
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_ActivityType', 'description' ), false);

        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'subject' ), true );

        $this->add('date', 'scheduled_date_time', ts('Date and Time'), CRM_Core_SelectValues::date('datetime'), true);
        $this->addRule('scheduled_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes());

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'location' ) );
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Activity', 'details' ) );
        
        $this->add('select','status',ts('Status'), CRM_Core_SelectValues::activityStatus(), true );

        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree('Activity',$this->_id,0);
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
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
            return;
        }

        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        $ids = array();
        
        // store the date with proper format
        $params['scheduled_date_time']= CRM_Utils_Date::format( $params['scheduled_date_time'] );

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
      
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $params );

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree,'Activity',$otherActivity->id); 


        $activityType = CRM_Core_PseudoConstant::activityType(true);
        
        if($otherActivity->status=='Completed'){
            // we need to insert an activity history record here
            $params = array('entity_table'     => 'civicrm_contact',
                            'entity_id'        => $this->_contactId,
                            'activity_type'    => $activityType[$params['activity_type_id']],
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
     * @param int $activityHistoryId
     *
     * @static
     * @access public
     *
     */
    static function showOtherActivityDetails( $id, $activityHistoryId )
    {
        //require_once 'CRM/Core/DAO/Activity.php';  
        //$dao =& new CRM_Core_DAO_Activity( );  
        //$dao->id = $id;  
        
        $params   = array( );
        $defaults = array( );
        $params['id'          ] = $activityHistoryId;
        $params['entity_table'] = 'civicrm_contact';
        
        require_once 'CRM/Core/BAO/History.php'; 
        $history   = CRM_Core_BAO_History::retrieve($params, $defaults);
        $contactId = CRM_Utils_Array::value('entity_id', $defaults);
        
        //if ( $dao->find( true ) ) {                                   
        if ( $contactId ) {
            // return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=2&cid={$dao->source_contact_id}&action=view&id=$id&status=true&history=1");  
             return CRM_Utils_System::url('civicrm/contact/view/activity', "activity_id=5&cid=$contactId&action=view&id=$id&status=true&history=1");  
        } else {  
            return CRM_Utils_System::url('civicrm' );  
        }             
    }


}

?>
