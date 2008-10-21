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

class CRM_Mailing_MailStore_Pop3 extends CRM_Mailing_MailStore
{
    /**
     * Connect to the supplied POP3 server and make sure the two mail dirs exist
     *
     * @param string $host      host to connect to
     * @param string $username  authentication username
     * @param string $password  authentication password
     * @param bool   $ssl       whether to use POP3 or POP3S
     * @return void
     */
    function __construct($host, $username, $password, $ssl = true)
    {
        $options = array('ssl' => $ssl);
        $this->_transport = new ezcMailPop3Transport($host, null, $options);
        $this->_transport->authenticate($username, $password);

        $this->_ignored   = $this->maildir('CiviMail.ignored');
        $this->_processed = $this->maildir('CiviMail.processed');
    }

    /**
     * Fetch the specified message to the local ignore folder
     *
     * @param integer $nr  number of the message to fetch
     * @return void
     */
    function markIgnored($nr)
    {
        $set = new ezcMailStorageSet($this->_transport->fetchByMessageNr($nr), $this->_ignored);
        $parser = new ezcMailParser;
        $parser->parseMail($set);
        $this->_transport->delete($nr);
    }

    /**
     * Fetch the specified message to the local processed folder
     *
     * @param integer $nr  number of the message to fetch
     * @return void
     */
    function markProcessed($nr)
    {
        $set = new ezcMailStorageSet($this->_transport->fetchByMessageNr($nr), $this->_processed);
        $parser = new ezcMailParser;
        $parser->parseMail($set);
        $this->_transport->delete($nr);
    }
}
