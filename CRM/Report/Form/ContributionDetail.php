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

    protected $_addressFields = false;

    protected $_emailField = false;

    function __construct( ) {
        $this->_columns = array( 'civicrm_contact'      =>
                                 array( 'dao'    => 'CRM_Contact_DAO_Contact',
                                        'alias'  => 'c',
                                        'fields' =>
                                        array( 'display_name' => array( 'title' => ts( 'Contact Name' ),
                                                                        'required'  => true ) ),
                                        'filters' =>             
                                        array('sort_name'    => 
                                              array( 'title'      => ts( 'Contact Name' ),
                                                     'table'      => 'civicrm_contact',
                                                     'field'      => 'sort_name',
                                                     'type'       => 'String',
                                                     'operator'   => 'like' ) ),
                                        ),
                                 
                                 'civicrm_contribution' =>
                                 array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                                        'alias'   => 'co',
                                        'fields'  =>
                                        array( 'total_amount'  => array( 'title'    => ts( 'Amount' ),
                                                                         'required' => true ),
                                               'trxn_id'       => null,
                                               'receive_date'  => null,
                                               'receipt_date'  => null,
                                               ),
                                        'filters' =>             
                                        array( 'receive_date' => 
                                               array( 'title'      => ts( 'Date Range' ),
                                                      'type'       => 'Date',
                                                      'default'    => 'this month' ),
                                               'total_amount' => 
                                               array( 'title'      => ts( 'Aggregate Total Between' ),
                                                      'type'       => 'Money' ),
                                               ),
                                        ),

                                 'civicrm_address' =>
                                 array( 'dao' => 'CRM_Core_DAO_Address',
                                        'alias'  => 'a',
                                        'fields' =>
                                        array( 'street_address'    => null,
                                               'city'              => null,
                                               'postal_code'       => null,
                                               'state_province_id' => array( 'title' => ts( 'State/Province' ) ),
                                               'country_id'        => array( 'title' => ts( 'Country' ) ),
                                               ),
                                        ),

                                 'civicrm_email' => 
                                 array( 'dao' => 'CRM_Core_DAO_Email',
                                        'alias'  => 'e',
                                        'fields' =>
                                        array( 'email' => null)
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
        $this->setDefaults( );
    }


    function select( ) {
        $select = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName=> $field ) {
                if ( CRM_Utils_Array::value( 'required', $field ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params ) ) {
                    if ( $table == 'civicrm_address' ) {
                        $this->_addressField = true;
                    } else if ( $table == 'civicrm_email' ) {
                        $this->_emailField = true;
                    }

                    $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = null;

        $this->_from = "
FROM       civicrm_contact c
INNER JOIN civicrm_contribution co ON c.id = co.contact_id
";

        if ( $this->_addressFields ) {
            $this->_from .= "LEFT JOIN civicrm_address a ON c.id = a.contact_id AND a.is_primary = 1\n";
        }
        
        if ( $this->_emailField ) {
            $this->_from .= "LEFT JOIN civicrm_email e ON c.id = e.contact_id AND e.is_primary = 1\n";
        }
    }

    function where( ) {
        $clauses = array( );
        foreach ( $this->_filters as $fieldName => $field ) {
            $field['name'] = "{$field['table']}.$fieldName";

            if ( $field['type'] == 'date' ) {
                $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                $from     = CRM_Utils_Array::value( "{$fieldName}_from", $this->_params );
                $to       = CRM_Utils_Array::value( "{$fieldName}_to", $this->_params );

                if ( $relative || $from || $to ) {
                    $clause = $this->dateClause( $field, $relative, $from, $to );
                }
            } else {
                $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                if ( $op ) {
                    $clause = $this->whereClause( $field,
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

        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }

    }


    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        $this->select( );
        $this->from  ( );
        $this->where ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where}";
        CRM_Core_Error::debug( $sql );
        exit( );
    }

}
