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

/**
 * Form to send test mail
 */
class CRM_Mailing_Form_Test extends CRM_Core_Form 
{
    
    public function buildQuickForm() 
    {
        $session =& CRM_Core_Session::singleton();
        $this->add('checkbox', 'test', ts('Send a Test Mailing?'));
        $defaults['test'] = true;
        $this->add('text', 'test_email', ts('Send to This Address:'));
        $defaults['test_email'] = $session->get('ufEmail');
        $this->add('select', 'test_group', ts('Send to This Group:'), array('' => ts('- none -')) + CRM_Core_PseudoConstant::group());
        $this->setDefaults($defaults);

        $this->addButtons(
            array(
                array(  'type'  => 'back',
                        'name'  => '<< Previous'),
                array(  'type'  => 'next',
                        'name'  => ts('Next >>'),
                        'isDefault' => true ),
                array(  'type'  => 'cancel',
                        'name'  => ts('Cancel') )
                )
            );
        $values = array( 'mailing_id' => $this->get('mailing_id' ) );

        $this->addFormRule(array('CRM_Mailing_Form_Test', 'testMail'), $values);
        $preview = array(
            'text_link' => CRM_Utils_System::url('civicrm/mailing/preview', 'type=text'),
            'html_link' => CRM_Utils_System::url('civicrm/mailing/preview', 'type=html'),
        );
        $this->assign('preview', $preview);
    }
    
    /**
     * Form rule to send out a test mailing.
     *
     * @param array $params     Array of the form values
     * @param array $files      Any files posted to the form
     * @param array $options    Additional options from earlier in the wizard
     * @return boolean          true on succesful SMTP handoff
     * @access public
     */
    public function &testMail($testParams, &$files, &$options) 
    {
        if ( CRM_Utils_Array::value('_qf_Import_refresh', $_POST) ||
             ! $testParams['_qf_Test_next'] ||
             ! $testParams['test'] ) {
            return true;
        }

        require_once 'CRM/Mailing/BAO/Job.php';
        $job =& new CRM_Mailing_BAO_Job();
        $job->mailing_id = $options['mailing_id'];
        $job->is_test    = true;
        $job->save( );
        
        $testParams['job_id'] = $job->id;
        CRM_Mailing_BAO_Job::runJobs($testParams);
        return true;
    }
    
    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Test' );
    }

}

?>
