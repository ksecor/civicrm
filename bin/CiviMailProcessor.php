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

    /**
     * Process the mailbox defined by the named set of settings from civicrm_mail_settings
     *
     * @param string $name  name of the set of settings from civicrm_mail_settings (null for default set)
     * @return void
     */
    static function process($name = null) {

        require_once 'CRM/Core/DAO/MailSettings.php';
        $dao = new CRM_Core_DAO_MailSettings;
        $name ? $dao->name = $name : $dao->is_default = 1;
        if (!$dao->find(true)) throw new Exception("Could not find entry named $name in civicrm_mail_settings");

        // legacy regexen to handle CiviCRM 2.1 address patterns, with domain id and possible VERP part
        $commonRegex = '/^' . preg_quote($dao->localpart) . '(b|bounce|c|confirm|o|optOut|r|reply|re|e|resubscribe|u|unsubscribe)\.(\d+)\.(\d+)\.(\d+)\.([0-9a-f]{16})(-.*)?@' . preg_quote($dao->domain) . '$/';
        $subscrRegex = '/^' . preg_quote($dao->localpart) . '(s|subscribe)\.(\d+)\.(\d+)@' . preg_quote($dao->domain) . '$/';

        // a common-for-all-actions regex to handle CiviCRM 2.2 address patterns
        $regex = '/^' . preg_quote($dao->localpart) . '(b|c|e|o|r|u)\.(\d+)\.(\d+)\.([0-9a-f]{16})@' . preg_quote($dao->domain) . '$/';

        // retrieve the emails
        require_once 'CRM/Mailing/MailStore.php';
        $store = CRM_Mailing_MailStore::getStore($name);
        $mails = $store->allMails();

        require_once 'api/Mailer.php';
        foreach ($mails as $key => $mail) {

            // for every addressee: match address elements if it's to CiviMail
            $matches = array();
            foreach ($mail->to as $address) {
                if (preg_match($regex, $address->email, $matches)) {
                    list($match, $action, $job, $queue, $hash) = $matches;
                    break;
                } elseif (preg_match($commonRegex, $address->email, $matches)) {
                    list($match, $action, $_, $job, $queue, $hash) = $matches;
                    break;
                } elseif (preg_match($subscrRegex, $address->email, $matches)) {
                    list($match, $action, $_, $job) = $matches;
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
            // FIXME: make sure it works with Reply-Tos containing non-email stuff
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
                crm_mailer_event_subscribe($mail->from->email, $job);
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

require_once 'CRM/Core/Lock.php';
$lock = new CRM_Core_Lock('CiviMailProcessor');

if ($lock->isAcquired()) {
    $names = is_array($_REQUEST['names']) ? $_REQUEST['names'] : array(null);
    foreach ($names as $name) {
        CiviMailProcessor::process($name);
    }
} else {
    throw new Exception('Could not acquire lock, another CiviMailProcessor process is running');
}

$lock->release();
