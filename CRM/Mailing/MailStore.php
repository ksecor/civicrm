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

class CRM_Mailing_MailStore
{
    /**
     * Return the proper mail store implementation, based on config settings
     *
     * @return object  mail store implementation for processing CiviMail-bound emails
     */
    function getStore()
    {
        // FIXME: get the params from the config
        $class = 'IMAP';

        switch ($class) {

        case 'IMAP':
            require_once 'CRM/Mailing/MailStore/Imap.php';
            return new CRM_Mailing_MailStore_Imap('server', 'username', 'password');

        case 'POP3':
            require_once 'CRM/Mailing/MailStore/Pop3.php';
            return new CRM_Mailing_MailStore_Pop3('server', 'username', 'password');

        case 'Maildir':
            require_once 'CRM/Mailing/MailStore/Maildir.php';
            return new CRM_Mailing_MailStore_Maildir('dir');

        // DO NOT USE the mbox transport for anything other than testing
        // in particular, it does not clear the mbox afterwards
        case 'mbox':
            require_once 'CRM/Mailing/MailStore/Mbox.php';
            return new CRM_Mailing_MailStore_Mbox('file');
        }
    }

    /**
     * Return all emails in the mail store
     *
     * @return array  array of ezcMail objects
     */
    function allMails()
    {
        $set = $this->_transport->fetchAll();
        print_r($set->getMessageNumbers());
        $mails = array();
        $parser = new ezcMailParser;
        foreach ($set->getMessageNumbers() as $nr) {
            $single = $parser->parseMail($this->_transport->fetchByMessageNr($nr));
            $mails[$nr] = $single[0];
        }
        return $mails;
    }

    /**
     * Point to (and create if needed) a local Maildir for storing retrieved mail
     *
     * @param string $name  name of the Maildir
     * @return string       path to the Maildir
     */
    function maildir($name)
    {
        $config =& CRM_Core_Config::singleton();
        $dir = $config->customFileUploadDir . DIRECTORY_SEPARATOR . $name;
        if (!file_exists($dir)) {
            foreach (array('cur', 'new', 'tmp') as $sub) {
                mkdir($dir . DIRECTORY_SEPARATOR . $sub, 0700, true);
            }
        }
        return $dir;
    }
}
