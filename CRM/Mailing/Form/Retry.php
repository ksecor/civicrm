<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for retrying a mailing
 * 
 */
class CRM_Mailing_Form_Retry extends CRM_Core_Form
{
    function preProcess( ) {
        $mailing_id     = CRM_Utils_Request::retrieve('mid', $this, null);

        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $mailing_id;
        $session =& CRM_Core_Session::singleton();
        $mailing->domain_id = $session->get('domainID');

        if (! $mailing->find(true)) {
            CRM_Utils_System::statusBounce(ts('Invalid Mailing ID'));
        }

        $this->assign('mailing_name', $mailing->name);

        $this->set('mailing_id', $mailing->id);
        $this->set('mailing_name', $mailing->name);
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addElement('date', 'start_date', ts('Retry Date'),
            CRM_Core_SelectValues::date('mailing'));
        $this->addElement('checkbox', 'now', ts('Send Immediately'));

        $this->addFormRule(array('CRM_Mailing_Form_Schedule', 'formRule'));
    
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Retry'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
    }

    /**
     * This function sets the default values for the form.
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $mailing_id = $this->get('mailing_id');
        $mailing_name = $this->get('mailing_name');
    
        $start_date = $this->controller->exportValue($this->_name,
            'start_date');
        $now = $this->controller->exportValue($this->_name, 'now');
       
        if ($now) {
            $start_date = date('YmdHis');
        } else {
            $start_date = CRM_Utils_Date::format($start_date);
        }
        CRM_Mailing_BAO_Job::retry($mailing_id, $start_date);
        
        CRM_Core_Session::setStatus(ts('Retry scheduled for mailing: %1',
        array(1 => $mailing_name)));
        
    }//end of function

    public function getTitle() {
        return ts('Retry Mailing');
    }

}

?>
