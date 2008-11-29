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

define( 'MAIL_DIR_DEFAULT'      , '/Users/lobo/public_html/drupal6/files/civicrm/upload/incoming/');

class bin_Email2CaseActivity {

    protected $_mailDir;

    protected $_processedDir;

    protected $_errorDir;

    function __construct( $dir ) {
        $this->_mailDir = $dir;
        // create the error and processed directories
        // sort them by date
        $this->createDir( );
    }

    function createDir( ) {
        require_once 'CRM/Utils/File.php';

        // ensure that $this->_mailDir is a directory and is writable
        if ( ! is_dir( $this->_mailDir ) ||
             ! is_readable( $this->_mailDir ) ) {
            echo "Could not read from {$this->_mailDir}\n";
            exit( );
        }
        
        $config =& CRM_Core_Config::singleton( );
        $dir = $config->uploadDir . DIRECTORY_SEPARATOR . 'mail';

        $this->_processedDir = $dir . DIRECTORY_SEPARATOR . 'processed';
        CRM_Utils_File::createDir( $this->_processedDir );

        $this->_errorDir     = $dir . DIRECTORY_SEPARATOR . 'error';
        CRM_Utils_File::createDir( $this->_errorDir );

        // create a date string YYYYMMDD
        require_once 'CRM/Utils/Date.php';
        $date = CRM_Utils_Date::getToday( null, 'Ymd' );

        $this->_processedDir = $this->_processedDir . DIRECTORY_SEPARATOR . $date;
        CRM_Utils_File::createDir( $this->_processedDir );

        $this->_errorDir = $this->_errorDir . DIRECTORY_SEPARATOR . $date;
        CRM_Utils_File::createDir( $this->_errorDir );
    }


    function run( ) {
        $directory = new DirectoryIterator( $this->_mailDir );

        $success = $error = 0;
        foreach ( $directory as $entry ) {
            if ( is_dir( $this->_mailDir . DIRECTORY_SEPARATOR . $entry ) ) {
                continue;
            }

            if ( $this->process( $entry ) ) {
                $success++;
            } else {
                $error++;
            }
        }

        echo "Successfully processed $success emails. Failed processing $error emails.";
        unset( $directory );
    }

    function process( $file ) {
        require_once 'CRM/Case/BAO/Case.php';
        $result = recordActivityViaEmail( $this->_mailDir . DIRECTORY_SEPARATOR . $file );
        if ( $result['is_error'] ) {
            rename( $this->_mailDir  . DIRECTORY_SEPARATOR . $file,
                    $this->_errorDir . DIRECTORY_SEPARATOR . $file );
            echo "Failed Processing: $file. Reason: {$result['error_message']}\n";
            return false;
        } else {
            rename( $this->_mailDir      . DIRECTORY_SEPARATOR . $file,
                    $this->_processedDir . DIRECTORY_SEPARATOR . $file );
            echo "Processed: $file\n";
            return true;
        }
    }

}
    

function run( ) {
    session_start( );

    require_once '../civicrm.config.php';
    require_once 'CRM/Core/Config.php'; 
    $config =& CRM_Core_Config::singleton( );

    // this does not return on failure
    CRM_Utils_System::authenticateScript( true );

    $mailDir = MAIL_DIR_DEFAULT;
    if ( isset( $_GET['mailDir'] ) ) {
        $mailDir = $_GET['mailDir'];
    }

    $email = new bin_Email2CaseActivity( $mailDir );

    $email->run( );
}

run( );
