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

class CRM_Upgrade_TwoZero_Form_Step3 extends CRM_Upgrade_Form {

    function verifyPreDBState( ) {

        $query = "SHOW COLUMNS FROM civicrm_domain LIKE 'version'";
        $res   = $this->runQuery( $query );
        $row   = $res->fetchRow( DB_FETCHMODE_ASSOC );

        if (! isset($row['Field'])) {
            // Go to step1
        } else {
            $domainID = CRM_Core_Config::domainID();
            $query    = "SELECT version FROM civicrm_domain WHERE id=$domainID";
            $res      = $this->runQuery( $query );
            $row      = $res->fetchRow( DB_FETCHMODE_ASSOC );
            
            if ((double)$row['version'] == 1.92) {
                $currentDir = dirname( __FILE__ );
                $sqlFile    = implode( DIRECTORY_SEPARATOR,
                                       array( $currentDir, 'sql', 'activity.mysql' ) );
                $this->source( $sqlFile );
                
                $query = "UPDATE `civicrm_domain` SET version='1.93'";
                $res   = $this->runQuery( $query );
            } elseif ((double)$row['version'] > 1.92) {
                // This step is already done. Move to next step
            } else {
                // Move to previous step.
            }
        }
    }

    function upgrade( ) {
    }

    function verifyPostDBState( ) {
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Three (Activity Upgrade)' );
    }

    function getTemplateMessage( ) {
        return ts( 'This is a message' );
    }

    function getButtonTitle( ) {
        return ts( 'Proceed to Step Four' );
    }

}
?>
