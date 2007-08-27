<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Core_BAO_CustomValueTable {
    function create ( &$params ) {
        if ( empty( $params ) ||
             ! is_array( $params ) ) {
            return;
        }

        foreach ( $params as $tableName => $fields ) {
            $sqlOP    = null;
            $entityID = null;
            $set      = array( );
            $params   = array( );
            $count    = 1;
            foreach ( $fields as $field ) {
                if ( ! $sqlOP ) {
                    $entityID = $field['entity_id'];
                    if ( array_key_exists( 'id', $field ) ) {
                        $sqlOP = "UPDATE $tableName ";
                        $where = " WHERE  id = %{$count}";
                        $params[$count] = array( $value, $type );
                        $count++;
                    } else {
                        $sqlOP = "INSERT INTO $tableName ";
                        $where = null;
                    }
                }

                $set[] = "{$field['column_name']} = %{$count}";
                $params[$count] = array( $field['value'], $field['type'] );
                $count++;
            }

            if ( ! empty( $set ) ) {
                $set[] = "domain_id = %{$count}";
                $params[$count] = array( CRM_Core_Config::domainID( ), 'Integer' );
                $count++;
                $set[] = "entity_id = %{$count}";
                $params[$count] = array( $entityID, 'Integer' );
                $count++;
                $set   = implode( ", ", $set );
                $query = "$sqlOP SET $set $where";
                $dao = CRM_Core_DAO::executeQuery( $query, $params );
            }
        }
    }

}

?>
