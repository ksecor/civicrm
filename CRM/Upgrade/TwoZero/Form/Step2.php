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

class CRM_Upgrade_TwoZero_Form_Step2 extends CRM_Upgrade_Form {
    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '2'));

        if (! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' )) {
            return false;
        }

        return $this->checkVersion( '1.90' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'contact.mysql' ) );
        $this->source( $sqlFile );

        $this->setVersion( '1.91' );
    }
    
    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '2'));

        if (! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'first_name' ) ||
            ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'organization_name' ) ||
            ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'household_name' )) {
            return false;
        }

        return $this->checkVersion( '1.91' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Two (Contact Upgrade)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'Step Two will upgrade the contact records in your database.') . '</p>';
    }
            
    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }

}



