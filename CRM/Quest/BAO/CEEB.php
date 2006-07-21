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
 | at http://www.openngo.org/faqs/licensing.html                       |
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
 *  this file contains functions for Referral
 */


require_once 'CRM/Quest/DAO/CEEB.php';

class CRM_Quest_BAO_CEEB extends CRM_Quest_DAO_CEEB {
    
    static function &createOrganization( $code, $phone = null ) {
        // check if the organization exists
        $query = "
SELECT o.contact_id as contact_id
FROM   civicrm_organization o,
       civicrm_custom_value v
WHERE  v.custom_field_id = 1
  AND  v.entity_table    = 'civicrm_contact'
  AND  v.int_data        = %1
";
        $params = array( 1 => array( $code, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );

        if ( $dao->fetch( ) ) {
            $org =& new CRM_Contact_DAO_Organization( );
            $org->id = $org->contact_id = $dao->contact_id;
            return $org;
        }

        // else we need to create a new org and populate it
        $args = array( 'code', 'school_name', 'street_address', 'city', 'postal_code',
                       'state_province', 'state_province_id', 'country_id', 
                       'school_type' );
        $select = implode( ',', $args );
        $query = "
SELECT   $select
FROM     quest_ceeb
WHERE    code = %1
";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        if ( ! $dao->fetch( ) ) {
            CRM_Core_Error::fatal( ts( "Could not find details for school %1", array( 1 => $code ) ) );
        }

        $params = array( );

        $params['organization_name']                           = $dao->school_name;
        $params['contact_type']                                = 'Organization';
        $params['location']                                    = array( );
        $params['location'][1]                                 = array( );
        $params['location'][1]['location_type_id']             = 1;
        $params['location'][1]['is_primary']                   = 1;
        $params['location'][1]['address']                      = array( );
        $params['location'][1]['address']['street_address']    = $dao->street_address;
        $params['location'][1]['address']['city'          ]    = $dao->city;
        $params['location'][1]['address']['postal_code'   ]    = $dao->postal_code;
        $params['location'][1]['address']['state_province_id'] = $dao->state_province_id;
        $params['location'][1]['address']['country_id'       ] = $dao->country_id;
        
        if ( $phone ) {
            $params['location'][1]['phone']    = array( );
            $params['location'][1]['phone'][1] = array( );
            $params['location'][1]['phone'][1]['phone'] = $phone;
        }
        
        $ids = array( );
        $org =& CRM_Contact_BAO_Contact::create($params, $ids, 1);
        return $org;
    }
}

?>