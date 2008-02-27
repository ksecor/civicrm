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

require_once 'CRM/Contact/Form/Search/Interface.php';

class CRM_Contact_Form_Search_Custom_Base {

    protected $_formValues;

    protected $_columns;

    function __construct( &$formValues ) {
        $this->_formValues =& $formValues;
    }

    function count( ) {
        return CRM_Core_DAO::singleValueQuery( $this->sql( 'count(distinct contact_a.id) as total' ),
                                               CRM_Core_DAO::$_nullArray );
    }
    
    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) {
        $sql    = $this->sql( 'contact_a.id as contact_id',
                              $offset, $rowcount, $sort );
        $this->validateUserSQL( $sql );

        $dao = new CRM_Core_DAO( );
        return CRM_Core_DAO::composeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray,
                                           true, $dao );
    }

    function sql( $selectClause,
                  $offset = 0, $rowCount = 0, $sort = null,
                  $includeContactIDs = false,
                  $groupBy = null ) {

        $sql =
            "SELECT $selectClause "     .
            $this->from ( )             .
            " WHERE "                   .
            $this->where( )             ;

        $this->addDomainClause( $where );

        if ( $includeContactIDs ) {
            $this->includeContactIDs( $sql,
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

    function addDomainClause( &$sql ) {
        $sql .=
            " AND contact_a.domain_id = " .
            CRM_Core_Config::domainID( );
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

    function addSortOffset( &$sql,
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

    function validateUserSQL( &$sql, $onlyWhere = false ) {
        $includeStrings = array( 'contact_a', 'contact_a.domain_id = ' );
        $excludeStrings = array( 'insert', 'delete', 'update' );

        if ( ! $onlyWhere ) {
            $includeStrings += array( 'select', 'from', 'where', 'civicrm_contact' );
        }

        foreach ( $includeStrings as $string ) {
            if ( stripos( $sql, $string ) === false ) {
                CRM_Core_Error::fatal( ts( 'Could not find \'%1\' string in SQL clause.',
                                           array( 1 => $string ) ) );
            }
        }

        foreach ( $excludeStrings as $string ) {
            if ( stripos( $sql, $string ) !== false ) {
                CRM_Core_Error::fatal( ts( 'Found illegal \'%1\' string in SQL clause.',
                                           array( 1 => $string ) ) );
            }
        }
    }

    function whereClause( &$where, &$params ) {
        $dao = new CRM_Core_DAO( );
        $where = CRM_Core_DAO::composeQuery( $where, $params, true, $dao );
        $this->addDomainClause( $where );
        $this->validateUserSQL( $where, true );

        return $where;
    }

}

?>
