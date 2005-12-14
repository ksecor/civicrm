<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Mailing_Form_Test extends CRM_Core_Form {

    public function buildQuickForm() {
        $this->add('checkbox', 'test', ts('Send a test mailing?'));
        $defaults['test'] = true;
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
        $values = array(
            'textFile'  => $this->get('textFile'),
            'htmlFile'  => $this->get('htmlFile'),
            'header_id' => $this->get('header_id'),
            'footer_id' => $this->get('footer_id'),
            'name'      => $this->get('mailing_name'),
            'from_name' => $this->get('from_name'),
            'from_email'=> $this->get('from_email'),
            'subject'   => $this->get('subject'),
        );
        
        $this->addFormRule(array('CRM_Mailing_Form_Test', 'testMail'), $values);
        $session    =& CRM_Core_Session::singleton();
        $email = $session->get('ufEmail');
        $this->assign('email', $email);
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
    public function &testMail($params, &$files, &$options) {
        if (CRM_Utils_Array::value('_qf_Import_refresh', $_POST) ||
            ! $params['test']) 
        {
            return true;
        }
        $config =& CRM_Core_Config::singleton();
        $session    =& CRM_Core_Session::singleton();
        $contactId  = $session->get('userID');
        $email      = $session->get('ufEmail');
        
        /* Create a new mailing object for test purposes only */
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $mailing    =& new CRM_Mailing_BAO_Mailing();
        $mailing->domain_id = $session->get('domainID');
        $mailing->header_id = $options['header_id'];
        $mailing->footer_id = $options['footer_id'];
        $mailing->name = $options['name'];
        $mailing->from_name = ts('CiviCRM Test Mailer (%1)', array(1 =>
                                $options['from_name']));
        $mailing->from_email = $options['from_email'];
        $mailing->replyTo_email = $email;
        $mailing->subject = ts('Test Mailing:') . ' ' . $options['subject'];

        $mailing->body_html = file_get_contents($options['htmlFile']);
        $mailing->body_text = file_get_contents($options['textFile']);
        
        $mime =& $mailing->compose(null, null, null, 
                                    $contactId, $email, $recipient, true);

        $mailer =& $config->getMailer();

        $body = $mime->get();
        $headers = $mime->headers();
        
        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $result = $mailer->send($recipient, $headers, $body);
        CRM_Core_Error::setCallback();
        
        if ($result === true) {
            return true;
        }
        
        $errors = array( 
            '_qf_default' => 
            ts('The test mailing could not be delivered due to the following error:') . '<br /> <tt>' . $result->getMessage() . '</tt>'
        );
        return $errors;
    }

    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Test Mailing' );
    }

}

?>
