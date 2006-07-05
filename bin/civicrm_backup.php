<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 *  This file is for taking backups
 */

require_once '../civicrm.config.php';
require_once 'DB.php';
require_once 'CRM/Core/Config.php';

global $civicrm_root;
$file = $civicrm_root . '/sql/civicrm_backup.mysql';

//path of backup directory

global $argv;
$backupPath = $argv[1] . '/';

//we get the upload folder for storing the huge backup data

$config =& new CRM_Core_Config();
chdir($config->uploadDir);
$fileName = $backupPath . 'backupData.sql';

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
    echo "We could not find the backup sql script. Check sql/civicrm_backup.mysql in the CiviCRM root directory.\n";
    exit( );
}

// make sure mysqldump exists
if ( ! file_exists( $config->mysqlPath . "mysqldump" ) ) {
    if ( ! file_exists( $config->mysqlPath . 'mysqldump.exe' ) ) {
        echo "We could not find the mysqldump program. Check the configuration variable CIVICRM_MYSQL_PATH in your CiviCRM config file.\n";
        exit( );
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
        $clause = " -w 'id IN ( " . implode(",", $ids) . " ) '";
    }
    $dumpCommand = "{$config->mysqlPath}mysqldump  -u$username -p$password --opt --single-transaction $database {$val[0]} $clause >> $fileName";
    exec($dumpCommand); 
}

$output  = file_get_contents( $fileName ); 
if ( function_exists( 'gzencode' ) ) { 
    $output    = gzencode( $output, 9 ); 
}

echo $output;

?>
