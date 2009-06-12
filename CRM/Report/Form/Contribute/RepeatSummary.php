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

class CRM_Report_Form_Contribute_RepeatSummary extends CRM_Report_Form {

    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'  =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'grouping'  => 'contact-fields',
                          'fields'    =>
                          array( 'display_name'      => 
                                 array( 'title'      => ts( 'Contact Name' ),
                                        'no_repeat'  => true,
                                        'default'    => true ),
                                 'id'           => 
                                 array( 'no_display' => true,
                                        'required'   => true, 
                                       ), ), 
                          'group_bys' => 
                          array( 'id'                =>
                                 array( 'title'      => ts( 'Contact' ),
                                        'default'    => true ), ),
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
                   array( 'dao'       => 'CRM_Core_DAO_Address',
                          'grouping'  => 'contact-fields',
                          'fields' =>
                          array( 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ) ), 
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ) ), ),
                          'group_bys' =>
                          array( 'country_id'        => 
                                 array( 'title'   => ts( 'Country' ) ), 
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 ),
                          ),

                   'civicrm_contribution_type' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_ContributionType',
                          'fields'        =>
                          array( 'contribution_type'   => null, ), 
                          'grouping'      => 'contri-fields',
                          'group_bys'     =>
                          array( 'contribution_type'   => 
                                 array('name' => 'id'), ), ),

                   'civicrm_contribution' =>
                   array( 'dao'           => 'CRM_Contribute_DAO_Contribution',
                          'fields'        =>
                          array( 'contribution_source' => null, 
                                 'total_amount1'       => 
                                 array( 'name'         => 'total_amount',
                                        'alias'        => 'contribution1',
                                        'title'        => ts( 'Range One Stat' ),
                                        'default'      => true,
                                        'required'     => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              //'avg'    => ts( 'Average' ), 
                                              ), 
                                        'clause'       => '
contribution1_total_amount_count, contribution1_total_amount_sum',
                                        ), 
                                 'total_amount2'        => 
                                 array( 'name'         => 'total_amount',
                                        'alias'        => 'contribution2',
                                        'title'        => ts( 'Range Two Stat' ),
                                        'default'      => true,
                                        'required'     => true,
                                        'statistics'   => 
                                        array('sum'    => ts( 'Total Amount' ), 
                                              'count'  => ts( 'Count' ), 
                                              //'avg'    => ts( 'Average' ), 
                                              ), 
                                        'clause'       => '
contribution2_total_amount_count, contribution2_total_amount_sum',
                                        ), 
                                 ),
                          'grouping'      => 'contri-fields',
                          'filters'       =>             
                          array( 
                                'receive_date1'  => 
                                array( 'title'   => ts( 'Date Range One' ),
                                       'default' => 'previous.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'operatorType' => CRM_Report_Form::OP_DATE,
                                       'name'    => 'receive_date',
                                       'alias'   => 'contribution1' ),
                                'receive_date2'  => 
                                array( 'title'   => ts( 'Date Range Two' ),
                                       'default' => 'this.year',
                                       'type'    => CRM_Utils_Type::T_DATE,
                                       'operatorType' => CRM_Report_Form::OP_DATE,
                                       'name'    => 'receive_date',
                                       'alias'   => 'contribution2' ), 
                                'total_amount1'  => 
                                array( 'title'   => ts( 'Range One Amount' ),
                                       'type'    => CRM_Utils_Type::T_INT,
                                       'operatorType' => CRM_Report_Form::OP_INT,
                                       'name'    => 'receive_date',
                                       'dbAlias' => 'contribution1_total_amount_sum' ),
                                'total_amount2'  => 
                                array( 'title'   => ts( 'Range Two Amount' ),
                                       'type'    => CRM_Utils_Type::T_INT,
                                       'operatorType' => CRM_Report_Form::OP_INT,
                                       'name'    => 'receive_date',
                                       'dbAlias' => 'contribution2_total_amount_sum' ),
                                 ),
                          'group_bys'           =>
                          array( 'contribution_source' => null ), ),

                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),
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
        $select = $uni = array( );

        // since contact fields not related to contribution type
        if ( array_key_exists('contribution_type', $this->_params['group_bys']) ||
             array_key_exists('contribution_source', $this->_params['group_bys']) ) {
            unset($this->_columns['civicrm_contact']['fields']['id']);
        }

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        $uni[]  = "{$field['dbAlias']}";
                    }
                }
            }

            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( isset($field['clause']) ) {
                            $select[] = $field['clause'];

                            // FIXME: dirty hack for setting columnHeaders
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}_sum"]['type']    = 
                                $field['type'];
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}_sum"]['title']   = 
                                $field['title'];
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}_count"]['type']  = 
                                $field['type'];
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}_count"]['title'] = 
                                $field['title'];
                            continue;
                        }

                        // only include statistics columns if set
                        $select[] = "{$field['dbAlias']} as {$field['alias']}_{$field['name']}";
                        $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['type'] = $field['type'];
                        $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['title'] = $field['title'];
                        if ( $field['no_display'] ) {
                            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['no_display'] = true;
                        }
                    }
                }
            }
        }

        if ( count($uni) >= 1 ) {
            $select[] = "CONCAT_WS('_', {$append}" . implode( ', ', $uni ) . ") AS uni";
            $this->_columnHeaders["uni"] = array('no_display' => true);
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function groupBy( $tableCol = false ) {
        $this->_groupBy = "";
        if ( is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            if ( $tableCol ) {
                                return array($tableName, $field['alias'], $field['name']);
                            } else {
                                $this->_groupBy[] = "{$field['dbAlias']}";
                            }
                        }
                    }
                }
            }
            
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " $rollUP ";
        }
    }

    function from( ) {
        foreach ( array( 'receive_date1', 'receive_date2' ) as $fieldName ) {
            $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
            $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
            $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );

            $$fieldName = 
                $this->dateClause( $this->_columns['civicrm_contribution']['filters'][$fieldName]['dbAlias'], 
                                   $relative, $from, $to );
        }

        list($fromTable, $fromAlias, $fromCol) = $this->groupBy( true );
        $from = "$fromTable $fromAlias";

        if ( $fromTable == 'civicrm_contact' ) {
            $contriCol  = "contact_id";
            $from .= "
LEFT JOIN civicrm_address address ON contact.id = address.contact_id
LEFT JOIN civicrm_email  email    ON contact.id = email.contact_id
LEFT JOIN civicrm_phone  phone    ON contact.id = phone.contact_id
";
        } else if ( $fromTable == 'civicrm_contribution_type' ) {
            $contriCol  = "contribution_type_id";
        } else if ( $fromTable == 'civicrm_contribution' ) {
            $contriCol  = $fromCol;
        } else if ( $fromTable == 'civicrm_address' ) {
            $from .= "
INNER JOIN civicrm_contact contact ON address.contact_id = contact.id";
            $fromAlias = "contact";
            $fromCol   = "id";
            $contriCol = "contact_id";
        }

        $this->_from = "
