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
            array( 'civicrm_contact'      =>
                   array( 'dao'     => 'CRM_Contact_DAO_Contact',
                          'fields'  =>
                          array( 'display_name' => 
                                 array( 'title' => ts( 'Contact Name' ),
                                        'required'  => true,
                                        'no_repeat' => true ),
                                 'id'           => 
                                 array( 'no_display'=> true,
                                        'required'  => true, ), ),
                          'filters' =>             
                          array('sort_name'    => 
                                array( 'title'      => ts( 'Contact Name' )  ),
                                'id'    => 
                                array( 'title'      => ts( 'Contact ID' ) ), ),
                          'grouping'=> 'contact-fields',
                          ),

                   
                   'civicrm_address' =>
                   array( 'dao'     => 'CRM_Core_DAO_Address',
                          'grouping'=> 'contact-fields',
                          'fields'  =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ),  
                                        'default' => true ), ),
                          'filters' =>             
                          array( 'country_id' => 
                                 array( 'title'   => ts( 'Country ID' ), 
                                        'type'    => CRM_Utils_Type::T_INT ), 
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province ID' ), 
                                        'type'    => CRM_Utils_Type::T_INT ), ), ),

                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'grouping'      => 'contri-fields',
                          'fields'  =>
                          array( 'total_amount'  => array( 'title'    => ts( 'Amount' ),
                                                           'required' => true,
/*                                                            'statistics'   =>  */
/*                                                            array('sum'    => ts( 'Amount' )),  */
                                                           ),
                                 'trxn_id'       => null,
                                 'receive_date'  => null,
                                 'contribution_source' => null,
                                 ),
                          'filters'       =>             
                          array('contribution_source' => null, 
                                'receive_date1'  => 
                                array( 'title'   => ts( 'Date Range One' ),
                                       'default' => 'previous.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'alias'   => 'c1',
                                       'name'    => 'receive_date' ),
                                'receive_date2'  => 
                                array( 'title'   => ts( 'Date Range Two' ),
                                       'default' => 'this.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'alias'   => 'c2',
                                       'name'    => 'receive_date' ), ), ),

                   'civicrm_contribution_type' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_ContributionType',
                          'grouping'      => 'contri-fields',
                          'fields'        =>
                          array( 'contribution_type'   => null, ), 
                          'filters'       =>
                          array( 'contribution_type'   => null, ), ),

                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group ID' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );
        
        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Repeat Contribution Detail Report' ) );
        
        parent::preProcess( );
    }

    function select( $alias = 'c1' ) {
        $select = $uni = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        
                        // do alias over-riding.
                        if ( $field['alias'] == 'contribution' ) {
                            $field['alias'] = $alias;
                        }

                        switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                        case 'YEARWEEK' :
                            $select[] = "DATE_SUB({$field['alias']}.{$field['name']}, 
INTERVAL WEEKDAY({$field['alias']}.{$field['name']}) DAY) AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), YEARWEEK({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Week';
                            break;
                            
                        case 'YEAR' :
                            $select[] = "MAKEDATE(YEAR({$field['alias']}.{$field['name']}), 1)  
AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Year';
                            break;
                            
                        case 'MONTH':
                            $select[] = "DATE_SUB({$field['alias']}.{$field['name']}, 
INTERVAL (DAYOFMONTH({$field['alias']}.{$field['name']})-1) DAY) as start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), MONTH({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Month';
                            break;
                            
                        case 'QUARTER':
                            $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['alias']}.{$field['name']} ) -2 , '/', '1', '/', YEAR( {$field['alias']}.{$field['name']} ) ), '%m/%d/%Y') AS start";
                            $uni[] = "YEAR({$field['alias']}.{$field['name']}), QUARTER({$field['alias']}.{$field['name']})";
                            $field['title'] = 'Quarter';
                            break;
                            
                        }
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys_freq'] ) ) {
                            $this->_columnHeaders["start"]['title'] = $field['title'] . ' Beginning';
                            $this->_columnHeaders["start"]['type']  = $field['type'];
                            $this->_columnHeaders["start"]['group_by'] = 
                                $this->_params['group_bys_freq'][$fieldName];

                        } else {
                            $uni[]  = "{$field['alias']}.{$field['name']}";
                        }
                    }
                }
            }

            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        // do alias over-riding.
                        if ( $field['alias'] == 'contribution' ) {
                            $field['alias'] = $alias;
                        }

                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['alias']}.{$field['name']}) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['type'] = 
                                        $field['type'];
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['alias']}.{$field['name']}) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['alias']}.{$field['name']}),2) as {$field['alias']}_{$field['name']}_{$stat}";
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['type'] =  
                                        $field['type'];
                                    $this->_columnHeaders["{$field['alias']}_{$field['name']}_{$stat}"]['title']= 
                                        $label;
                                    $this->_statFields[] = "{$field['alias']}_{$field['name']}_{$stat}";
                                    break;
                                }
                            }   
                        } else {
                            $select[] = "{$field['alias']}.{$field['name']} as {$field['alias']}_{$field['name']}";
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['title'] = $field['title'];
                        }
                    }
                }
            }
        }

        if ( count($uni) >=1 ) {
            $select[] = "CONCAT_WS('_', {$append}" . implode( ', ', $uni ) . ") AS uni";
            $this->_columnHeaders["uni"] = array('no_display' => true);
        }
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( $alias = 'c1' ) {
        $this->_from = "
FROM  civicrm_contact {$this->_aliases['civicrm_contact']}
INNER JOIN civicrm_contribution $alias 
       ON contact.id = {$alias}.contact_id
LEFT  JOIN civicrm_address address 
       ON address.contact_id = {$alias}.contact_id
LEFT  JOIN civicrm_contribution_type contribution_type 
       ON contribution_type.id = {$alias}.contribution_type_id
LEFT  JOIN civicrm_group_contact group_contact 
       ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'
LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
       ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id
";
    }

    function where( $alias = 'c1' ) {
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    // over-ride alias
                    if ( $tableName == 'civicrm_contribution' ) {
                        $field['dbAlias'] = "{$alias}.{$field['name']}";
                    }
                    $clause = null;
                    if ( ($field['type'] & CRM_Utils_Type::T_DATE) && ($field['alias'] == $alias) ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );

                        $clause = $this->dateClause( "{$field['alias']}.{$field['name']}", $relative, $from, $to );
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

    function statistics( &$rows ) {
        $statistics = array();
        
        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );
        return $statistics;
    }

    function groupBy( $alias = 'c1' ) {
    }

    function orderBy( $alias = 'c1' ) {
        $this->_orderBy = "ORDER BY contact.display_name, {$alias}.total_amount";
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );
        
        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );
        //$this->orderBy ( );
        $this->limit  ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_orderBy} {$this->_limit}";

        $rows = array( );
        $dao = CRM_Core_DAO::executeQuery( $sql );

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                $rows[$dao->contact_id][$key] = $dao->$key;
            }
        }

        $this->select  ( 'c2' );
        $this->from    ( 'c2' );
        $this->where   ( 'c2' );
        $this->groupBy ( 'c2' );
        //$this->orderBy ( 'c2' );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_orderBy} {$this->_limit}";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                if ( substr( $key, 0, 3 ) != 'c1_' ) {
                    $rows[$dao->contact_id][$key] = $dao->$key;
                }
            }
        }

        // FIXME: move following to alterDisplay()
        foreach ( $rows as $id => &$row ) {
            if ( $row['c1_total_amount'] && $row['c2_total_amount'] ) {
                $row['change'] = 
                    number_format((($row['c2_total_amount'] - $row['c1_total_amount']) * 100) / 
                                  ($row['c1_total_amount'] ), 2);
            } else if ( $row['c1_total_amount'] ) {
                $row['change'] = ts( 'Skipped Donation' );
            } else if ( $row['c2_total_amount'] ) {
                $row['change'] = ts( 'New Donor' );
            }
        }
        $this->_columnHeaders['change'] = array('title' => 'Change');

        // FIXME: doesn't go with structure
        $this->_noDisplay[] = "contact_id";
        $this->_columnHeaders['c1_total_amount'] = array('title' => 'Range One Amount');
        $this->_columnHeaders['c2_total_amount'] = array('title' => 'Range Two Amount');

        $this->formatDisplay( $rows );
        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics', $this->statistics( $rows ) );

        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows

        list($from1, $to1) = $this->getFromTo( CRM_Utils_Array::value( "receive_date1_relative", $this->_params ), 
                                               CRM_Utils_Array::value( "receive_date1_from"    , $this->_params ),
                                               CRM_Utils_Array::value( "receive_date1_to"      , $this->_params ) );
        list($from2, $to2) = $this->getFromTo( CRM_Utils_Array::value( "receive_date2_relative", $this->_params ), 
                                               CRM_Utils_Array::value( "receive_date2_from"    , $this->_params ),
                                               CRM_Utils_Array::value( "receive_date2_to"      , $this->_params ) );

        foreach ( $rows as $rowNum => $row ) {
            if ( array_key_exists('change', $row) && 
                 $row['change'] != 'Skipped Donation' && 
                 $row['change'] != 'New Donor' ) {
                $rows[$rowNum]['change'] = "{$row['change']}&nbsp;%";
            }

            // convert display name to links
            if ( array_key_exists('contact_display_name', $row) && 
                 array_key_exists('contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['contact_id'] );
                $rows[$rowNum]['contact_display_name'] = "<a href='$url'>" . 
                    $row["contact_display_name"] . '</a>';
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('address_country_id', $row) ) {
                if ( $value = $row['address_country_id'] ) {
                    $rows[$rowNum]['address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );

                    $dateUrl = ""; 
                    if ( $from1 ) {
                        $dateUrl .= "receive_date1_from={$from1}&";
                    }
                    if ( $to1 ) {
                        $dateUrl .= "receive_date1_to={$to1}&";
                    }
                    if ( $from2 ) {
                        $dateUrl .= "receive_date2_from={$from2}&";
                    }
                    if ( $to2 ) {
                        $dateUrl .= "receive_date2_to={$to2}&";
                    }
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "country_id_op=eq&country_id_value={$value}&" .
                                                  "$dateUrl"
                                                  );
                    $rows[$rowNum]['address_country_id_link'] = $url;
                }
                $entryFound = true;
            }

            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
