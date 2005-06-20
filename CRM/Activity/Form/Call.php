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
 * This class generates form components for Call
 * 
 */
class CRM_Activity_Form_Call extends CRM_Activity_Form
{

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');
        $contactPhone[''] = 'Select Phone Number';
        if ( is_array(CRM_Contact_BAO_Phone::getphoneNumber($this->_contactId))) {
            $contactPhone = CRM_Contact_BAO_Phone::getphoneNumber($this->_contactId);
        }
        
        $this->add('text', 'subject', ts('Subject'),'');
        $this->addRule( 'subject', ts('The Field Subject should not be Empty'), 'required' );
        $this->add('date', 'scheduled_date_time', ts('Scheduled'),CRM_Core_SelectValues::date('datetime'));
        $this->addRule( 'scheduled_date_time', ts('The Field Scheduled Date should not be Empty'), 'qfDate' );
        $this->addRule( 'scheduled_date_time', ts('Please select Scheduled Date.'), 'required' );

        $this->add('select','phone_id',ts('Phone Number'), $contactPhone );
        $this->add('text', 'phone_number'  , ts(' OR New Phone') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'phone_number' ));
        $this->addRule( 'phone_number', ts('Phone number is not valid.'), 'phone' );
        $this->add('select', 'duration_hours', '', CRM_Core_SelectValues::getHours());
        $this->add('select', 'duration_minutes', '', CRM_Core_SelectValues::getMinutes());
        
        $this->add('select','status',ts('Status'),CRM_Core_SelectValues::ActivityStatus(true));
        $this->addRule( 'status', ts('Please select status.'), 'required' );

        $this->add('textarea', 'details'       , ts('Details')       ,CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'details' ));
        
        parent::buildQuickForm( );
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
         // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );       
        $ids = array();
        

        $date=CRM_Utils_Date::format( CRM_Utils_Array::value('scheduled_date_time', $params) );
        $time=CRM_Utils_Array::value('scheduled_date_time', $params);
        $h=$time['H'];
        $m=$time['i'];
        $date = $date.$h.$m.'00';
       
        $params['scheduled_date_time']= $date;
        // store the contact id
        $params['source_contact_id'] = $this->_userId;
        $params['target_contact_id'] = $this->_contactId;


        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['call'] = $this->_id;
        }
      
        $call = CRM_Core_BAO_Call::add($params, $ids);
        
        CRM_Core_Session::setStatus( ts('Phone Call "%1" has been saved.', array( 1 => $call->subject)) );
    }//end of function


}

?>
