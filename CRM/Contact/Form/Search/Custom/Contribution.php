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
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Contact_Form_Search_Custom_Contribution implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    protected $_columns;

    function __construct( &$formValues ) {
        $this->_formValues =& $formValues;

        $this->_columns = array( ts('Contact Id')   => 'contact_id'    ,
                                 ts('Contact Type') => 'contact_type'  ,
                                 ts('Name')         => 'sort_name'     ,
                                 ts('State')        => 'state_province',
                                 ts('Total Amount') => 'amount'          );
    }

    function buildForm( &$form ) {
        $form->add( 'text',
                    'name',
                    ts( 'Name' ),
                    true );

        $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
        $form->addElement('select', 'state_province_id', ts('State/Province'), $stateProvince);

        $form->add('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('manual', 20, 1), false );
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $form->add('date', 'end_date', ts('End Date'), CRM_Core_SelectValues::date('manual', 20, 1), false );
        $form->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        /**
         * if you are using the standard template, this array tells the template what elements
         * are part of the search criteria
         */
        $form->assign( 'elements', array( 'name', 'state_province_id', 'start_date', 'end_date' ) );
    }

    function count( &$queryParams ) {
        return $this->sql( $queryParams,
                           'count(distinct contact_a.id) as total' );
    }

    function contactIDs( &$queryParams,
                         $offset, $rowcount, $sort ) {
        $selectClause = "
contact_a.id           as contact_id
";
        return $this->sql( $queryParams,
                           $selectClause,
                           $offset, $rowcount, $sort );

    }

    function all( &$queryParams,
                  $offset, $rowcount, $sort,
                  $includeContactIDs = false ) {
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name,
state_province.name    as state_province,
sum( c.total_amount )  as amount
";
        $sql  = $this->sql( $queryParams,
                            $selectClause,
                            $offset, $rowcount, $sort,
                            $includeContactIDs,
                            'GROUP BY contact_a.id' );
        return $sql;

    }
    
    function from( &$queryParams ) {
        return "
FROM       civicrm_contact      contact_a
INNER JOIN civicrm_contribution c                ON c.contact_id       = contact_a.id
LEFT  JOIN civicrm_address address               ON ( address.contact_id = contact_a.id AND address.is_primary = 1 )
LEFT  JOIN civicrm_state_province state_province ON state_province.id  = address.state_province_id
";
    }

    function where( &$queryParams,
                    $includeContactIDs = false ) {
        $where = "
      c.contribution_status_id = 1
AND   c.is_test                = 0";

        $count  = 1;
        $clause = array( );
        $name   = CRM_Utils_Array::value( 'name',
                                          $this->_formValues );
        if ( $name != null ) {
            if ( strpos( $name, '%' ) === false ) {
                $name = "%{$name}%";
            }
            $queryParams[$count] = array( $name, 'String' );
            $clause[] = "contact_a.sort_name LIKE %{$count}";
            $count++;
        }

        $state = CRM_Utils_Array::value( 'state_province_id',
                                         $this->_formValues );
        if ( $state ) {
            $queryParams[$count] = array( $state, 'Integer' );
            $clause[] = "state_province.id = %{$count}";
            $count++;
        }
        
        $startDate = CRM_Utils_Array::value( 'start_date',
                                             $this->_formValues );
        $startDate  = CRM_Utils_Date::format( $startDate );
        if ( $startDate ) {
            $queryParams[$count] = array( $startDate, 'Date' );
            $clause[] = "c.receive_date >= $startDate";
            $count++;
        }
        
        $endDate = CRM_Utils_Array::value( 'end_date',
                                           $this->_formValues );
        $endDate  = CRM_Utils_Date::format( $endDate );
        if ( $endDate ) {
            $endDate .= '235959';
            $queryParams[$count] = array( $endDate, 'Date' );
            $clause[] = "c.receive_date <= $endDate";
            $count++;
        }

        if ( ! empty( $clause ) ) {
            $where .= ' AND ' . implode( ' AND ', $clause );
        }


        require_once 'CRM/Contact/BAO/SearchCustom.php';
        if ( $includeContactIDs ) {
            CRM_Contact_BAO_SearchCustom::includeContactIDs( $where,
                                                             $this->_formValues );
        }

        CRM_Contact_BAO_SearchCustom::addDomainClause( $where, $queryParams );
        return $where;
    }

    function sql( &$queryParams,
                  $selectClause,
                  $offset = 0, $rowCount = 0, $sort = null,
                  $includeContactIDs = false,
                  $groupBy = null ) {

        $sql =
            "SELECT $selectClause "     .
            self::from ( $queryParams ) .
            " WHERE "                   .
            self::where( $queryParams, $includeContactIDs ) ;

        if ( $groupBy ) {
            $sql .= " $groupBy ";
        }
        
        require_once 'CRM/Contact/BAO/SearchCustom.php';
        CRM_Contact_BAO_SearchCustom::addSortOffset( $sql, $offset, $rowCount, $sort );

        return $sql;
    }

    function templateFile( ) {
        return null;
    }

    function &columns( ) {
        return $this->_columns;
    }

}

?>
