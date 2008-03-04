<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/** 
 *  This file contains functions for Domain Dump
 */

require_once 'DB.php';

class CRM_Core_BAO_DomainDump  
{
    /**
     * Function to create the dump from backup.sql
     * 
     * @param NULL
     *
     * @return void
     * 
     * @access public
     * @static
     */
    static function backupData ( ) 
    {
        global $civicrm_root;
        $file = $civicrm_root . '/sql/civicrm_backup.mysql';
        
        //we get the upload folder for storing the huge backup data
        $config =& CRM_Core_Config::singleton( );
        chdir($config->uploadDir);
        $fileName = 'domainDump.sql';
        
        //get the username and password from dsn
        $values = DB::parseDSN($config->dsn);
        
        $username  = $values['username'];
        $password  = $values['password'];
        $database  = $values['database'];

        if ( is_file($fileName) ) {
            unlink($fileName);
        }
        
        //read the contents of the file into an array
        $sql = file($file);

        if ( empty( $sql ) ) {
            CRM_Core_Error::statusBounce( ts( 'We could not find the backup sql script. Check %1 exists and is readable by the webserver.', array(1 => $file ) ) );
        }
        
        // make sure mysqldump exists
        if ( ! file_exists( $config->mysqlPath . 'mysqldump' ) ) {
            if ( ! file_exists( $config->mysqlPath . 'mysqldump.exe' ) ) {
                CRM_Core_Error::statusBounce( ts( 'We could not find the mysqldump program. Check the configuration variable CIVICRM_MYSQL_PATH in your CiviCRM config file.' ) );
            } else {
                $mysqlExe = $config->mysqlPath . 'mysqldump.exe';
            }
        } else {
            $mysqlExe = $config->mysqlPath . 'mysqldump';
        }

        foreach($sql as $value) {
            $val = explode("|", $value);
            $domainDAO =& new CRM_Core_DAO();
            $domainDAO->query($val[1]);
            
            $ids = array( );
            while ( $domainDAO->fetch( ) ) {
                $ids[] = $domainDAO->id; 
            }

            $clause = null;
            if ( ! empty( $ids ) ) {
                $clause = "-w 'id IN ( " . implode( ",", $ids ) . " ) '";
            }
            $dumpCommand = "$mysqlExe -u{$username} -p{$password} --opt --single-transaction $database {$val[0]} $clause >> $fileName";
            exec($dumpCommand); 
        }

        $output  = file_get_contents( $fileName );
        if ( function_exists( 'gzencode' ) ) {
            $output    = gzencode( $output, 9 );
            $type      = "application/x-gzip";
            $ext       = ".gz";
            $fileName .= $ext;
        } else {
            $type   = "text/plain";
            $ext    = ".txt";
        }

        $fileSize = strlen( $output ) + 1;
        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header("ContentType='$type'");
        header("Content-Length: $fileSize");
        header("Content-Disposition: attachment; filename=$fileName");

        echo $output;
        exit( );
    }
}