FROM $from

LEFT  JOIN (
   SELECT contribution1.$contriCol, 
          sum( contribution1.total_amount ) AS contribution1_total_amount_sum, 
          count( * ) AS contribution1_total_amount_count
   FROM   civicrm_contribution {$this->_aliases['civicrm_contribution']}1
   WHERE  ( $receive_date1 )
   GROUP BY contribution1.$contriCol
) contribution1 ON $fromAlias.$fromCol = contribution1.$contriCol

LEFT  JOIN (
   SELECT contribution2.$contriCol, 
          sum( contribution2.total_amount ) AS contribution2_total_amount_sum, 
          count( * ) AS contribution2_total_amount_count
   FROM   civicrm_contribution {$this->_aliases['civicrm_contribution']}2
   WHERE  ( $receive_date2 )
   GROUP BY contribution2.$contriCol
) contribution2 ON $fromAlias.$fromCol = contribution2.$contriCol
";
    }

    function where( ) {
        $clauses[] = "!(contribution1_total_amount_count IS NULL AND contribution2_total_amount_count IS NULL)";

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( !($field['type'] & CRM_Utils_Type::T_DATE) ) {
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

    function formRule ( &$fields, &$files, $self ) {
        require_once 'CRM/Utils/Date.php';
        
        $errors = $checkDate = $errorCount = array( );
        
        $col_fields     = $fields['fields'];
        $grp_fields     = $fields['group_bys'];
        $num_col_fields = count($col_fields);
        $num_grp_fields = count($grp_fields);
        
        if ( $num_grp_fields == 0 ) {
            $errors['fields'] = "You have to select at least one Group by field.";
        } else {
            if ( ($grp_fields['id'] || $grp_fields['country_id'] || $grp_fields['state_province_id'] ) ) {
                if( !$grp_fields['id'] && ($col_fields['phone'] || $col_fields['email'] ) ) {
                    $errors['fields'] = "Group by Contact should be selected for Email or/and 
                                         Phone field(s)";
                }
                if ( $col_fields['contribution_source'] || $col_fields['contribution_type'] ) {
                    $errors['fields'] = "You can not select 'Contribution source' or/and 
                                        'Contribution type' field(s) with Group by 
                                        Contact or/and Address ";
                }
            }
            
            if( $grp_fields['contribution_type'] ) {
                if ( (!array_key_exists('contribution_type', $col_fields) && $num_col_fields > 2 )
                     || (array_key_exists('contribution_type', $col_fields) && $num_col_fields > 3 ) ) {
                    $errors['fields'] = "Should select only 'Contribution type' field with 
                                         Group by Contribution type";
                } 
                if( $num_grp_fields > 1 ) {
                    $errors['fields'] = "You can not use other Group  by with 
                                         Contribution type or Contribution source ";
                } 
            }
            
            if( $grp_fields['contribution_source'] ) {
                if ( (!array_key_exists('contribution_source', $col_fields) && $num_col_fields > 2 )
                     ||(array_key_exists('contribution_source', $col_fields) && $num_col_fields > 3 ) ) {
                    $errors['fields'] = "Should select only 'Contribution source' field with 
                                        Group by Contribution source";
                } 
                if( $num_grp_fields > 1 ) {
                    $errors['fields'] = "You can not use other Group  by with 
                                     Contribution type or Contribution source ";
                } 
            }
        }
            
        if ( $fields['receive_date1_relative'] == '0' ) {
            $checkDate['receive_date1']['receive_date1_from'] = $fields['receive_date1_from'];
            $checkDate['receive_date1']['receive_date1_to'  ] = $fields['receive_date1_to'];
        } 
        
        if ( $fields['receive_date2_relative'] == '0' ) {
            $checkDate['receive_date2']['receive_date2_from'] = $fields['receive_date2_from'];
            $checkDate['receive_date2']['receive_date2_to'  ] = $fields['receive_date2_to'];
        }

        foreach ( $checkDate as $date_range => $range_data ) {
            foreach ( $range_data as $key => $value ) {
                
                if ( CRM_Utils_Date::isDate( $value ) ) {
                    $errorCount[$date_range][$key]['valid'   ] = 'true';
                    $errorCount[$date_range][$key]['is_empty'] = 'false';
                } else {
                    $errorCount[$date_range][$key]['valid'   ] ='false';
                    $errorCount[$date_range][$key]['is_empty'] = 'true';
                    foreach ( $value as $v ) {
                        if ( $v ) {
                            $errorCount[$date_range][$key]['is_empty'] = 'false';
                        }
                    }
                }
            }
        }

        foreach ( $errorCount as $date_range => $error_data) {
            
            if ( ( $error_data[$date_range.'_from']['valid'] == 'false' ) && 
                 ( $error_data[$date_range.'_to']['valid'] == 'false') ) {
                
                if ( ( $error_data[$date_range.'_from']['is_empty'] == 'true' ) && 
                     ( $error_data[$date_range.'_to']['is_empty'] == 'true') ) {
                    $errors[$date_range.'_relative'] ="select valid date range";
                }
                
                if ( $error_data[$date_range.'_from']['is_empty'] == 'false' ) {
                    $errors[$date_range.'_from'] = "Select valid 'from' for ".str_replace('_',' ',$date_range);
                }

                if ( $error_data[$date_range.'_to']['is_empty'] == 'false' ){
                    $errors[$date_range.'_to'] = "Select valid 'to' for ".str_replace('_',' ',$date_range);
                }
                
            } elseif ( ( $error_data[$date_range.'_from']['valid'] == 'true' ) && 
                       ( $error_data[$date_range.'_to']['valid'] == 'false') ) {
                if ( $error_data[$date_range.'_to']['is_empty'] == 'false' ){
                    $errors[$date_range.'_to'] = "Select valid 'to' for ".str_replace('_',' ',$date_range);
                }
                
            } elseif ( ( $error_data[$date_range.'_from']['valid'] == 'false' ) && 
                       ( $error_data[$date_range.'_to']['valid'] == 'true' ) ) {
                if ( $error_data[$date_range.'_from']['is_empty'] == 'false' ){
                    $errors[$date_range.'_from'] = "Select valid 'from' for ".str_replace('_',' ',$date_range);
                }
            }
        }

        return $errors;
    }   
    

    function postProcess( ) {
        $this->beginPostProcess( );

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );
        $this->limit   ( );

        $sql  = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_limit}";
        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            foreach ( $this->_columnHeaders as $key => $value ) {
                $rows[$dao->uni][$key] = $dao->$key;
            }
        }

        // FIXME: calculate % using query
        foreach ( $rows as $uid => $row ) {
            if ( $row['contribution1_total_amount_sum'] && $row['contribution2_total_amount_sum'] ) {
                $rows[$uid]['change'] =
                    number_format((($row['contribution2_total_amount_sum'] - 
                                    $row['contribution1_total_amount_sum']) * 100) /
                                  ($row['contribution1_total_amount_sum'] ), 2);
            } else if ( $row['contribution1_total_amount_sum'] ) {
                $rows[$uid]['change'] = ts( 'Skipped Donation' );
            } else if ( $row['contribution2_total_amount_sum'] ) {
                $rows[$uid]['change'] = ts( 'New Donor' );
            }
            if ( $row['contribution1_total_amount_count'] ) {
                $rows[$uid]['contribution1_total_amount_sum'] =
                    $row['contribution1_total_amount_sum'] . " ({$row['contribution1_total_amount_count']})";
            }
            if ( $row['contribution2_total_amount_count'] ) {
                $rows[$uid]['contribution2_total_amount_sum'] =
                    $row['contribution2_total_amount_sum'] . " ({$row['contribution2_total_amount_count']})";
            }
        }
        $this->_columnHeaders['change'] = array('title' => 'Change');

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
        
        $this->_columnHeaders['contribution1_total_amount_sum']['title']   = "$from1 -<br/> $to1";
        $this->_columnHeaders['contribution2_total_amount_sum']['title']   = "$from2 -<br/> $to2";
        unset($this->_columnHeaders['contribution1_total_amount_count'],
              $this->_columnHeaders['contribution2_total_amount_count']);

        $this->formatDisplay( $rows );
        
        // assign variables to templates
        $this->doTemplateAssignment( $rows );

        $this->endPostProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $hoverCountry     = ts("View repeatDetails for this Country.");
        $hoverState       = ts("View repeatDetails for this state.");
        $hoverContriType  = ts("View repeatDetails for this Contribution type.");
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
                    $contryName = CRM_Core_PseudoConstant::country( $value, false );
                    
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "country_id_op=in&country_id_value={$value}&" .
                                                  "$dateUrl",
                                                  $this->_absoluteUrl
                                                  );
		                                      
                    $rows[$rowNum]['address_country_id']="<a title='{$hoverCountry }' href='{$url}'>".$contryName."</a>";
                }
                $entryFound = true;
            }

            // handle state province
            if ( array_key_exists('address_state_province_id', $row) ) {
                if ( $value = $row['address_state_province_id'] ) {
                    $stateName = 
                        CRM_Core_PseudoConstant::stateProvinceAbbreviation( $value, false );

                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "state_province_id_op=in&state_province_id_value={$value}&" .
                                                  "$dateUrl",
                                                  $this->_absoluteUrl
                                                  );
                    $rows[$rowNum]['address_state_province_id'] ="<a title='{$hoverState}' href='{$url}'>".$stateName."</a>";
                }
                $entryFound = true;
            }
            
            // link contribution type
            if ( array_key_exists('contribution_type_name', $row) ) {
                if ( $value = $row['contribution_type_name'] ) {
                    $url = CRM_Utils_System::url( 'civicrm/report/contribute/repeatDetail',
                                                  "reset=1&force=1&" . 
                                                  "contribution_type_op=has&contribution_type_value={$value}&" .
                                                  "$dateUrl",
                                                  $this->_absoluteUrl
                                                  );
                    $rows[$rowNum]['contribution_type_name'] ="<a title='{$hoverContriType}' href='{$url}' >".$value."</a>";
                }
                $entryFound = true;
            }

            // convert display name to links
            if ( array_key_exists('contact_display_name', $row) && 
                 array_key_exists('contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/report/contribute/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['contact_id'],
                                              $this->_absoluteUrl );
                $rows[$rowNum]['contact_display_name'] = "<a href='$url'>" . 
                    $row["contact_display_name"] . '</a>';
                $entryFound = true;
            }
        } // foreach ends
    }
}
