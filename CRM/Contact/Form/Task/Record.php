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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */
class CRM_Contact_Form_Task_Record extends CRM_Contact_Form_Task {

    /** 
     * The id of the logged in user, used when add / edit  
     * 
     * @var int 
     */ 
    protected $_userID;

    /** 
     * The display name of the logged in user
     * 
     * @var string
     */ 
    protected $_displayName;

    /** 
     * The list of valid activity types
     * 
     * @var array
     */ 
    protected $_activityType;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */

    function preProcess( ) {

        if ( ! isset($_POST['activity_type_id']) ) {
            $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', CRM_Core_DAO::$_nullObject );
        }  else {
            $this->_activityType = $_POST['activity_type_id'];
        }
        
        if ( isset ($subType ) ) {
            $this->_activityType = $subType;
        } 
        require_once'CRM/Core/BAO/CustomGroup.php';
        $this->_id = 0;
        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree("Activity",$this->_id, 0,$this->_activityType);
        parent::preProcess();
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
        $defaults = array( );
        $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', CRM_Core_DAO::$_nullObject );
        if ( $subType ) {
            $defaults["activity_type_id"] = $subType;
        }
       
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }

        $defaults['scheduled_date_time'] = array( );
        CRM_Utils_Date::getAllDefaultValues( $defaults['scheduled_date_time'] );
        $defaults['scheduled_date_time']['i'] = (int ) ( $defaults['scheduled_date_time']['i'] / 15 ) * 15;

        return $defaults;
        
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
       
        $currentPath = CRM_Utils_System::currentPath( );
        $url = CRM_Utils_System::url( $currentPath, '_qf_Record_display=true', true, null, false  );
        $this->assign("refreshURL",$url); 
        
        $session =& CRM_Core_Session::singleton( ); 
        $this->_userID  =  $session->get( 'userID' ); 
        list( $this->_displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $this->_userID ); 
        if ( ! $this->_userID || ! $this->_displayName ) { 
            CRM_Core_Error::statusBounce( ts('Your user record does not have a valid user ID' )); 
        }
        $this->assign( 'displayName', $this->_displayName );

        // add select for tag
        $this->_activityType =
            array( ''   => ' - select activity - ' ) + 
            CRM_Core_PseudoConstant::ActivityType( true );
        unset( $this->_activityType[8] );
        $this->add('select', 'activity_type_id', ts('Activity Type'),
                   $this->_activityType,
                   true, array('onchange' => "reload(true)"));

        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ), true ); 
 
        $this->add('date', 'scheduled_date_time', ts('Date and Time'), CRM_Core_SelectValues::date('activityDatetime'), true); 
        $this->addRule('scheduled_date_time', ts('Select a valid date.'), 'qfDate'); 
         
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours()); 
        $this->add('select','duration_minutes', null,CRM_Core_SelectValues::getMinutes()); 
 
        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'location' ) ); 
         
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'details' ) ); 
         
        $this->add('select','status',ts('Status'), CRM_Core_PseudoConstant::activityStatus(), true); 

        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        
        $this->assign('recordActivity', true);

        $this->addDefaultButtons( ts('Record Activity for Contacts') );
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Contact_Form_Task_Record', 'formRule' ) );
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

        if ( $fields['activity_type_id'] == 3 && $fields['status'] == 'Scheduled' ) {
            $errorMsg['status'] = ts('You cannot record scheduled email activity.');
        } else if ( $fields['activity_type_id'] == 4 && $fields['status'] == 'Scheduled' ) {
            $errorMsg['status'] = ts('You cannot record scheduled SMS activity.');
        }


        if ( !empty($errorMsg) ) {
            return $errorMsg;
        }
        
        return true;
    }    



    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $params = $this->controller->exportValues( $this->_name );
        
        // store the date with proper format 
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['scheduled_date_time'] );
        $params['status_id']          = $params['status'];
 
        // store the contact id and current drupal user id 
        $params['source_contact_id'] = $this->_userID; 
       
        list( $total, $added ) = array( count( $this->_contactIds ), 0 );

        require_once 'CRM/Activity/BAO/Activity.php';        
        foreach ( $this->_contactIds as $contactId ) {
            $params['target_contact_id'] = $contactId;  
            $activity = CRM_Activity_BAO_Activity::create( $params );
            if ( $activity ) {
                $added++;
            } 
        }

        $status = array(
                        'Activity: ' . $this->_activityType[$params['activity_type_id']],
                        'Subject: ' . $params['subject'],
                        'Total Selected Contact(s): '  . $total,
                        'Recorded for Contact(s): '  . $added
                        );

        CRM_Core_Session::setStatus( $status );
    }//end of function


}


