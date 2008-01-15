<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

require_once 'CRM/Upgrade/Form.php';

class CRM_Upgrade_TwoZero_Form_Step1 extends CRM_Upgrade_Form {

    function verifyPreDBState( ) {
    }

    function upgrade( ) {
        // check if field version exists
        $query = "SHOW COLUMNS FROM civicrm_domain LIKE 'version'";
        $res   = $this->runQuery( $query );
        $row   = $res->fetchRow( DB_FETCHMODE_ASSOC );

        // Don't do structure/data upgrade if version column exists
        if (! isset($row['Field'])) {
            $currentDir = dirname( __FILE__ );
            $sqlFile    = implode( DIRECTORY_SEPARATOR,
                                   array( $currentDir, 'sql', 'contact.mysql' ) );
            $this->source( $sqlFile );
            
            // add column 'version'
            $query = "ALTER TABLE `civicrm_domain` ADD `version` varchar(8) NULL DEFAULT NULL COMMENT 'The civicrm version this instance is running' AFTER config_backend";
            $res   = $this->runQuery( $query );
            
            // mark the level completed
            $query = "UPDATE `civicrm_domain` SET version='1.91'";
            $res   = $this->runQuery( $query );
        } else {
            // This step already done. Move to next step.
        }
    }
    
    function verifyPostDBState( ) {
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step One (Contact Upgrade)' );
    }

    function getTemplateMessage( ) {
        return ts( 'This is a message' );
    }

    function getButtonTitle( ) {
        return ts( 'Proceed to Step Two' );
    }

}


?>
