<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/GroupContactCache.php';

class CRM_Contact_BAO_GroupContactCache extends CRM_Contact_DAO_GroupContactCache {

    const
        NUM_CONTACTS_TO_INSERT = 5;

    /**
     * Check to see if we have cache entries for this group
     * if not, regenerate, else return
     *
     * @param int $groupID groupID of group that we are checking against
     *
     * @return boolean true if we did not regenerate, false if we did
     */
    static function check( $groupID ) {
        $cacheDate = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group',
                                                  $groupID,
                                                  'cache_date' );

        // we'll modify the below if to regenerate if cacheDate is quite old
        if ( $cacheDate != null ) {
            return true;
        }
        self::add( $groupID );
        return false;
    }

    static function add( $groupID ) {
        // first delete the current cache
        self::remove( $groupID );
        if ( ! is_array( $groupID ) ) {
            $groupID = array( $groupID );
        }

        $params['return.contact_id'] = 1;
        $params['offset']            = 0;
        $params['rowCount']          = 0;
        $params['sort']              = null;
        $params['smartGroupCache']   = false;

        require_once 'api/v2/Contact.php';
        
        $values = array( );
        foreach ( $groupID as $gid ) {
            $params['group'] = array( );
            $params['group'][$gid] = 1;

            // the below call update the cache table as a byproduct of the query
            $contacts = civicrm_contact_search( $params );
        }
    }

    static function store( &$groupID, &$values ) {

        // to avoid long strings, lets do NUM_CONTACTS_TO_INSERT values at a time
        while ( ! empty( $values ) ) {
            $input = array_splice( $values, 0, self::NUM_CONTACTS_TO_INSERT );
            $str   = implode( ', ', $input );
            $sql = "INSERT INTO civicrm_group_contact_cache (group_id,contact_id) VALUES $str;";
            CRM_Core_DAO::executeQuery( $sql,
                                        CRM_Core_DAO::$_nullArray );
        }

        // also update the group with cache date information
        $now = date('YmdHis');
        $groupIDs = implode( ',', $groupID );
        $sql = "
UPDATE civicrm_group
SET    cache_date = $now
WHERE  id IN ( $groupIDs )
";
        CRM_Core_DAO::executeQuery( $sql,
                                    CRM_Core_DAO::$_nullArray );
    }

    static function remove( $groupID = null ) {
        if ( ! isset( $groupID ) ) {
            $domainID = CRM_Core_Config::domainID( );
            $query = "
DELETE     g
FROM       civicrm_group_contact_cache g
INNER JOIN civicrm_contact c ON c.id = g.contact_id
WHERE      c.domain_id = %1
";

            $update = "
UPDATE civicrm_group g
SET    cache_date = null
WHERE  g.domain_id = %1
";
            $params = array( 1 => array( $domainID, 'Integer' ) );
        } else if ( is_array( $groupID ) ) {
            $query = "
DELETE     g
FROM       civicrm_group_contact_cache g
WHERE      g.group_id IN ( %1 )
";
            $update = "
UPDATE civicrm_group g
SET    cache_date = null
WHERE  g.group_id IN ( %1 )
";
            $groupIDs = implode( ', ', $groupID );
            $params = array( 1 => array( $groupIDs, 'String' ) );
        } else {
            $query = "
DELETE     g
FROM       civicrm_group_contact_cache g
WHERE      g.group_id = %1
";
            $update = "
UPDATE civicrm_group g
SET    cache_date = null
WHERE  g.id = %1
";
            $params = array( 1 => array( $groupID, 'Integer' ) );
        }

        CRM_Core_DAO::executeQuery( $query , $params );

        // also update the cache_date for these groups
        CRM_Core_DAO::executeQuery( $update, $params );
    }
    
}



