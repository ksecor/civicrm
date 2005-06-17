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
        // $this->add('text', 'Caller_name'       , ts('Caller Name')       ,'');

        $this->add('date', 'phonecall_date', ts('Phone Call Date'), CRM_Core_SelectValues::date( 'relative' ) );
        $this->add('select','phone_id',ts('Phone Number'), array ('Select Phone Number') + CRM_Contact_BAO_Phone::getphoneNumber($this->_contactId)  );
        $this->add('text', 'phone_number'  , ts(' OR New Phone') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'phone_number' ));
        $this->addRule( 'phone_number', ts('Phone number is not valid.'), 'phone' );
        $this->add('select','status',ts('Status'),CRM_Core_SelectValues::phoneStatus());
        $this->add('textarea', 'call_log'       , ts('Call Log')       ,CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Phonecall', 'call_log' ));
        $this->add('select', 'priority'       , ts('Priority')       ,CRM_Core_SelectValues::phonePriority());
        $this->add('date', 'next_phonecall_datetime', ts('Next Call Date'), CRM_Core_SelectValues::date( 'relative' ) );
        
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
        $params = $this->exportValues();
               
        $ids = array();

        // store the contact id
        $ids['contact'] = $this->_contactId;
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['call'] = $this->_id;
        }

        $call = CRM_Core_BAO_Call::add($params, $ids);
        
        CRM_Core_Session::setStatus( ts('Phone Call has been saved.') );
    }//end of function


}

?>
