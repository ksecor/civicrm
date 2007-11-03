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

require_once 'CRM/Contact/Form/Search/Interface.php';

class CRM_Contact_Form_Search_Custom_Base {

    protected $_formValues;

    protected $_columns;

    function __construct( &$formValues ) {
        $this->_formValues =& $formValues;
    }

    function count( &$queryParams ) {
        return $this->sql( $queryParams,
                           'count(distinct contact_a.id) as total' );
    }

    function contactIDs( &$queryParams,
                         $offset, $rowcount, $sort ) {
        return $this->sql( $queryParams,
                           'contact_a.id as contact_id',
                           $offset, $rowcount, $sort );
    }

    function sql( &$queryParams,
                  $selectClause,
                  $offset = 0, $rowCount = 0, $sort = null,
                  $includeContactIDs = false,
                  $groupBy = null ) {

        $sql =
            "SELECT $selectClause "     .
            $this->from ( $queryParams ) .
            " WHERE "                   .
            $this->where( $queryParams, $includeContactIDs ) ;

        $this->addDomainClause( $where, $queryParams );

        if ( $includeContactIDs ) {
            $this->includeContactIDs( $where,
                                      $this->_formValues );
        }

        if ( $groupBy ) {
            $sql .= " $groupBy ";
        }
        
        $this->addSortOffset( $sql, $offset, $rowCount, $sort );
        return $sql;
    }

    function templateFile( ) {
        return null;
    }

    function &columns( ) {
        return $this->_columns;
    }


    function addDomainClause( &$sql, &$params ) {
        $max = count( $params ) + 1;
        $sql .= " AND contact_a.domain_id = %{$max}";
        $params[$max] = array( CRM_Core_Config::domainID( ),
                               'Integer' );

    }

    static function includeContactIDs( &$sql, &$formValues ) {
        $contactIDs = array( );
        foreach ( $formValues as $id => $value ) {
            if ( $value &&
                 substr( $id, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                $contactIDs[] = substr( $id, CRM_Core_Form::CB_PREFIX_LEN );
            }
        }
        
        if ( ! empty( $contactIDs ) ) {
            $contactIDs = implode( ', ', $contactIDs );
            $sql .= " AND contact_a.id IN ( $contactIDs )";
        }
    }

    static function addSortOffset( &$sql,
                                   $offset, $rowCount, $sort ) {
        if ( ! empty( $sort ) ) {
            if ( is_string( $sort ) ) {
                $sql .= " ORDER BY $sort ";
            } else {
                $sql .= " ORDER BY " . trim( $sort->orderBy() );
            }
        }
        
        if ( $row_count > 0 && $offset >= 0 ) {
            $sql .= " LIMIT $offset, $row_count ";
        }
    }

}

?>
