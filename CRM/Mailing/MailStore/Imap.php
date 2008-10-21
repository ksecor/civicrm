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

require_once 'ezc/Base/src/ezc_bootstrap.php';
require_once 'ezc/autoload/mail_autoload.php';
require_once 'CRM/Mailing/MailStore.php';

class CRM_Mailing_MailStore_Imap extends CRM_Mailing_MailStore
{
    /**
     * Connect to the supplied IMAP server and make sure the two mailboxes exist
     *
     * @param string $host      host to connect to
     * @param string $username  authentication username
     * @param string $password  authentication password
     * @param bool   $ssl       whether to use IMAP or IMAPS
     * @param string $folder    name of the inbox folder
     * @return void
     */
    function __construct($host, $username, $password, $ssl = true, $folder = 'Inbox')
    {
        $options = array('ssl' => $ssl);
        $this->_transport = new ezcMailImapTransport($host, null, $options);
        $this->_transport->authenticate($username, $password);
        $this->_transport->selectMailbox($folder);

        $this->_ignored   = 'CiviMail.ignored';
        $this->_processed = 'CiviMail.processed';
        $boxes = $this->_transport->listMailboxes();
        if (!in_array($this->_ignored,   $boxes)) $this->_transport->createMailbox($this->_ignored);
        if (!in_array($this->_processed, $boxes)) $this->_transport->createMailbox($this->_processed);
    }

    /**
     * Move the specified message to the ignored folder
     *
     * @param integer $nr  number of the message to move
     * @return void
     */
    function markIgnored($nr)
    {
        $this->_transport->copyMessages($nr, $this->_ignored);
        $this->_transport->delete($nr);
    }

    /**
     * Move the specified message to the processed folder
     *
     * @param integer $nr  number of the message to move
     * @return void
     */
    function markProcessed($nr)
    {
        $this->_transport->copyMessages($nr, $this->_processed);
        $this->_transport->delete($nr);
    }
}
