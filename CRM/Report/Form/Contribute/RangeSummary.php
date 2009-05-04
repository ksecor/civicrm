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

class CRM_Report_Form_Contribute_RangeSummary extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'total_amount1'        => 
                                 array( 'title'        => ts( 'Range1' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), ), 
                                        'dbAlias'      => 'contribution1.total_amount' ),
                                 'total_amount2'        => 
                                 array( 'title'        => ts( 'Range2' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), ), 
                                        'dbAlias'      => 'contribution2.total_amount' ),
                                 ),
                          'filters'               =>             
                          array( 
                                 'receive_date1'  => 
                                 array( 'title'   => ts( 'Date Range1' ),
                                        'default' => 'previous.year',
                                        'type'    => 12,
                                        'dbAlias' => 'contribution1.receive_date' ),
                                 'receive_date2'  => 
                                 array( 'title'   => ts( 'Date Range2' ),
                                        'default' => 'this.year',
                                        'type'    => 12,
                                        'dbAlias' => 'contribution2.receive_date' ),
                                 'total_amount'   => 
                                 array( 'title'   => ts( 'Total  Amount Between' ), ), ), ),
                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'group_bys'           =>
                          array( 'country_id'   => 
                                 array( 'default'    => true,
                                        'title'      => ts( 'Country' ) ), ), ),
                   );

        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Contribution Range Summary Report' ) );
        
        parent::preProcess( );
    }
    
    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );

        $r1_relative = CRM_Utils_Array::value( "receive_date1_relative", $this->_params );
        $r1_from     = CRM_Utils_Array::value( "receive_date1_from"    , $this->_params );
        $r1_to       = CRM_Utils_Array::value( "receive_date1_to"      , $this->_params );

        $c1_clause = $this->dateClause( 'c1.receive_date', $r1_relative, $r1_from, $r1_to );

        $r2_relative = CRM_Utils_Array::value( "receive_date2_relative", $this->_params );
        $r2_from     = CRM_Utils_Array::value( "receive_date2_from"    , $this->_params );
        $r2_to       = CRM_Utils_Array::value( "receive_date2_to"      , $this->_params );

        $c2_clause = $this->dateClause( 'c2.receive_date', $r2_relative, $r2_from, $r2_to );

        $sql = "
SELECT 
ifnull(country.name, 'N/A') as country_name,
ifnull( SUM(c1_count), 0 ) AS c1_count, ifnull( SUM(c1_amount), 0 ) AS c1_amount,
ifnull( SUM(c2_count), 0 ) AS c2_count, ifnull( SUM(c2_amount), 0 ) AS c2_amount

FROM civicrm_contact c
LEFT JOIN (
 SELECT c1.contact_id, count( * ) AS c1_count, sum( c1.total_amount ) AS c1_amount
 FROM civicrm_contribution c1
 LEFT JOIN civicrm_address ad ON ad.contact_id=c1.contact_id 
 WHERE $c1_clause
 GROUP BY ad.country_id
) c1 ON c.id = c1.contact_id

LEFT JOIN (
 SELECT c2.contact_id, count( * ) AS c2_count, sum( c2.total_amount ) AS c2_amount
 FROM civicrm_contribution c2
 LEFT JOIN civicrm_address ad ON ad.contact_id=c2.contact_id 
 WHERE $c2_clause
 GROUP BY ad.country_id
) c2 ON c.id = c2.contact_id

LEFT JOIN civicrm_address ad ON c.id=ad.contact_id
LEFT JOIN civicrm_country country ON ad.country_id=country.id

WHERE !(c1_count IS NULL AND c2_count IS NULL)
GROUP BY ad.country_id WITH ROLLUP
";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            $rows[] = array( 'country_name' => $dao->country_name,
                             'c1_count'     => $dao->c1_count ,
                             'c1_amount'    => $dao->c1_amount,
                             'c2_count'     => $dao->c2_count,
                             'c2_amount'    => $dao->c2_amount,
                             );
        }
        $this->_columnHeaders = 
            array( 'country_name'=> array('title' => 'Country'),
                   'c1_count'    => array('title' => 'Range1 Count'),
                   'c1_amount'   => array('title' => 'Range1 Amount'),
                   'c2_count'    => array('title' => 'Range2 Count'),
                   'c2_amount'   => array('title' => 'Range2 Amount'),
                   );

        $this->formatDisplay( $rows );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        
        foreach ( $rows[count($rows)-1] as $fld => $val ) {
            if ( $fld == 'country_name' ) {
                unset($rows[count($rows)-1]['country_name']);
            } else {
                $rows[count($rows)-1][$fld] = "<strong>$val</strong>";
            }
        }
    }
}
