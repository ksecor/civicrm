<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

class CRM_Report_Form_Contribute_Summary extends CRM_Report_Form {
    protected $_addressField = false;

    protected $_charts = array( ''         => 'Tabular',
                                'barGraph' => 'Bar Graph',
                                'pieGraph' => 'Pie Graph'
                                );
    
    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'display_name'      => 
                                 array( 'title'      => ts( 'Contact Name' ),
                                        'no_repeat'  => true ),
                                 'id'           => 
                                 array( 'no_display' => true,
                                        'required'  => true, ), ),
                          'grouping'  => 'contact-fields',
                          'group_bys' => 
                          array( 'id'                =>
                                 array( 'title'      => ts( 'Contact ID' ) ),
                                 'display_name'      => 
                                 array( 'title'      => ts( 'Contact Name' ), ), ),
                          ),
                   
                   'civicrm_email'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'fields'    =>
                          array( 'email' => 
                                 array( 'title'      => ts( 'Email' ),
                                        'no_repeat'  => true ),  ),
                          'grouping'      => 'contact-fields',
                          ),
                   
                   'civicrm_phone'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Phone',
                          'fields'    =>
                          array( 'phone' => 
                                 array( 'title'      => ts( 'Phone' ),
                                        'no_repeat'  => true ), ),
                          'grouping'      => 'contact-fields',
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'fields' =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ) ), ),
                          'group_bys' =>
                          array( 'street_address'    => null,
                                 'city'              => null,
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ), ), ),
                          'grouping'=> 'contact-fields',
                          'filters' =>             
                          array( 'country_id' => 
                                 array( 'title'         => ts( 'Country' ), 
                                        'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                        'options'       => CRM_Core_PseudoConstant::country(null, false),), 
                                 'state_province_id' => 
                                 array( 'title'         => ts( 'State/Province' ), 
                                        'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                        'options'       => CRM_Core_PseudoConstant::stateProvince( ),), ),
                          ),

                   'civicrm_contribution_type' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_ContributionType',
                          'fields'        =>
                          array( 'contribution_type'   => null, ), 
                          'grouping'      => 'contri-fields',
                          'group_bys'     =>
                          array( 'contribution_type'   => null, ), ),

                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          //'bao'           => 'CRM_Contribute_BAO_Contribution',
                          'fields'        =>
                          array( 'contribution_source' => null, 
                                 'total_amount'        => 
                                 array( 'title'        => ts( 'Amount Statistics' ),
                                        'default'      => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              'avg'    => ts( 'Average' ), ), ), ),
                          'grouping'              => 'contri-fields',
                          'filters'               =>             
                          array( 'receive_date'   => 
                                 array( 'operatorType' => CRM_Report_Form::OP_DATE ),
                                 'total_amount'   => 
                                 array( 'title'   => ts( 'Total  Amount' ), ), ),
                          'group_bys'           =>
                          array( 'receive_date' => 
                                 array( 'frequency'  => true,
                                        'default'    => true,
                                        'chart'      => true ),
                                 'contribution_source'     => null, ), ),

                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'          => 'id',
                                        'title'         => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options'       => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
                   );

        // Add contribution custom fields
        $query = 'SELECT id, table_name FROM civicrm_custom_group WHERE is_active = 1 AND extends = "Contribution"';
        $dao = CRM_Core_DAO::executeQuery( $query );
        while ( $dao->fetch( ) ) {
        
          // Assemble the fields for this custom data group
          $fields = array();
          $query = 'SELECT column_name, label FROM civicrm_custom_field WHERE is_active = 1 AND custom_group_id = ' . $dao->id;
          $dao_column = CRM_Core_DAO::executeQuery( $query );
          while ( $dao_column->fetch( ) ) {
            $fields[$dao_column->column_name] = array(
              'title' => $dao_column->label,
            );
          }
          
          // Add the custom data table and fields to the report column options
          $this->_columns[$dao->table_name] = array(
            'dao' => 'CRM_Contribute_DAO_Contribution',
            'fields' => $fields,
            'group_bys' => $fields,
          );
        }


        $this->_options = array( 'include_grand_total' => array( 'title'  => ts( 'Include Grand Totals' ),
                                                                 'type'   => 'checkbox',
                                                                 'default'=> true ),
                                 );
        parent::__construct( );
    }

    function preProcess( ) {
        parent::preProcess( );
    }
    
    function setDefaultValues( $freeze = true ) {
        return parent::setDefaultValues( $freeze );
    }

    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( $tableName == 'civicrm_address' ) {
                        $this->_addressField = true;
                    }
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        switch ( $this->_params['group_bys_freq'][$fieldName] ) {
                        case 'YEARWEEK' :
                            $select[] = "DATE_SUB({$field['dbAlias']}, INTERVAL WEEKDAY({$field['dbAlias']}) DAY) AS {$tableName}_{$fieldName}_start";
                            $select[] = "YEARWEEK({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "WEEKOFYEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Week';
                            break;
                            
                        case 'YEAR' :
                            $select[] = "MAKEDATE(YEAR({$field['dbAlias']}), 1)  AS {$tableName}_{$fieldName}_start";
                            $select[] = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "YEAR({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Year';
                            break;
                            
                        case 'MONTH':
                            $select[] = "DATE_SUB({$field['dbAlias']}, INTERVAL (DAYOFMONTH({$field['dbAlias']})-1) DAY) as {$tableName}_{$fieldName}_start";
                            $select[] = "MONTH({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "MONTHNAME({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Month';
                            break;
                            
                        case 'QUARTER':
                            $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$field['dbAlias']} ) -2 , '/', '1', '/', YEAR( {$field['dbAlias']} ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                            $select[] = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_subtotal";
                            $select[] = "QUARTER({$field['dbAlias']}) AS {$tableName}_{$fieldName}_interval";
                            $field['title'] = 'Quarter';
                            break;
                            
                        }
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys_freq'] ) ) {
                            $this->_interval = $field['title'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['title'] = 
                                $field['title'] . ' Beginning';
                            $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['type']  = 
                                $field['type'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}_start"]['group_by'] = 
                                $this->_params['group_bys_freq'][$fieldName];

                            // just to make sure these values are transfered to rows.
                            // since we need that for calculation purpose, 
                            // e.g making subtotals look nicer or graphs
                            $this->_columnHeaders["{$tableName}_{$fieldName}_interval"] = array('no_display' => true);
                            $this->_columnHeaders["{$tableName}_{$fieldName}_subtotal"] = array('no_display' => true);
                        }
                    }
                }
            }

            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( $tableName == 'civicrm_address' ) {
                        $this->_addressField = true;
                    }
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = $field['type'];
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] = CRM_Utils_Type::T_INT;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type'] =  $field['type'];
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                }
                            }   
                            
                        } else {
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        }
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    static function formRule( &$fields, &$files, $self ) {  
        $errors = $grouping = array( );
        //check for searching combination of dispaly columns and
        //grouping criteria
        if ( $fields['group_bys']['receive_date'] ) {
            foreach ( $self->_columns as $tableName => $table ) {
                if ( array_key_exists('fields', $table) ) {
                    foreach ( $table['fields'] as $fieldName => $field ) {
                        if ( $fields['fields'][$field['name']] && 
                             in_array( $field['name'], array( 'display_name', 'contribution_source', 'contribution_type' ) ) ) {
                            $grouping[] = $field['title'];
                        }
                    }
                }
            }
            if ( !empty( $grouping ) ) {
                $temp = 'and '. implode(', ', $grouping );
                $errors['fields'] = ts("Please Do not use combination of received date %1", array( 1 => $temp ));    
            }
        }
         
        if ( !$fields['group_bys']['receive_date'] ) {
            if ( CRM_Utils_Date::isDate( $fields['receive_date_from'] ) || CRM_Utils_Date::isDate( $fields['receive_date_to'] ) ) {
                $errors['receive_date_relative'] = ts("Do not use filter on Date if group by received date not used ");      
            }
        }
        return $errors;
    }

    function from( ) {
        $this->_from = "
        FROM civicrm_contact  {$this->_aliases['civicrm_contact']}
             INNER JOIN civicrm_contribution   {$this->_aliases['civicrm_contribution']} 
                     ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id
             LEFT  JOIN civicrm_contribution_type  {$this->_aliases['civicrm_contribution_type']} 
                     ON {$this->_aliases['civicrm_contribution']}.contribution_type_id ={$this->_aliases['civicrm_contribution_type']}.id
             LEFT  JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                     ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND 
                        {$this->_aliases['civicrm_email']}.is_primary = 1) 
              
             LEFT  JOIN civicrm_phone {$this->_aliases['civicrm_phone']} 
                     ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                        {$this->_aliases['civicrm_phone']}.is_primary = 1)
              
             LEFT  JOIN civicrm_group_contact  group_contact 
                     ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND group_contact.status='Added'
             LEFT  JOIN civicrm_group  {$this->_aliases['civicrm_group']} 
                     ON group_contact.group_id = {$this->_aliases['civicrm_group']}.id
            ";

        // LEFT JOIN on contribution custom data fields
        $query = 'SELECT id, table_name FROM civicrm_custom_group WHERE is_active = 1 AND extends = "Contribution"';
        $dao = CRM_Core_DAO::executeQuery( $query );
        while ( $dao->fetch( ) ) {
          $alias = $this->_aliases[$dao->table_name];
          $this->_from .= "\n" . 'LEFT JOIN ' . $dao->table_name . ' ' . $alias;
          $this->_from .= "\n" . '        ON ' . $alias . '.entity_id = ' . $this->_aliases['civicrm_contribution'] . '.id';
        }

        if ( $this->_addressField ) {
            $this->_from .= "
            LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                      {$this->_aliases['civicrm_address']}.is_primary = 1\n";
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
                        
                        if ( $relative || $from || $to ) {
                            $clause = $this->dateClause( $field['name'], $relative, $from, $to );
                        }
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

    function groupBy( ) {
        $this->_groupBy = "";
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            if ( CRM_Utils_Array::value( 'chart', $field ) ) {
                                $this->assign( 'displayChart', true );
                            }

                            if ( CRM_Utils_Array::value('frequency', $table['group_bys'][$fieldName]) && 
                                 CRM_Utils_Array::value($fieldName, $this->_params['group_bys_freq']) ) {
                                
                                $append = "YEAR({$field['dbAlias']}),";
                                if ( in_array(strtolower($this->_params['group_bys_freq'][$fieldName]), 
                                              array('year')) ) {
                                    $append = '';
                                }
                                $this->_groupBy[] = "$append {$this->_params['group_bys_freq'][$fieldName]}({$field['dbAlias']})";
                                $append = true;
                            } else {
                                $this->_groupBy[] = $field['dbAlias'];
                            }
                        }
                    }
                }
            }
            
            if ( !empty($this->_statFields) && 
                 CRM_Utils_Array::value( 'include_grand_total', $this->_params['options'] ) && 
                 (( $append && count($this->_groupBy) <= 1 ) || (!$append)) ) {
                $this->_rollup = " WITH ROLLUP";
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " {$this->_rollup} ";
        } else {
            $this->_groupBy = "GROUP BY contact.id";
        }
    }

    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );
        
        $select = "
        SELECT COUNT( contribution.total_amount ) as count,
               SUM(   contribution.total_amount ) as amount,
               ROUND(AVG(contribution.total_amount), 2) as avg
        ";
        
        $sql = "{$select} {$this->_from} {$this->_where}";
        $dao = CRM_Core_DAO::executeQuery( $sql );
        
        if ( $dao->fetch( ) ) {
            $statistics['counts']['amount'] = array( 'value' => $dao->amount,
                                                     'title' => 'Total Amount' );
            $statistics['counts']['count '] = array( 'value' => $dao->count,
                                                     'title' => 'Total Counts' );
            $statistics['counts']['avg   '] = array( 'value' => $dao->avg,
                                                     'title' => 'Average');
        }
        
        return $statistics;
    }
    
    function postProcess( ) {
        parent::postProcess( );
    }
    
    function buildChart( &$rows ) {
        $graphRows = array();
        $count = 0;

        if ( CRM_Utils_Array::value('charts', $this->_params ) ) {
            foreach ( $rows as $key => $row ) {
                if ( $row['civicrm_contribution_receive_date_subtotal'] ) {
                    $graphRows['receive_date'][]   = $row['civicrm_contribution_receive_date_start'];
                    $graphRows[$this->_interval][] = $row['civicrm_contribution_receive_date_interval'];
                    $graphRows['value'][]          = $row['civicrm_contribution_total_amount_sum'];
                    $count++;
                }
            }
            
            if ( CRM_Utils_Array::value( 'receive_date', $this->_params['group_bys'] ) ) {
                foreach ( array ( 'receive_date', $this->_interval, 'value' ) as $ignore ) {
                    unset( $graphRows[$ignore][$count-1] );
                }
                $graphs = CRM_Utils_PChart::chart( $graphRows, $this->_params['charts'], $this->_interval );
                $this->assign( 'graphFilePath', $graphs['0']['file_name'] );
                $this->_graphPath =  $graphs['0']['file_name'];
            }
        }
    }


    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $hoverText  = ts("Lists detailed contribution(s) for this record.");
        $entryFound = false;

        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            if ( CRM_Utils_Array::value('receive_date', $this->_params['group_bys']) && 
                 CRM_Utils_Array::value('civicrm_contribution_receive_date_start', $row) &&
                 $row['civicrm_contribution_receive_date_start'] && 
                 $row['civicrm_contribution_receive_date_subtotal'] ) {

                $dateStart = CRM_Utils_Date::customFormat($row['civicrm_contribution_receive_date_start'], 
                                                          '%Y%m%d');
                $dateEnd   = CRM_Utils_Date::unformat($dateStart, '');

                switch(strtolower($this->_params['group_bys_freq']['receive_date'])) {
                case 'month': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M']+1, 
                                                    $dateEnd['d']-1, $dateEnd['Y']));
                    break;
                case 'year': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M'], 
                                                    $dateEnd['d']-1, $dateEnd['Y']+1));
                    break;
                case 'yearweek': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M'], 
                                                    $dateEnd['d']+6, $dateEnd['Y']));
                    break;
                case 'quarter': 
                    $dateEnd   = date("Ymd", mktime(0, 0, 0, $dateEnd['M']+3, 
                                                    $dateEnd['d']-1, $dateEnd['Y']));
                    break;
                }
                $url =
                    CRM_Utils_System::url( 'civicrm/report/contribute/detail',
                                           "reset=1&force=1&receive_date_from={$dateStart}&receive_date_to={$dateEnd}",
                                           $this->_absoluteUrl
                                           );
                $rows[$rowNum]['civicrm_contribution_receive_date_start_link'] = $url;
                $entryFound = true;
            }

            // make subtotals look nicer
            if ( array_key_exists('civicrm_contribution_receive_date_subtotal', $row) && 
                 !$row['civicrm_contribution_receive_date_subtotal'] ) {
                $this->fixSubTotalDisplay( $rows[$rowNum], $this->_statFields );
                $entryFound = true;
            }

            // handle state province
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value, false );

                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/summary',
                                                  "reset=1&force=1&" . 
                                                  "state_province_id_op=in&state_province_id_value={$value}", 
                                                  $this->_absoluteUrl );
                    $rows[$rowNum]['civicrm_address_state_province_id_link'] = $url;
                }
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/summary',
                                                  "reset=1&force=1&" . 
                                                  "country_id_op=in&country_id_value={$value}",
                                                  $this->_absoluteUrl );
                    $rows[$rowNum]['civicrm_address_country_id_link'] = $url;
                }
                
                $entryFound = true;
            }
            
            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
                                              $this->_absoluteUrl );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a title='{$hoverText}' href='$url'>" . 
                    $row["civicrm_contact_display_name"] . '</a>';
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
