<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Core/OptionValue.php';

class CRM_Upgrade_ThreeOne_ThreeOne extends CRM_Upgrade_Form {

    function verifyPreDBState( &$errorMessage ) {
        $latestVer  = CRM_Utils_System::version();
        
        $errorMessage = ts('Pre-condition failed for upgrade to %1.', array( 1 => $latestVer ));

        // check table, if the db is 3.1
        if ( CRM_Core_DAO::checkTableExists( 'civicrm_acl_contact_cache' ) ) {
            $errorMessage =  ts("Database check failed - it looks like you have already upgraded to the latest version (v%1) of the database. OR If you think this message is wrong, it is very likely that this a partially upgraded db and you will need to reload the correct db on which upgrade was never tried.", array( 1 => $latestVer ));
            return false;
        } 

        // check table-column, if the db is 3.1 
        if ( CRM_Core_DAO::checkFieldExists( 'civicrm_custom_field', 'date_format' ) ) {
            $errorMessage =  ts("Database check failed - it looks like you have already upgraded to the latest version (v%1) of the database. OR If you think this message is wrong, it is very likely that this a partially upgraded db and you will need to reload the correct db on which upgrade was never tried.", array( 1 => $latestVer ));
            return false;
        } 
        
        //check previous version table e.g 3.0.*
        if ( ! CRM_Core_DAO::checkTableExists( 'civicrm_participant_status_type' ) ||
             ! CRM_Core_DAO::checkTableExists( 'civicrm_navigation' ) ) {
            $errorMessage .= ' Few important tables were found missing.';
            return false;
        }
        
        // check fields which MUST be present if a proper 3.0.* db
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_relationship_type', 'label_a_b' )      ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_mapping_field',     'im_provider_id' ) ||
             ! CRM_Core_DAO::checkFieldExists( 'civicrm_contact',           'email_greeting_id' ) ) {
            // db looks to have stuck somewhere between 2.2 & 3.0
            $errorMessage .= ' Few important fields were found missing in some of the tables.';
            return false;
        }

        return true;
    }
    
    function upgrade( $rev ) {

        $upgrade =& new CRM_Upgrade_Form( );

        //Run the SQL file
        $upgrade->processSQL( $rev );

        // fix for CRM-5162
        // we need to encrypt all smtpPasswords if present
        require_once "CRM/Core/DAO/Preferences.php";
        $mailingDomain =& new CRM_Core_DAO_Preferences();
        $mailingDomain->find( );
        while ( $mailingDomain->fetch( ) ) {
            if ( $mailingDomain->mailing_backend ) {
                $values = unserialize( $mailingDomain->mailing_backend );
                
                if ( isset( $values['smtpPassword'] ) ) {
                    require_once 'CRM/Utils/Crypt.php';
                    $values['smtpPassword'] = CRM_Utils_Crypt::encrypt( $values['smtpPassword'] );
                    $mailingDomain->mailing_backend = setialize( $values );
                    $mailingDomain->save( );
                }
            }
        }
        
        require_once "CRM/Core/DAO/Domain.php";
        $domain =& new CRM_Core_DAO_Domain();
        $domain->selectAdd( );
        $domain->selectAdd( 'config_backend' );
        $domain->find(true);
        if ( $domain->config_backend ) {
            $defaults = unserialize($domain->config_backend);
            if ( $dateFormat = $defaults['dateformatQfDate'] ) {
                $dateFormatArray =  explode(" ", $dateFormat );

                //replace new date format based on previous month format
                //%b month name [abbreviated]
                //%B full month name ('January'..'December')
                //%m decimal number, 0-padded ('01'..'12')                
                if ( in_array('%b', $dateFormatArray) ) {
                    $defaults['dateInputFormat']= 'd M, y';
                } else if ( in_array('%B', $dateFormatArray) ) {
                    $defaults['dateInputFormat']= 'd MM, y';
                } else {
                    $defaults['dateInputFormat']= 'mm/dd/yy';
                }
            }

            //Need to fix
            // %p - lowercase ante/post meridiem ('am', 'pm')
            // %P - uppercase ante/post meridiem ('AM', 'PM')
            $defaults['timeInputFormat'] = 1;
            
            unset($defaults['dateformatQfDate']);
            unset($defaults['dateformatQfDatetime']);
            unset($defaults['dateformatTime']);
            require_once "CRM/Core/BAO/Setting.php";
            CRM_Core_BAO_Setting::add($defaults);                            
        }
    }
}
