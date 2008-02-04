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

    protected $_ahEntry    = false;
    protected $_ehEntry    = false;
    protected $_meetEntry  = false;
    protected $_phoneEntry = false;

    protected $_tplMessage = null;

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = 'pre-condition failed for upgrade step 3';

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
            $errorMessage = ts('Database consistency check failed for step 3. FK constraint names not in the required format.');
            return false;
        }

        // check orphan entries in activity history 
        $query    = "
SELECT count(*) as ahEntry FROM civicrm_activity_history ah 
LEFT JOIN civicrm_contact cc ON ah.entity_id=cc.id WHERE cc.id is NULL;
";
        $res      = $this->runQuery( $query );
        if ($res->fetch() && $res->ahEntry >= 1) {
            $this->_tplMessage .= '<p><strong>Warning:</strong> Database consistency check failed. There are orphaned entries in activity history table. Continuing with the process will delete the these entries.</p>';
            $this->_ahEntry = true;
        }
        $res->free();
        
        // check orphan entries in email history 
        $query    = "
SELECT count(*) as ehEntry FROM civicrm_email_history eh 
LEFT JOIN civicrm_contact cc ON eh.contact_id=cc.id WHERE cc.id is NULL;
";
        $res      = $this->runQuery( $query );
        if ($res->fetch() && $res->ehEntry >= 1) {
            $this->_tplMessage .= '<p><strong>Warning:</strong> Database consistency check failed. There are orphaned entries in email history table. Continuing with the process will delete the these entries.</p>';
            $this->_ehEntry = true;
        }
        $res->free();

        // check orphan entries in meeting table 
        $query    = "
SELECT count(*) as meetEntry FROM civicrm_meeting cm
LEFT JOIN civicrm_contact cc ON cm.source_contact_id=cc.id WHERE cc.id is NULL;
";
        $res      = $this->runQuery( $query );
        if ($res->fetch() && $res->meetEntry >= 1) {
            $this->_tplMessage .= '<p><strong>Warning:</strong> Database consistency check failed. There are orphaned entries in meeting table. Continuing with the process will delete the these entries.</p>';
            $this->_meetEntry = true;
        }
        $res->free();

        // check orphan entries in phonecall table 
        $query    = "
SELECT count(*) as phoneEntry FROM civicrm_phonecall cp
LEFT JOIN civicrm_contact cc ON cp.source_contact_id=cc.id WHERE cc.id is NULL;
";
        $res      = $this->runQuery( $query );
        if ($res->fetch() && $res->phoneEntry >= 1) {
            $this->_tplMessage .= '<p><strong>Warning:</strong> Database consistency check failed. There are orphaned entries in phonecall table. Continuing with the process will delete the these entries.</p>';
            $this->_phoneEntry = true;
        }
        $res->free();

        return $this->checkVersion( '1.92' );
    }

    function upgrade( ) {

        if ( $this->_ahEntry ) {
            $query = "
DELETE ah.* FROM civicrm_activity_history ah 
LEFT JOIN civicrm_contact cc ON ah.entity_id=cc.id WHERE cc.id is NULL;
";
            $res   = $this->runQuery( $query );
            $res->free();
        }

        if ( $this->_ehEntry ) {
            $query = "
DELETE ah.* FROM civicrm_email_history eh 
LEFT JOIN civicrm_contact cc ON eh.contact_id=cc.id WHERE cc.id is NULL;
";
            $res   = $this->runQuery( $query );
            $res->free();
        }

        if ( $this->_meetEntry ) {
            $query = "
DELETE cm.* FROM civicrm_meeting cm 
LEFT JOIN civicrm_contact cc ON cm.source_contact_id=cc.id WHERE cc.id is NULL;
";
            $res   = $this->runQuery( $query );
            $res->free();
        }

        if ( $this->_phoneEntry ) {
            $query = "
DELETE cp.* FROM civicrm_phonecall cp 
LEFT JOIN civicrm_contact cc ON cp.source_contact_id=cc.id WHERE cc.id is NULL;
";
            $res   = $this->runQuery( $query );
            $res->free();
        }

        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'activity.mysql' ) );
        $this->source( $sqlFile );
        
        $this->setVersion( '1.93' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = 'post-condition failed for upgrade step 3';
        
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
        return ts( 'CiviCRM 2.0 Upgrade: Step Three (Activity Upgrade)' );
    }

    function getTemplateMessage( ) {
        $this->_tplMessage = '<p>This step will upgrade the activity section of your database.</p>' . $this->_tplMessage;
        return $this->_tplMessage;
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }

}
?>
