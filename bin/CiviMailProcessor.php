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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

class CiviMailProcessor {

    function process() {

        // retrieve the emails
        require_once 'CRM/Mailing/MailStore.php';
        $store = CRM_Mailing_MailStore::getStore();
        $mails = $store->allMails();

        require_once 'api/Mailer.php';
        foreach ($mails as $key => $mail) {

            // for every addressee: match address elements if it's to CiviMail
            // FIXME: the regexen should be limited to the domain
            $matches = array();
            foreach ($mail->to as $address) {
                if (preg_match('/^(b|bounce|c|confirm|o|optOut|r|reply|re|e|resubscribe|u|unsubscribe)\.(\d+).(\d+).(\d+).([0-9a-f]{16})(-.*)?@/', $address->email, $matches)) {
                    list($match, $action, $domain, $job, $queue, $hash) = $matches;
                    break;
                } elseif (preg_match('/^(s|subscribe)\.(\d+).(\d+)@/', $address->email, $matches)) {
                    list($match, $action, $domain, $group) = $matches;
                    break;
                }
            }

            // if $matches is empty, this email is not CiviMail-bound
            if (!$matches) {
                $store->markIgnored($key);
                continue;
            }

            // for bounces and replies get the plaintext and HTML parts of the message
            // FIXME: this assumes only one plain and one html part per message (and discards the rest)
            if (in_array($action, array('b', 'bounce', 'r', 'reply'))) {
                $text = $html = null;
                if ($mail->body instanceof ezcMailText) {
                    $text = $mail->body->text;
                } elseif ($mail->body instanceof ezcMailMultipart) {
                    foreach ($mail->body->getParts() as $part) {
                        switch ($part->subType) {
                        case 'plain': $text = $part->text; break;
                        case 'html':  $html = $part->text; break;
                        }
                    }
                }
            }

            // get $replyTo from either the Reply-To header or from From
            $replyTo = $mail->getHeader('Reply-To') ? $mail->getHeader('Reply-To') : $mail->from->email;

            // handle the action by passing it to the proper API call
            switch($action) {
            case 'b':
            case 'bounce':
                crm_mailer_event_bounce($job, $queue, $hash, $text);
                break;
            case 'c':
            case 'confirm':
                crm_mailer_event_confirm($job, $queue, $hash);
                break;
            case 'o':
            case 'optOut':
                crm_mailer_event_domain_unsubscribe($job, $queue, $hash);
                break;
            case 'r':
            case 'reply':
                crm_mailer_event_reply($job, $queue, $hash, $text, $replyTo, $html);
                break;
            case 'e':
            case 're':
            case 'resubscribe':
                crm_mailer_event_resubscribe($job, $queue, $hash);
                break;
            case 's':
            case 'subscribe':
                crm_mailer_event_subscribe($mail->from->email, $group);
                break;
            case 'u':
            case 'unsubscribe':
                crm_mailer_event_unsubscribe($job, $queue, $hash);
                break;
            }

            $store->markProcessed($key);
        }
    }

}

// bootstrap the environment and run the processor
session_start();
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton();

CRM_Utils_System::authenticateScript(true);

$processor = new CiviMailProcessor;
$processor->process();
