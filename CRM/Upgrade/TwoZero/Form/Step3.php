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

class CRM_Upgrade_TwoZero_Form_Step3 extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $errorMessage = ts('Pre-condition failed for upgrade step %1.', array(1 => '3'));

        // ensure that version field exists in db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_domain', 'version' ) ) {
            return false;
        }

        // also ensure first_name, household_name and contact_name exist in db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'first_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'household_name' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact', 'organization_name' ) ) {
            return false;
        }

        if (! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'county_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'state_province_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_address', 'country_id') ||
            ! CRM_Core_DAO::checkFKConstraintInFormat('civicrm_event',   'payment_processor_id') ) {
            $errorMessage = ts('Database consistency check failed for step %1.', array(1 => '3')) . ' ' . ts('FK constraint names not in the required format.') . ' ' . ts('Please rebuild your 1.9 database to ensure schema integrity.');
            return false;
        }

        return $this->checkVersion( '1.91' );
    }

    function upgrade( ) {
        $currentDir = dirname( __FILE__ );
        $sqlFile    = implode( DIRECTORY_SEPARATOR,
                               array( $currentDir, '../sql', 'location.mysql' ) );
        $this->source( $sqlFile );

        // now clean up the is_primary issues
        self::cleanupIsPrimary( );

        $this->setVersion( '1.92' );
    }

    function verifyPostDBState( &$errorMessage ) {
        $errorMessage = ts('Post-condition failed for upgrade step %1.', array(1 => '3'));

        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_loc_block' ) ) {
            return false;
        }
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_address', 'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_email',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_phone',   'contact_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_im', 'contact_id' )    ) {
            return false;
        }
        
        return $this->checkVersion( '1.92' );
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Three (Location Data Upgrade)' );
    }

    function getTemplateMessage( ) {
        return '<p>' . ts( 'This step will upgrade the location data in your database.' ) . '</p>';
    }

    function getButtonTitle( ) {
        return ts( 'Upgrade & Continue' );
    }

    function cleanupIsPrimary( ) {
        $tables = array( 'civicrm_address',
                         'civicrm_email',
                         'civicrm_phone',
                         'civicrm_im' );

        $message = '';
        foreach ( $tables as $table ) {
            $query = "
SELECT   contact_id, count(id) as cnt
FROM     $table
WHERE    is_primary = 1
AND      ( contact_id IS NOT NULL
OR         contact_id != 0 )
GROUP BY contact_id having cnt > 1
";
            $dao =& CRM_Core_DAO::executeQuery( $query,
                                                CRM_Core_DAO::$_nullArray );
            $contactIDs = array( );
            while ( $dao->fetch( ) ) {
                $contactIDs[] = $dao->contact_id;
            }
            if ( empty( $contactIDs ) ) {
                continue;
            }

            // for each group of 200 contact ids
            // find the ids of the records other than the min
            $batchSize       = 0;
            $currentContacts = array( );
            foreach ( $contactIDs as $contactID ) {
                $currentContacts[] = $contactID;
                $batchSize++;
                if ( $batchSize == 200 ) {
                    $message .= self::processBatch( $table, $currentContacts );

                    // reset batch size and currentContacts
                    $batchSize       = 0;
                    $currentContacts = array( );
                }
            }
            $message .= self::processBatch( $table, $currentContacts );
        }

        return $message;
    }

    static function processBatch( $table, &$contacts ) {
        if ( empty( $contacts ) ) {
            return '';
        }

        $message = null;
        $contactIDs = implode( ', ', $contacts );

        $query = "
SELECT id, contact_id
FROM   $table
WHERE  contact_id IN  ( $contactIDs )
ORDER BY contact_id
";
        $dao =& CRM_Core_DAO::executeQuery( $query,
                                            CRM_Core_DAO::$_nullArray );
        $idArray = array( );
        $seen    = array( );
        while ( $dao->fetch( ) ) {
            if ( array_key_exists( $dao->contact_id, $seen ) ) {
                $idArray[] = $dao->id;
            } else {
                $seen[$dao->contact_id] = 1;
            }
        }

        if ( ! empty( $idArray ) ) {
            $ids = implode( ', ', $idArray );
            $query = "
UPDATE $table
SET    is_primary = 0
WHERE  id IN ( $ids );
";
            CRM_Core_DAO::executeQuery( $query,
                                        CRM_Core_DAO::$_nullArray );
            $message = "Updating " . count( $idArray ) . " records in $table<p>\n";
        }
        return $message;
    }
}



