<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
     * @static
     */
    static function backupData ( ) 
    {
        global $civicrm_root;
        $file = $civicrm_root . '/sql/civicrm_backup.mysql';
        
        //we get the upload folder for storing the huge backup data
        $config =& new CRM_Core_Config();
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
            CRM_Utils_System::statusBounce( ts( 'We could not find the backup sql script. Check sql/civicrm_backup.mysql in the CiviCRM root directory.' ) );
        }

        // make sure mysqldump exists
        if ( ! file_exists( $config->mysqlPath . 'mysqldump' ) ) {
            CRM_Utils_System::statusBounce( ts( 'We could not find the mysqldump program. Check the configuration variable CIVICRM_MYSQL_PATH in your CiviCRM config file.' ) );
        }


        foreach($sql as $value) {
            $val = explode("|", $value);
            $domainDAO =& new CRM_Core_DAO();
            $domainDAO->query($val[1]);
            
            $ids = array( );
            while ( $domainDAO->fetch( ) ) {
                $ids[] = $domainDAO->id; 
            }
                        
            if ( !empty($ids) ) {
                $dumpCommand = $config->mysqlPath."mysqldump  -u".$username." -p".$password." --opt --single-transaction  ".$database." ". $val[0] ." -w 'id IN ( ".implode(",", $ids)." ) ' >> " . $fileName;
                exec($dumpCommand); 
            }
        }

        /*speacial case for table civicrm_custom_option
        need to fix*/

        $dumpCommand = $config->mysqlPath."mysqldump  -u".$username." -p".$password." --opt --single-transaction  ".$database." "."civicrm_custom_option >> " . $fileName;
        exec($dumpCommand);

        $tarFileName = 'backupData.tgz';

        if ( is_file($tarFileName) ) {
            unlink($tarFileName);
        }

        $tarCommand = 'tar -czf '.$tarFileName.' '.$fileName;
        exec($tarCommand);
        
        $fileSize = filesize( $tarFileName );
        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('ContentType Extension=".tgz" ContentType="application/x-compressed" ');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename=backupData.tgz');

        readfile($tarFileName);
    }
}

?>
