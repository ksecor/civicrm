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

class CRM_Report_Form_Contribute_RepeatDetail extends CRM_Report_Form {

    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'filters'       =>             
                          array( 
                                'receive_date_r1'  => 
                                array( 'title'   => ts( 'Date Range One' ),
                                       'default' => 'previous.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'dbAlias' => 'contribution1.receive_date' ),
                                'receive_date_r2'  => 
                                array( 'title'   => ts( 'Date Range Two' ),
                                       'default' => 'this.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'dbAlias' => 'contribution2.receive_date' ), ), ),
                   );
        
/*         $this->_options =  */
/*             array(  */
/*                   'group_bys_country' => array( 'title'   => ts( 'Group Contacts by Country' ), */
/*                                                 'type'    => 'checkbox', */
/*                                                 'default' => true ),  */
/*                   'is_repeat'         => array( 'title'   => ts( 'Show contacts who have donated in both ranges only' ), */
/*                                                 'type'    => 'checkbox', */
/*                                                 'default' => true ),  */
/*                    ); */

        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contribution Repeat Detail Report' ) );
        
        parent::preProcess( );
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );
        
        $r1_relative = CRM_Utils_Array::value( "receive_date_r1_relative", $this->_params );
        $r1_from     = CRM_Utils_Array::value( "receive_date_r1_from"    , $this->_params );
        $r1_to       = CRM_Utils_Array::value( "receive_date_r1_to"      , $this->_params );

        $c1_clause = $this->dateClause( 'c1.receive_date', $r1_relative, $r1_from, $r1_to );

        $r2_relative = CRM_Utils_Array::value( "receive_date_r2_relative", $this->_params );
        $r2_from     = CRM_Utils_Array::value( "receive_date_r2_from"    , $this->_params );
        $r2_to       = CRM_Utils_Array::value( "receive_date_r2_to"      , $this->_params );

        $c2_clause = $this->dateClause( 'c2.receive_date', $r2_relative, $r2_from, $r2_to );

        if ( $this->_params['is_repeat'] ) {
            $whereOP = 'AND';
        } else {
            $whereOP = 'OR';
        }

        $sql = "
SELECT    c.id, c.display_name,
          sum(c1.total_amount) as c1_amount
FROM      civicrm_contact c, civicrm_contribution c1
WHERE     c1.contact_id = c.id
AND       $c1_clause
GROUP BY c.display_name ASC WITH ROLLUP
";

        $rows = array( );

        $dao = CRM_Core_DAO::executeQuery( $sql );
        while ( $dao->fetch( ) ) {
            $rows[$dao->id] = array( 'cid'         => $dao->id,
                                    'display_name' => $dao->display_name,
                                    'c1_amount'    => $dao->c1_amount   ,
                                    'c2_amount'    => null
                                    );
        }


        $sql = "
SELECT    c.id, c.display_name,
          sum(c2.total_amount) as c2_amount
FROM      civicrm_contact c, civicrm_contribution c2
WHERE     c2.contact_id = c.id
AND       $c2_clause
GROUP BY c.display_name ASC WITH ROLLUP
";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        while ( $dao->fetch( ) ){
            if ( isset( $rows[$dao->id] ) ) {
                $rows[$dao->id]['c2_amount'] = $dao->c2_amount;
            } else {
                $rows[$dao->id] = array( 'cid'          => $dao->id,
                                         'display_name' => $dao->display_name,
                                         'c1_amount'    => null,
                                         'c2_amount'    => $dao->c2_amount
                                         );
            }
        }

        $c1_clause = $this->dateDisplay( $r1_relative, $r1_from, $r1_to );
        $c2_clause = $this->dateDisplay( $r2_relative, $r2_from, $r2_to );
        $this->_columnHeaders = 
            array( 'display_name'=> array('title' => 'Contact'),
                   'c1_amount'   => array('title' => "Amount ($c1_clause)"),
                   'c2_amount'   => array('title' => "Amount ($c2_clause)"),
                   'change'      => array('title' => 'Change'),
                   );

        foreach ( $rows as $id => &$row ) {
            if ( $row['c1_amount'] && $row['c2_amount'] ) {
                $row['change'] = number_format( ( ( $row['c2_amount'] - $row['c1_amount'] ) * 100 ) / ( $row['c1_amount'] ),
                                                2 );
            } else if ( $row['c1_amount'] ) {
                $row['change'] = ts( 'Skipped Donation' );
            } else if ( $row['c2_amount'] ) {
                $row['change'] = ts( 'New Donor' );
            }
        }

        $this->formatDisplay( $rows );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        foreach ( $rows as $cid => $field ) {
            if ( array_key_exists('change', $field) ) {
                $rows[$cid]['change'] = "{$field['change']}&nbsp;%";
            }
        }

        // do operation on the last row
        foreach ( $rows[$cid] as $fld => $val ) {
            if ( $fld == 'display_name' ) {
                unset($rows[$cid]['display_name']);
            } else {
                $rows[$cid][$fld] = "<strong>$val</strong>";
            }
        }
    }
}


