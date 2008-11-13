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

class CRM_Mailing_MailStore_Maildir extends CRM_Mailing_MailStore
{
    /**
     * Connect to the supplied dir and make sure the two mail dirs exist
     *
     * @param string $dir  dir to operate upon
     * @return void
     */
    function __construct($dir)
    {
        $this->_dir = $dir;

        $this->_ignored   = $this->maildir('CiviMail.ignored');
        $this->_processed = $this->maildir('CiviMail.processed');
    }

    /**
     * Return all emails in the mail store
     *
     * @return array  array of ezcMail objects
     */
    function allMails()
    {
        $mails = array();
        $parser = new ezcMailParser;
        foreach (array('cur', 'new') as $subdir) {
            $dir = $this->_dir . DIRECTORY_SEPARATOR . $subdir;
            foreach (scandir($dir) as $file) {
                if ($file == '.' or $file == '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $file;

                if ($this->_debug) print "retrieving message $path\n";

                $set = new ezcMailFileSet(array($path));
                $single = $parser->parseMail($set);
                $mails[$path] = $single[0];
            }
        }
        return $mails;
    }

    /**
     * Fetch the specified message to the local ignore folder
     *
     * @param integer $file  file location of the message to fetch
     * @return void
     */
    function markIgnored($file)
    {
        if ($this->_debug) print "moving $file to ignored folder\n";
        $target = $this->_ignored . DIRECTORY_SEPARATOR . basename($file);
        if (!rename($file, $target)) {
            throw new Exception("Could not rename $file to $target");
        }
    }

    /**
     * Fetch the specified message to the local processed folder
     *
     * @param integer $file  file location of the message to fetch
     * @return void
     */
    function markProcessed($file)
    {
        if ($this->_debug) print "moving $file to processed folder\n";
        $target = $this->_processed . DIRECTORY_SEPARATOR . basename($file);
        if (!rename($file, $target)) {
            throw new Exception("Could not rename $file to $target");
        }
    }
}
