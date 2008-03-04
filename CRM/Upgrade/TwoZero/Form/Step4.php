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

class CRM_Upgrade_TwoZero_Form_Step4 extends CRM_Upgrade_Form {

    protected $_ahEntry    = false;
    protected $_ehEntry    = false;
    protected $_meetEntry  = false;
    protected $_phoneEntry = false;

    protected $_tplMessage = null;

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = 'pre-condition failed for upgrade step 4';

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
            $errorMessage = ts('Database consistency check failed for step 4. FK constraint names not in the required format.');
            return false;
        }

        return $this->checkVersion( '1.92' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'activity.mysql' ) );
        $this->source( $sqlFile );
        
        $this->setVersion( '1.93' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = 'post-condition failed for upgrade step 4';
        
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

