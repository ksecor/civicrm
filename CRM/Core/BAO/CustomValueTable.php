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
                        $params[$count] = array( $field['id'], 'Integer' );
                        $count++;
                    } else {
                        $sqlOP = "INSERT INTO $tableName ";
                        $where = null;
                    }
                }

                // fix the value before we store it
                $value = $field['value'];
                $type  = $field['type'];
                switch( $type ) {

                case 'StateProvince':
                    if ( ! is_numeric( $value ) ) {
                        $states = array( );
                        $states['state_province'] = $value;
                
                        CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province', 
                                                              CRM_Core_PseudoConstant::stateProvince(), true );
                        if ( !$states['state_province_id'] ) {
                            CRM_Contact_BAO_Contact::lookupValue( $states, 'state_province',
                                                                  CRM_Core_PseudoConstant::stateProvinceAbbreviation(), true );
                        }
                        $value = $states['state_province_id'];
                    }
                    $type = 'Integer';
                    break;

                case 'Country':
                    if ( !is_numeric( $value ) ) {
                        $countries = array( );
                        $countries['country'] = $value;
                        
                        CRM_Contact_BAO_Contact::lookupValue( $countries, 'country', 
                                                              CRM_Core_PseudoConstant::country(), true );
                        if ( ! $countries['country_id'] ) {
                            CRM_Contact_BAO_Contact::lookupValue( $countries, 'country',
                                                                  CRM_Core_PseudoConstant::countryIsoCode(), true );
                        }
                        $value = $countries['country_id'];
                    }
                    $type = 'Integer';
                    break;

                case 'File':
                    if ( ! $field['file_id'] ) {
                        CRM_Core_Error::fatal( );
                    }

                    // need to add/update civicrm_entity_file
                    require_once 'CRM/Core/DAO/EntityFile.php'; 
                    $entityFileDAO =& new CRM_Core_DAO_EntityFile();
                    $entityFileDAO->file_id = $field['file_id'];
                    $entityFileDAO->find( true );

                    $entityFileDAO->entity_table = $field['table_name'];
                    $entityFileDAO->entity_id    = $field['entity_id'];
                    $entityFileDAO->file_id      = $field['file_id'];
                    $entityFileDAO->save();
                    $value = $field['file_id'];
                    $type  = 'String';
                    break;

                default:
                    break;

                }
                $set[] = "{$field['column_name']} = %{$count}";
                $params[$count] = array( $value, $type );
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

    /**
     * given a field return the mysql data type associated with it
     *
     * @param string $type the civicrm type string
     *
     * @return the mysql data store placeholder
     * @access public
     * @static
     */
    public static function fieldToSQLType($type) 
    {
        switch ($type) {
        case 'String':
        case 'Link':
            return 'varchar(255)';
        case 'Boolean':
            return 'tinyint';
        case 'Int':
            return 'int';
        // the below three are FK's, and have constraints added to them
        case 'StateProvince':
        case 'Country':
        case 'File':
            return 'int unsigned';
        case 'Float':
            return 'double';
        case 'Money':
            return 'decimal(20,2)';
        case 'Memo':
            return 'text';
        case 'Date':
            return 'datetime';
        default:
            CRM_Core_Error::fatal( );
        }
    }
    
}

?>
