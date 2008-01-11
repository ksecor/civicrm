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

require_once 'CRM/Upgrade/Base.php';

class CRM_Upgrade_TwoZero_Step2 extends CRM_Upgrade_Base {

    function verifyPreDBState( ) {

        $query = "SHOW COLUMNS FROM civicrm_domain LIKE 'version'";
        $res   = $this->runQuery( $query );
        $row   = $res->fetchRow( DB_FETCHMODE_ASSOC );

        if (! isset($row['Field'])) {
            // go to step1
        } else {
            $query = "SELECT version FROM civicrm_domain WHERE id=1";
            $res   = $this->runQuery( $query );
            $row   = $res->fetchRow( DB_FETCHMODE_ASSOC );

            if ((double)$row['version'] == 1.91) {
                // ** Update contact table data / values
                $query = "
UPDATE civicrm_contact cc, civicrm_household ch
SET    cc.household_name=ch.household_name
WHERE ch.contact_id=cc.id";
                $res   = $this->runQuery( $query );

                $query = "
UPDATE civicrm_contact cc, civicrm_organization co 
SET 
    cc.legal_name        =co.legal_name,
    cc.organization_name =co.organization_name,
    cc.sic_code          =co.sic_code,
    cc.primary_contact_id=co.primary_contact_id
WHERE co.contact_id=cc.id";
                $res   = $this->runQuery( $query );

                $query = "
UPDATE civicrm_contact cc, civicrm_individual ci 
SET 
    cc.first_name          =ci.first_name,
    cc.middle_name         =ci.middle_name,
    cc.last_name           =ci.last_name,
    cc.prefix_id           =ci.prefix_id,
    cc.suffix_id           =ci.suffix_id,
    cc.greeting_type       =ci.greeting_type,
    cc.custom_greeting     =ci.custom_greeting,
    cc.job_title           =ci.job_title,
    cc.gender_id           =ci.gender_id,
    cc.birth_date          =ci.birth_date,
    cc.is_deceased         =ci.is_deceased,
    cc.deceased_date       =ci.deceased_date,
    cc.mail_to_household_id=ci.mail_to_household_id
WHERE ci.contact_id=cc.id";
                $res   = $this->runQuery( $query );

                $query = "UPDATE `civicrm_domain` SET version='1.921'";
                $res   = $this->runQuery( $query );
            } elseif ((double)$row['version'] < '1.91') {
                // go to previous step
            }
        } 
    }

    function upgrade( ) {
    }

    function verifyPostDBState( ) {
    }

    function getTitle( ) {
        return ts( 'CiviCRM 2.0 Upgrade: Step Two (Data Upgrade)' );
    }

    function getButtonTitle( ) {
        return ts( 'Proceed to Step Three' );
    }

}


?>
