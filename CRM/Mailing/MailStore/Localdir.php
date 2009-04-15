<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'ezc/Base/src/ezc_bootstrap.php';
require_once 'ezc/autoload/mail_autoload.php';
require_once 'CRM/Mailing/MailStore.php';

class CRM_Mailing_MailStore_Localdir extends CRM_Mailing_MailStore
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
     * Return the next X messages from the mail store
     * FIXME: in CiviCRM 2.2 this always returns all the emails
     *
     * @param int $count  number of messages to fetch FIXME: ignored in CiviCRM 2.2 (assumed to be 0, i.e., fetch all)
     * @return array      array of ezcMail objects
     */
    function fetchNext($count = 0)
    {
        $mails = array();
        $path  = rtrim($this->_dir, DIRECTORY_SEPARATOR);

        if ($this->_debug) print "fetching $count messages\n";

        $directory = new DirectoryIterator( $path );
        foreach ( $directory as $entry ) {
            if ( $entry->isDot() ) continue;
            if ( count($mails) >= $count ) break; 

            $file   = $path . DIRECTORY_SEPARATOR . $entry->getFilename();
            if ($this->_debug) print "retrieving message $file\n";

            $set    = new ezcMailFileSet( array( $file ) );
            $parser = new ezcMailParser;
            $mail   = $parser->parseMail( $set );

            if ( ! $mail ) {
                return CRM_Core_Error::createAPIError( ts( '%1 could not be parsed',
                                                           array( 1 => $file ) ) );
            }
            $mails[$file] = $mail[0];
        }

        if ($this->_debug && (count($mails) <= 0)) print "No messages found\n";

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
