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

class CRM_Report_Form_Contribute_Detail extends CRM_Report_Form {
    protected $_addressField = false;

    protected $_emailField   = false;

    protected $_summary      = null;

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
                                 array( 'no_display' => true,
                                        'required'  => true, ), ),
                          'filters' =>             
                          array('sort_name'    => 
                                array( 'title'      => ts( 'Contact Name' ),
                                       'operator'   => 'like' ),
                                'id'    => 
                                array( 'title'      => ts( 'Contact ID' ),
                                       'no_display' => true ), ),
                          'grouping'=> 'contact-fields',
                          ),
 
                   'civicrm_email'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'fields'    =>
                          array( 'email' => 
                                 array( 'title'      => ts( 'Email' ),
                                        'default'    => true,
                                        'no_repeat'  => true
                                       ),  ),
                          'grouping'      => 'contact-fields',
                          ),

                   'civicrm_phone'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Phone',
                          'fields'    =>
                          array( 'phone' => 
                                 array( 'title'      => ts( 'Phone' ),
                                        'default'    => true,
                                        'no_repeat'  => true
                                        ), ),
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
                                 array( 'title'   => ts( 'Country' ),  
                                        'default' => true ), ),
                          'grouping'=> 'contact-fields',
                          'filters' =>             
                          array( 'country_id' => 
                                 array( 'title'        => ts( 'Country' ), 
                                        'type'         => CRM_Utils_Type::T_INT,
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options'      => CRM_Core_PseudoConstant::country( ),), 
                                 'state_province_id' => 
                                 array( 'title'        => ts( 'State/Province' ), 
                                        'type'         => CRM_Utils_Type::T_INT,
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options'      => CRM_Core_PseudoConstant::stateProvince( ),), ),
                          ),

                   'civicrm_contribution' =>
                   array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                          'fields'  =>
                          array( 'total_amount'  => array( 'title'    => ts( 'Amount' ),
                                                           'required' => true,
                                                           'statistics'   => 
                                                           array('sum'    => ts( 'Amount' )), ),
                                 'trxn_id'       => null,
                                 'receive_date'  => array( 'default' => true ),
                                 'receipt_date'  => null,
                                 ),
                          'filters' =>             
                          array( 'receive_date' => 
                                 array( 'operatorType' => CRM_Report_Form::OP_DATE ),
                                 'total_amount' => 
                                 array( 'title'      => ts( 'Amount Between' ) ),
                                 ),
                          'grouping'=> 'contri-fields',
                          ),
                   
                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'id',
                                        'title'   => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options'      => CRM_Core_PseudoConstant::staticGroup( ) ), ), ),

                   'civicrm_contribution_ordinality' => 
                   array( 'dao'    => 'CRM_Contribute_DAO_Contribution',
                          'alias'  => 'cordinality',
                          'filters' =>             
                          array( 'ordinality' => 
                                 array( 'title'   => ts( 'Contribution Ordinality' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options'      => array( 0 => 'First by Contributor', 
                                                                 1 => 'Second or Later by Contributor') ), ), ),
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
          );
        }
        
        parent::__construct( );
    }

    function preProcess( ) {
        parent::preProcess( );
    }

    function select( ) {
        $select = array( );

        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( $tableName == 'civicrm_address' ) {
                            $this->_addressField = true;
                        } else if ( $tableName == 'civicrm_email' ) {
                            $this->_emailField = true;
                        }
                        
                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = 
                                        $field['type'];
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'count':
                                    $select[] = "COUNT({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                case 'avg':
                                    $select[] = "ROUND(AVG({$field['dbAlias']}),2) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  =  
                                        $field['type'];
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                }
                            }   
                            
                        } else {
                            $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value( 'type', $field );
                        }
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = null;

        $this->_from = "
        FROM  civicrm_contact      {$this->_aliases['civicrm_contact']}
              INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} 
                      ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id
              INNER JOIN (SELECT c.id, IF(COUNT(oc.id) = 0, 0, 1) AS ordinality FROM civicrm_contribution c LEFT JOIN civicrm_contribution oc ON c.contact_id = oc.contact_id AND oc.receive_date < c.receive_date GROUP BY c.id) {$this->_aliases['civicrm_contribution_ordinality']} 
                      ON {$this->_aliases['civicrm_contribution_ordinality']}.id = {$this->_aliases['civicrm_contribution']}.id
               LEFT JOIN  civicrm_phone {$this->_aliases['civicrm_phone']} 
                      ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                         {$this->_aliases['civicrm_phone']}.is_primary = 1)
              LEFT  JOIN civicrm_group_contact group_contact 
                      ON {$this->_aliases['civicrm_contact']}.id = group_contact.contact_id  AND 
                         group_contact.status='Added'
              LEFT  JOIN civicrm_group {$this->_aliases['civicrm_group']} 
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
        
        if ( $this->_emailField ) {
            $this->_from .= " 
            LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND 
                      {$this->_aliases['civicrm_email']}.is_primary = 1\n";
        }

    }


    function groupBy( ) {
        $this->_groupBy = " GROUP BY contact.id, contribution.id ";
    }

    function orderBy( ) {
        $this->_orderBy = " ORDER BY contact.id ";
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
            $statistics['counts']['amount']    = array( 'value' => $dao->amount,
                                                        'title' => 'Total Amount',
                                                        'type'  => CRM_Utils_Type::T_MONEY );
            $statistics['counts']['avg']       = array( 'value' => $dao->avg,
                                                        'title' => 'Average',
                                                        'type'  => CRM_Utils_Type::T_MONEY );
        }

        return $statistics;
    }

    function postProcess( ) {
        parent::postProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $checkList  = array();
        $entryFound = false;
        $display_flag = $prev_cid = $cid =  0;
        
        foreach ( $rows as $rowNum => $row ) {
            if ( !empty($this->_noRepeats) ) {
                // don't repeat contact details if its same as the previous row
                if ( array_key_exists('civicrm_contact_id', $row ) ) {
                    if ( $cid =  $row['civicrm_contact_id'] ) {
                        if ( $rowNum == 0 ) {
                            $prev_cid = $cid;
                        } else {
                            if( $prev_cid == $cid ) {
                                $display_flag = 1;
                                $prev_cid = $cid;
                            } else {
                                $display_flag = 0;
                                $prev_cid = $cid;
                            }
                        }
                        
                        if ( $display_flag ) {
                            foreach ( $row as $colName => $colVal ) {
                                if ( in_array($colName, $this->_noRepeats) ) {
                                    unset($rows[$rowNum][$colName]);          
                                }
                            }
                        }
                        $entryFound = true;
                    }
                }
            }
            
            // handle state province
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvince( $value, false );

                    $url = CRM_Report_Utils_Report::getNextUrl( 'contribute/detail',
                                                                "reset=1&force=1&" . 
                                                                "state_province_id_op=in&state_province_id_value={$value}",
                                                                $this->_absoluteUrl, $this->_id );
                    $rows[$rowNum]['civicrm_address_state_province_id_link' ] = $url;
                    $rows[$rowNum]['civicrm_address_state_province_id_hover'] =
                        ts("List all contribution(s) for this State.");
                }
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value, false );

                    $url = CRM_Report_Utils_Report::getNextUrl( 'contribute/detail',
                                                                "reset=1&force=1&" . 
                                                                "country_id_op=in&country_id_value={$value}",
                                                                $this->_absoluteUrl, $this->_id );
                    $rows[$rowNum]['civicrm_address_country_id_link' ] = $url;
                    $rows[$rowNum]['civicrm_address_country_id_hover'] = 
                        ts("List all contribution(s) for this Country.");
                }
                
                $entryFound = true;
            }

            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 CRM_Utils_Array::value( 'civicrm_contact_display_name', $rows[$rowNum] ) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( "civicrm/contact/view"  , 
                                              'reset=1&cid=' . $row['civicrm_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_display_name_hover'] =  
                    ts("View Contact Summary for this Contact.");
            }

            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
            $lastKey = $rowNum;
        }
    }

}
