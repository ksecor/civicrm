<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for retrying a mailing
 * 
 */
class CRM_Mailing_Form_Retry extends CRM_Core_Form
{
    function preProcess( ) {
        $mailing_id     = CRM_Utils_Request::retrieve('mid', 'Positive',
                                                      $this, null);

        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->id = $mailing_id;
        $session =& CRM_Core_Session::singleton();
        $mailing->domain_id = $session->get('domainID');

        if (! $mailing->find(true)) {
            CRM_Core_Error::statusBounce(ts('Invalid Mailing ID'));
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

        require_once 'CRM/Mailing/Form/Schedule.php';
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
        require_once 'CRM/Mailing/BAO/Job.php';
        CRM_Mailing_BAO_Job::retry($mailing_id, $start_date);
        
        CRM_Core_Session::setStatus(ts('Retry scheduled for mailing: %1',
        array(1 => $mailing_name)));
        
    }//end of function

    public function getTitle() {
        return ts('Retry Mailing');
    }

}

?>
