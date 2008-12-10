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

require_once 'CRM/Upgrade/Form.php';

class CRM_Upgrade_TwoZero_Form_Step4 extends CRM_Upgrade_Form {

    protected $_ahEntry    = false;
    protected $_ehEntry    = false;
    protected $_meetEntry  = false;
    protected $_phoneEntry = false;

    protected $_tplMessage = null;

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '4'));
        
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_loc_block' ) ) {
            return false;
        }
        
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_address', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_phone',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_im', 'contact_id' )    ) {
            return false;
        }
        
        // check whether civicrm_{meeting,phonecall} are used only for contacts 
        // and don't have any parent_ids set
        if ( ! CRM_Core_DAO::checkFieldHasAlwaysValue('civicrm_meeting',   'target_entity_table', 'civicrm_contact') ||
             ! CRM_Core_DAO::checkFieldHasAlwaysValue('civicrm_phonecall', 'target_entity_table', 'civicrm_contact') ||
             ! CRM_Core_DAO::checkFieldIsAlwaysNull('civicrm_meeting',   'parent_id') ||
             ! CRM_Core_DAO::checkFieldIsAlwaysNull('civicrm_phonecall', 'parent_id')) {
            return false;
        }

        // check FK constraint names are in valid format.
        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_activity', 'source_contact_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_activity', 'parent_id') ) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '4')) . ' ' . ts('FK constraint names not in the required format.') . ' ' . ts('Please rebuild your 1.9 database to ensure schema integrity.');
            return false;
        }

        return $this->checkVersion( '1.92' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'activity.mysql' ) );
        $this->source( $sqlFile );
        
        $domainID   = CRM_Core_Config::domainID( );

        $actStatusIdQry = "
SELECT ov.value FROM civicrm_option_value ov 
WHERE  ov.option_group_id=(
SELECT og.id FROM civicrm_option_group og 
WHERE  og.domain_id=$domainID AND og.name='activity_status') AND label like 'Completed'";
        $as = $this->runQuery( $actStatusIdQry );
        $as->fetch();

        $ogIdQry    = "
SELECT id FROM civicrm_option_group 
WHERE domain_id = $domainID AND name = 'activity_type'";
        $og = $this->runQuery( $ogIdQry );
        $og->fetch();

        $query        = "SELECT DISTINCT activity_type FROM civicrm_activity_history";
        $activityType = $this->runQuery( $query );

        while ( $activityType->fetch() ) {
            $activityTypeIdQry = "
SELECT value FROM civicrm_option_value 
WHERE  option_group_id={$og->id} AND label like '{$activityType->activity_type}'";
            $ov = $this->runQuery( $activityTypeIdQry );

            if ( $ov->fetch() ) {
                // migration to activity table
                $insertQry = "
INSERT INTO civicrm_activity (source_contact_id, source_record_id, activity_type_id, subject, 
            activity_date_time, due_date_time, duration, location, phone_id, phone_number, 
            details, status_id, priority_id, parent_id, is_test) 
SELECT ah.entity_id, ah.activity_id, {$ov->value}, ah.activity_summary, 
       ah.activity_date, NULL, NULL, NULL, NULL, NULL, 
       ah.activity_summary, {$as->value}, NULL, NULL, ah.is_test 
FROM   civicrm_activity_history ah
WHERE  ah.activity_type='{$activityType->activity_type}'";
                $this->runQuery( $insertQry );

                $deleteQry = "
DELETE FROM civicrm_activity_history 
WHERE  activity_type='{$activityType->activity_type}'";
                $this->runQuery( $deleteQry );
            }
        }

        // migration to target and assignment table
        $insertQry = "
INSERT INTO civicrm_activity_target (activity_id, target_contact_id)
SELECT ca.id, ca.source_contact_id 
FROM   civicrm_activity ca
LEFT JOIN civicrm_activity_target cat ON (ca.id = cat.activity_id)
WHERE cat.activity_id IS NULL
ON DUPLICATE KEY UPDATE activity_id=ca.id";
        $this->runQuery( $insertQry );

        $insertQry = "
INSERT INTO civicrm_activity_assignment (activity_id, assignee_contact_id)
SELECT ca.id, ca.source_contact_id 
FROM   civicrm_activity ca
LEFT JOIN civicrm_activity_assignment cas ON (ca.id = cas.activity_id)
WHERE cas.activity_id IS NULL
ON DUPLICATE KEY UPDATE activity_id=ca.id";
        $this->runQuery( $insertQry );

        // drop activity history table if empty
        $query = "SELECT id FROM civicrm_activity_history LIMIT 1";
        $res   = $this->runQuery( $query );
        if ($res->fetch()) {
            CRM_Core_Session::setStatus( ts('%1: This database includes Activity History records which were generated by 3rd party modules. We are unable to migrate these records automatically to the 2.0 record structure. Un-migrated records have been retained in the civicrm_activity_history table, and can be reviewed using phpMyAdmin or a MySQL command line query. Consult this document if you are interested in migrating the records manually: %2', 
                                            array( 1 => "<strong>WARNING</strong>", 
                                                   2 => "<a href='http://wiki.civicrm.org/confluence/display/CRMDOC/Migrate+3rd+Party+Activity+History+Records'>Migrate 3rd Party Activity History Records</a>")
                                            ));
        } else {
            $deleteQry = "DROP TABLE civicrm_activity_history";
            $this->runQuery( $deleteQry );
        }

        $this->setVersion( '1.93' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '4'));
        
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_assignment' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_activity_target' )   ) {
            return false;
        }

        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'source_record_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_activity', 'due_date_time'    )) {
            return false;
        }

        return $this->checkVersion( '1.93' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Four (Activity Upgrade)' );
    }

    function getTemplateMessage( ) {
        $this->_tplMessage = '<p>This step will upgrade the activity section of your database.</p>' . $this->_tplMessage;
        return $this->_tplMessage;
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }
}

