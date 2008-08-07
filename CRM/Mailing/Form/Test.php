<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

/**
 * Form to send test mail
 */
class CRM_Mailing_Form_Test extends CRM_Core_Form 
{
    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $count = $this->get('count');
        $this->assign('count',$count);    
    }

    public function buildQuickForm() 
    {
        $session =& CRM_Core_Session::singleton();
        $this->add('text', 'test_email', ts('Send to This Address'));
        $defaults['test_email'] = $session->get('ufUniqID');
        $qfKey = $this->get('qfKey');
        
        $this->add('select',
                   'test_group',
                   ts('Send to This Group'),
                   array( '' => ts( '- none -' ) ) + CRM_Core_PseudoConstant::group( 'Mailing' ) );
        $this->setDefaults($defaults);

        $this->add('submit', 'sendtest', ts('Send a Test Mailing'));
        
        $this->addButtons(
            array(
                array(  'type'  => 'back',
                        'name'  => '<< Previous'),
                array(  'type'  => 'next',
                        'name'  => ts('Next >>'),
                        'isDefault' => true ),
                array(  'type'  => 'cancel',
                        'name'  => ts('Cancel') ),
                array ( 'type'      => 'submit',
                        'name'      => ts('Save & Continue Later') )
                )
            );

        $mailingID = $this->get('mailing_id' );
        $values = array( 'mailing_id' => $mailingID );
        $textFile = $this->get('textFile');
        $htmlFile = $this->get('htmlFile');
        $subject = $this->get('subject');
        $this->assign('subject', $subject);

        $this->addFormRule(array('CRM_Mailing_Form_Test', 'testMail'), $values);
        $preview = array();
        if ($textFile) {
            $preview['text_link'] = CRM_Utils_System::url('civicrm/mailing/preview', "type=text&qfKey=$qfKey");
        }
        if ($htmlFile) {
            $preview['html_link'] = CRM_Utils_System::url('civicrm/mailing/preview', "type=html&qfKey=$qfKey");
        }

        require_once 'CRM/Core/BAO/File.php';
        $preview['attachment'] = CRM_Core_BAO_File::attachmentInfo( 'civicrm_mailing',
                                                                    $mailingID );
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
        $error = null;
        if ($testParams['sendtest']) {
            if (!($testParams['test_group'] || $testParams['test_email'] )) {
                CRM_Core_Session::setStatus( ts("Your did not provided any email address or selected any group. No test mail is sent.") );
                $error = true;
            } elseif (substr_count($testParams['test_email'], '@') > 1) {
                CRM_Core_Session::setStatus( ts('You cannot provide more than one email address.') );
                $error = true;
            } elseif ($testParams['test_email'] && !CRM_Utils_Rule::email($testParams['test_email'])) {
                CRM_Core_Session::setStatus( ts("Please enter a valid email address") );
                $error = true;
            }

            if ($error) {
                $url = CRM_Utils_System::url( 'civicrm/mailing/send', 
                                              "_qf_Test_display=true&qfKey={$testParams['qfKey']}" );
                CRM_Utils_System::redirect($url);
                return true;
            }
        } 
        
        if ($testParams['_qf_Test_submit']) {
            CRM_Core_Session::setStatus( ts("Your mailing has been saved. Click the 'Continue' action to resume working on it.") );
            $url = CRM_Utils_System::url( 'civicrm/mailing/browse/unscheduled', 'scheduled=false&reset=1' );
            CRM_Utils_System::redirect($url);
        }
        if ( CRM_Utils_Array::value('_qf_Import_refresh', $_POST) ||
             $testParams['_qf_Test_next'] ||
             !$testParams['sendtest'] ) {
            return true;
        }
        
        require_once 'CRM/Mailing/BAO/Job.php';
        $job =& new CRM_Mailing_BAO_Job();
        $job->mailing_id = $options['mailing_id'];
        $job->is_test    = true;
        $job->save( );
        
        $testParams['job_id'] = $job->id;
        $isComplete = false;
        while (!$isComplete) {
            $isComplete = CRM_Mailing_BAO_Job::runJobs($testParams);
        }
        if ($testParams['sendtest']) {
            CRM_Core_Session::setStatus( ts("Your test message has been sent. Click 'Next' when you are ready to Schedule or Send your live mailing (you will still have a chance to confirm or cancel sending this mailing on the next page).") );
            $url = CRM_Utils_System::url( 'civicrm/mailing/send',
                                          "_qf_Test_display=true&qfKey={$testParams['qfKey']}" );
            CRM_Utils_System::redirect($url);
        }
        
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


