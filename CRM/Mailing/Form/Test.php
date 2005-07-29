<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Mailing_Form_Test extends CRM_Core_Form {

    public function buildQuickForm() {
        $this->addButtons(
            array(
                array(  'type'  => 'prev',
                        'name'  => '<< Previous'),
                array(  'type'  => 'next',
                        'name'  => ts('Next >>'),
                        'isDefault' => true ),
                array(  'type'  => 'cancel',
                        'name'  => ts('Cancel') )
                )
            );
    }

    public function postProcess() {
        $session    =& CRM_Core_Session::singleton();
        $contactId  = $session->get('userID');
        $email      = $session->get('ufEmail');
        
        $textFile   = $this->get('textFile');
        $htmlFile   = $this->get('htmlFile');
        $header     = $this->get('mailingHeader');
        $footer     = $this->get('mailingFooter');
        
        /* Create a new mailing object for test purposes only */
        $mailing    =& new CRM_Mailing_BAO_Mailing();
        $mailing->domain_id = $session->get('domainID');
        $mailing->header_id = $header;
        $mailing->footer_id = $footer;
        $mailing->name = 'Test mailing';
        $mailing->from_name = 'Tester';
        $mailing->from_email = $email;
        $mailing->replyTo_email = $email;
        $mailing->subject = 'Test Mailing';

        $mailing->body_html = file_get_contents($htmlFile);
        $mailing->body_text = file_get_contents($textFile);
        
        $mime =& $mailing->compose(null, null, null, 
                                    $contactId, $email, $recipient, true);
        $mailer =& Mail::factory('smtp', array('host' => 'FIXME.ORG'));

        $body = $mime->get();
        $headers = $mime->headers();
        $result = $mailer->send($recipient, $headers, $body);
    
        CRM_Core_Error::debug('result', $result);
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
