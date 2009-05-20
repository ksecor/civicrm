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

class CRM_Report_Form_Contribute_RepeatSummary extends CRM_Report_Form {

    protected $_summary = null;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'fields' =>
                          array( 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ),
                                        'default' => true  ), 
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ) ), ),
                          'group_bys' =>
                          array( 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ),
                                        'default' => true ), 
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 ),
                          ),

                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'total_amount'        => 
                                 array( 'title'        => ts( 'Amount Statistics' ),
                                        'default'      => true,
                                        //'required'     => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              //'avg'    => ts( 'Average' ), 
                                              ), ), 
                                 'contribution_source' => null ),
                          'grouping'      => 'contri-fields',
                          'filters'       =>             
                          array( 
                                'receive_date1'  => 
                                array( 'title'   => ts( 'Date Range One' ),
                                       'default' => 'previous.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'name'    => 'receive_date',
                                       'alias'   => 'c1' ),
                                'receive_date2'  => 
                                array( 'title'   => ts( 'Date Range Two' ),
                                       'default' => 'this.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'name'    => 'receive_date',
                                       'alias'   => 'c2' ), ),
                          'group_bys'           =>
                          array( 'contribution_source' => null, 
                                 'receive_date' => 
                                 array( 'frequency'  => true ), ), ),

                   'civicrm_contribution_type' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_ContributionType',
                          'fields'        =>
                          array( 'contribution_type'   => null, ), 
                          'grouping'      => 'contri-fields',
                          'group_bys'     =>
                          array( 'contribution_type'   => null, ), ),

                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'type'    => CRM_Utils_Type::T_INT + CRM_Utils_Type::T_ENUM,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );

        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Repeat Contribution Summary Report' ) );
        
        parent::preProcess( );
    }
    
    function setDefaultValues( ) {
        return parent::setDefaultValues( );
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

    function groupBy( $alias = 'c1' ) {
        $this->_groupBy = "";
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {

                            // do alias over-riding.
                            if ( $field['alias'] == 'contribution' ) {
                                $field['alias'] = $alias;
                            }

                            if ( CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) && 
                                 CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq']) ) {
                                
                                $append = "YEAR({$field['alias']}.{$field['name']}),";
                                if ( in_array(strtolower($this->_params['group_bys_freq'][$fieldName]), 
                                              array('year')) ) {
                                    $append = '';
                                }
                                $this->_groupBy[] = "$append {$this->_params['group_bys_freq'][$fieldName]}({$field['alias']}.{$field['name']})";
                                $append = true;
                            } else {
                                $this->_groupBy[] = "{$field['alias']}.{$field['name']}";
                            }
                        }
                    }
                }
            }
            
            $rollUP = "";
            if ( !empty($this->_statFields) && 
                 CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) && 
                 ( $append && count($this->_groupBy) <= 1 ) ) {
                $rollUP = " WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " $rollUP ";
        } else {
            $this->_groupBy = "GROUP BY $alias.contact_id";
        }
    }

    function from( $alias = 'c1' ) {
        $this->_from = "
FROM civicrm_contribution $alias 
LEFT JOIN civicrm_address            address 
      ON address.contact_id = {$alias}.contact_id
LEFT JOIN civicrm_contribution_type  contribution_type 
      ON contribution_type.id = {$alias}.contribution_type_id
LEFT  JOIN civicrm_group_contact     group_contact 
       ON {$alias}.contact_id = group_contact.contact_id  AND group_contact.status='Added'
LEFT  JOIN civicrm_group             {$this->_aliases['civicrm_group']} 
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
        
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            $combinations[] = $field['title'];
                        }
                    }
                }
            }
            $statistics[] = array( 'title' => ts('Grouping(s)'),
                                   'value' => implode( ' & ', $combinations ) );
        }
        
        return $statistics;
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params;

        $this->processReportMode( );

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";

        $dao = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                $rows[$dao->uni][$key] = $dao->$key;
            }
        }

        $this->select  ( 'c2' );
        $this->from    ( 'c2' );
        $this->where   ( 'c2' );
        $this->groupBy ( 'c2' );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        // hack to not allow create two copies/columns
        foreach ( $this->_columnHeaders as $key => $value ) {
            if ( (substr( $key, 0, 3 ) == 'c2_') && 
                 (! in_array(substr( $key, 3 ), 
                             array('total_amount_sum',
                                   'total_amount_count',
                                   'total_amount_sum')) ) ) {
                unset($this->_columnHeaders[$key]);
            }
        }

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                if ( substr( $key, 0, 3 ) != 'c1_' ) {
                    $rows[$dao->uni][$key] = $dao->$key;
                }
            }
        }

        // hack to fix title
        list($from1, $to1) = $this->getFromTo( CRM_Utils_Array::value( "receive_date1_relative", $this->_params ), 
                                               CRM_Utils_Array::value( "receive_date1_from"    , $this->_params ),
                                               CRM_Utils_Array::value( "receive_date1_to"      , $this->_params ) );
        $from1 = CRM_Utils_Date::customFormat( $from1, null, array('d') );
        $to1   = CRM_Utils_Date::customFormat( $to1,   null, array('d') );

        list($from2, $to2) = $this->getFromTo( CRM_Utils_Array::value( "receive_date2_relative", $this->_params ), 
                                               CRM_Utils_Array::value( "receive_date2_from"    , $this->_params ),
                                               CRM_Utils_Array::value( "receive_date2_to"      , $this->_params ) );
        $from2 = CRM_Utils_Date::customFormat( $from2, null, array('d') );
        $to2   = CRM_Utils_Date::customFormat( $to2,   null, array('d') );

        $this->_columnHeaders['c1_total_amount_sum']['title']   = "$from1 -<br/> $to1";
        $this->_columnHeaders['c1_total_amount_sum']['colspan'] = 2;
        $this->_columnHeaders['c2_total_amount_sum']['title']   = "$from2 -<br/> $to2";
        $this->_columnHeaders['c2_total_amount_sum']['colspan'] = 2;

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

        foreach ( $rows as $rowNum => $row ) {
            // handle country
            if ( array_key_exists('address_country_id', $row) ) {
                if ( $value = $row['address_country_id'] ) {
                    $rows[$rowNum]['address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );
                    
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "country_id_op=eq&country_id_value={$value}&" .
                                                  "$dateUrl"
                                                  );
                    $rows[$rowNum]['address_country_id_link'] = $url;
                }
                $entryFound = true;
            }

            // handle state province
            if ( array_key_exists('address_state_province_id', $row) ) {
                if ( $value = $row['address_state_province_id'] ) {
                    $rows[$rowNum]['address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value, false );

                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "state_province_id_op=eq&state_province_id_value={$value}&" .
                                                  "$dateUrl"
                                                  );
                    $rows[$rowNum]['address_state_province_id_link'] = $url;
                }
                $entryFound = true;
            }
            
            // link contribution type
            if ( array_key_exists('contribution_type_name', $row) ) {
                if ( $value = $row['contribution_type_name'] ) {
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "contribution_type_op=has&contribution_type_value={$value}&" .
                                                  "$dateUrl"
                                                  );
                    $rows[$rowNum]['contribution_type_name_link'] = $url;
                }
                $entryFound = true;
            }
        } // foreach ends
    }
}
