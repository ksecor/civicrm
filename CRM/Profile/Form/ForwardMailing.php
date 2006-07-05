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
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'api/Mailer.php';

class CRM_Profile_Form_ForwardMailing extends CRM_Core_Form
{

    function preProcess( ) 
    {
        $job_id     = CRM_Utils_Request::retrieve('jid', 'Positive',
                                                  $this, null);
        $queue_id   = CRM_Utils_Request::retrieve('qid', 'Positive',
                                                  $this, null);
        $hash       = CRM_Utils_Request::retrieve('h', 'String',
                                                  $this, null);

        $q =& CRM_Mailing_Event_BAO_Queue::verify($job_id, $queue_id, $hash);

        if ($q == null) {
            /** ERROR **/
            CRM_Utils_System::statusBounce(ts('Invalid form parameters.'));
        }
        $mailing =& $q->getMailing();
        
        /* Show the subject instead of the name here, since it's being
         * displayed to external contacts/users */
        CRM_Utils_System::setTitle(ts('Forward Mailing: %1', array(1 => $mailing->subject)));

        $this->set('queue_id'   ,   $queue_id);
        $this->set('job_id'     ,   $job_id);
        $this->set('hash'       ,   $hash);
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function &setDefaultValues( ) 
    {
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        for ($i = 0; $i < 5; $i++) {
            $this->add('text', "email_$i", ts('Email %1', array(1 => $i + 1)));
            $this->addRule("email_$i", ts('Email is not valid.'), 'email');
        }

        $this->addButtons( array(
                            array( 'type' => 'next',
                                    'name'  => ts('Forward'),
                                    'isDefault' => true),
                            array( 'type' => 'cancel',
                                    'name' => ts('Cancel'))));
    }

       
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $queue_id   = $this->get('queue_id');
        $job_id     = $this->get('job_id');
        $hash       = $this->get('hash');

        $emails = array();
        for ($i = 0; $i < 5; $i++) {
            $email = $this->controller->exportValue($this->_name, "email_$i");
            if (!empty($email)) {
                $emails[] = $email;
            }
        }
    
        foreach ($emails as $email) {
            $result = crm_mailer_event_forward( $job_id, $queue_id, 
                                                $hash, $email);
        }
    }
}

?>
