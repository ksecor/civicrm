<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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

/**
 * This class generates form components for Call
 * 
 */
class CRM_Activity_Form_Phonecall extends CRM_Activity_Form
{

    /**
     * variable to store activity type name
     *
     */
    public $_activityType = 'Phonecall';

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
        
        $this->add('text', 'subject', ts('Subject'), CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'subject' ));
        $this->addRule( 'subject', ts('The Field Subject should not be Empty'), 'required' );
        $this->add('date', 'scheduled_date_time', ts('Date and Time'),CRM_Core_SelectValues::date('datetime'));
        //$this->addRule( 'scheduled_date_time', ts('Please enter a valid date and time for this call.'), 'qfDate' );
        $this->addRule( 'scheduled_date_time', ts('Call Date and Time are required.'), 'required' );

        $this->add('select','phone_id',ts('Phone Number'), $contactPhone );
        $this->add('text', 'phone_number'  , ' ' . ts('OR New Phone') , CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'phone_number' ));
        $this->add('select', 'duration_hours', '', CRM_Core_SelectValues::getHours());
        $this->add('select', 'duration_minutes', '', CRM_Core_SelectValues::getMinutes());
        
        $status =& $this->add('select','status',ts('Status'),CRM_Core_SelectValues::activityStatus(true));
        $this->addRule( 'status', ts('Please select status.'), 'required' );
        
        $this->add('textarea', 'details'       , ts('Details')       ,CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Phonecall', 'details' ));
        
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
            CRM_Activity_BAO_Activity::del( $this->_id, $this->_activityType);
            CRM_Core_Session::setStatus( ts("Selected Phone Call is deleted sucessfully."));
            return;
        }

        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );       
        $ids = array();
        
        $dateTime = $params['scheduled_date_time'];

        $dateTime = CRM_Utils_Date::format($dateTime);
        
        // store the date with proper format
        $params['scheduled_date_time']= $dateTime;
        
        // store the contact id and current drupal user id
        $params['source_contact_id'  ] = $this->_sourceCID;
        $params['target_entity_id'   ] = $this->_targetCID;
        $params['target_entity_table'] = 'civicrm_contact';
        
        //set parent id if exists for follow up activities
        if ($this->_pid) {
            $params['parent_id'] = $this->_pid;            
        }
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['id'] = $this->_id;
        }
      
        require_once "CRM/Activity/BAO/Activity.php";
        CRM_Activity_BAO_Activity::createActivity($params, $ids, $this->_activityType);

    }
}

?>
