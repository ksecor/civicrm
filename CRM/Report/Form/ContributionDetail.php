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

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_ContributionDetail extends CRM_Report_Form {

    protected $_addressField = false;

    protected $_emailField = false;

    protected $_summary = null;

    function __construct( ) {
        $this->_columns = array( 'civicrm_contact'      =>
                                 array( 'dao'     => 'CRM_Contact_DAO_Contact',
                                        'fields'  =>
                                        array( 'display_name' => array( 'title' => ts( 'Contact Name' ),
                                                                        'required'  => true ) ),
                                        'filters' =>             
                                        array('sort_name'    => 
                                              array( 'title'      => ts( 'Contact Name' ),
                                                     'operator'   => 'like' ) ),
                                        'grouping'=> 'contact-fields',
                                        ),
                                 
                                 'civicrm_contribution' =>
                                 array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                                        'fields'  =>
                                        array( 'total_amount'  => array( 'title'    => ts( 'Amount' ),
                                                                         'required' => true ),
                                               'trxn_id'       => null,
                                               'receive_date'  => null,
                                               'receipt_date'  => null,
                                               ),
                                        'filters' =>             
                                        array( 'receive_date' => 
                                               array( 'default'    => 'this month' ),
                                               'total_amount' => 
                                               array( 'title'      => ts( 'Aggregate Total Between' ) ),
                                               ),
                                        'grouping'=> 'contri-fields',
                                        ),

                                 'civicrm_address' =>
                                 array( 'dao' => 'CRM_Core_DAO_Address',
                                        'fields' =>
                                        array( 'street_address'    => null,
                                               'city'              => null,
                                               'postal_code'       => null,
                                               'state_province_id' => array( 'title' => ts( 'State/Province' ) ),
                                               'country_id'        => array( 'title' => ts( 'Country' ) ),
                                               ),
                                        'grouping'=> 'contact-fields',
                                        ),

                                 'civicrm_email' => 
                                 array( 'dao' => 'CRM_Core_DAO_Email',
                                        'fields' =>
                                        array( 'email' => null),
                                        'grouping'=> 'contact-fields',
                                        ),
                                 );

        $this->_options = array( 'include_statistics' => array( 'title' => ts( 'Include Contribution Statistics' ),
                                                                'type'  => 'checkbox' ),
                                 );
        
        parent::__construct( );
    }

    function preProcess( ) {
        parent::preProcess( );
    }

    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function select( ) {
        $select = array( );

        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( 'required', $field ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$table['grouping']] ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$tableName] ) ) {
                    if ( $tableName == 'civicrm_address' ) {
                        $this->_addressField = true;
                    } else if ( $tableName == 'civicrm_email' ) {
                        $this->_emailField = true;
                    }

                    $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}"] = $field['title'];
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = null;

        $this->_from = "
FROM       civicrm_contact {$this->_aliases['civicrm_contact']}
INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id
";

        if ( $this->_addressField ) {
            $this->_from .= "LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        }
        
        if ( $this->_emailField ) {
            $this->_from .= "LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND {$this->_aliases['civicrm_email']}.is_primary = 1\n";
        }
    }

    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        $clause = $this->dateClause( $field, $relative, $from, $to );
                    } else {
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        if ( $op ) {
                            $clause = 
                                $this->whereClause( $field,
                                                    $op,
                                                    CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
                        }
                    }
                    
                    if ( ! empty( $clause ) ) {
                        $clauses[] = $clause;
                    }
                }
            }
        }

        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }


    function statistics( ) {
        $select = "
SELECT COUNT( contribution.total_amount ) as count,
       SUM(   contribution.total_amount ) as amount,
       AVG(   contribution.total_amount ) as avg
";

        $sql = "{$select} {$this->_from} {$this->_where}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        $statistics = null;
        if ( $dao->fetch( ) ) {
            $statistics = array( 'count'  => array( 'value' => $dao->count,
                                                    'title' => 'Count' ),
                                 'amount' => array( 'value' => $dao->amount,
                                                    'title' => 'Total Amount' ),
                                 'avg'    => array( 'value' => $dao->avg,
                                                    'title' => 'Average' ),
                                 );
        }
        
        return $statistics;
    }

    function postProcess( ) {
        if ( $this->_id &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        } else {
            $this->_params = $this->controller->exportValues( $this->_name );
        }

        $this->select( );
        $this->from  ( );
        $this->where ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where}";

        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            $rows[] = $row;
        }

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );

        if ( CRM_Utils_Array::value( 'include_statistics', $this->_params['options'] ) ) {
            $this->assign( 'statistics',
                           $this->statistics( ) );
        }

        parent::postProcess( );
    }

}
