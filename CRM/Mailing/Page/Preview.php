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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo 01/15/2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Page.php';
/**
 * a page for mailing preview
 */
class CRM_Mailing_Page_Preview extends CRM_Core_Form {

    /** 
     * run this page (figure out the action needed and perform it).
     * 
     * @return void
     */ 
    function run() {
        require_once 'CRM/Mailing/BAO/Mailing.php';
        $session =& CRM_Core_Session::singleton();
        CRM_Core_Error::debug('$this', $this);
        exit;
        // FIXME: the below and CRM_Mailing_Form_Test::testMail() should be refactored
        $mailing =& new CRM_Mailing_BAO_Mailing();
        $mailing->domain_id = $session->get('domainID');
        CRM_Core_Error::debug('$mailing', $mailing);
        exit;

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

        $mailer =& $config->getMailer();

        if ($params['test_email']) {
            $testers = array($session->get('userID') => $params['test_email']);
        } else {
            $testers = array();
        }
        if (array_key_exists($params['test_group'], CRM_Core_PseudoConstant::group())) {
            $group =& new CRM_Contact_DAO_Group();
            $group->id = $params['test_group'];
            $contacts = CRM_Contact_BAO_GroupContact::getGroupContacts($group);
            foreach ($contacts as $contact) {
                $testers[$contact->contact_id] = $contact->email;
            }
        }
        
        $errors = array();
        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        foreach ($testers as $testerId => $testerEmail) {
            $mime =& $mailing->compose(null, null, null,
                $testerId, $testerEmail, $recipient, true);
            $body = $mime->get();
            $headers = $mime->headers();
            $result = $mailer->send($recipient, $headers, $body);
            if ($result !== true) {
                $errors['_qf_default'] =
                    ts('The test mailing could not be delivered due to the following error:') .
                    '<br /> <tt>' . $result->getMessage() . '</tt>';
            }
        }
        CRM_Core_Error::setCallback();
        
        return (count($errors) ? $errors : true);
    }

}

?>
