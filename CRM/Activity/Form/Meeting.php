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
 * This class generates form components for Meeting
 * 
 */
class CRM_Activity_Form_Meeting extends CRM_Activity_Form
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
       
        $this->add('text', 'title', ts('Name') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'name' ) );
        $this->addRule( 'title', ts('Please enter a valid title.'), 'required' );

        $this->addElement('date', 'meeting_date', ts('Meeting Date'), CRM_Core_SelectValues::date());
        $this->addRule('meeting_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text', 'location', ts('Location'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'location' ) );
        
        $this->add('textarea', 'notes', ts('Notes'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'notes' ) );

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
            $ids['meeting'] = $this->_id;
        }

        $meeting = CRM_Core_BAO_Meeting::add($params, $ids);

        CRM_Core_Session::setStatus( ts('Meeting "%1" has been saved.', array( 1 => $meeting->title)) );
    }//end of function


}

?>
