<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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


class CRM_Report_Utils_Get {

    static function getTypedValue( $name, $type ) {
        $value = CRM_Utils_Array::value( $name, $_GET );
        if ( $value === null ) {
            return null;
        }
        return CRM_Utils_Type::escape( $value,
                                       CRM_Utils_Type::typeToString( $type ),
                                       false );

    }

    static function dateParam( $fieldName, &$field, &$defaults ) {
    }

    static function stringParam( $fieldName, &$field, &$defaults ) {
        $fieldOP = CRM_Utils_Array::value( "{$fieldName}_op", $_GET, 'like' );

        switch ( $fieldOP ) {
        case 'has' :
        case 'sw'  :
        case 'ew'  :
        case 'nhas':
        case 'like':
        case 'neq' :
            $value = self::getTypedValue( "{$fieldName}_value", $field['type'] );
            if ( $value !== null ) {
                $defaults["{$fieldName}_value"] = $value;
                $defaults["{$fieldName}_op"   ] = $fieldOP;
            }
            break;

        }            
    }

    static function intParam( $fieldName, &$field, &$defaults ) {
        $fieldOP = CRM_Utils_Array::value( "{$fieldName}_op", $_GET, 'eq' );

        switch ( $fieldOP ) {
        case 'lte':
        case 'gte':
        case 'eq' :
        case 'lt' :
        case 'gt' :
        case 'neq':
            $value = self::getTypedValue( "{$fieldName}_value", $field['type'] );
            if ( $value !== null ) {
                $defaults["{$fieldName}_value"] = $value;
                $defaults["{$fieldName}_op"   ] = $fieldOP;
            }
            break;

        case 'bw' :
        case 'nbw':
            $minValue = self::getTypedValue( "{$fieldName}_min", $field['type'] );
            $maxValue = self::getTypedValue( "{$fieldName}_max", $field['type'] );
            if ( $minValue !== null ||
                 $maxValue !== null ) {
                $defaults["{$fieldName}_min"] = $minValue;
                $defaults["{$fieldName}_max"] = $maxValue;
                $defaults["{$fieldName}_op" ] = $fieldOP;
            }
            break;

        }
    }

    function process( &$filters, &$defaults ) {
        // process only filters for now
        foreach ( $filters as $tableName => $fields ) {
            foreach ( $fields as $fieldName => $field ) {
                switch ( $field['type'] ) {

                case CRM_Utils_Type::T_INT:
                case CRM_Utils_Type::T_MONEY:
                    self::intParam( $fieldName, $field, $defaults );
                    break;

                case CRM_Utils_Type::T_STRING:
                    self::stringParam( $fieldName, $field, $defaults );
                    break;

                case CRM_Utils_Type::T_DATE:
                case CRM_Utils_Type::T_DATE | CRM_Utils_Type::T_TIME:
                    self::dateParam( $fieldName, $field, $defaults );
                    break;
                }
            }
        }
    }


}
