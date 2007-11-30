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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Search/Custom/Base.php';

class CRM_Contact_Form_Search_Custom_Contribution
   extends    CRM_Contact_Form_Search_Custom_Base
   implements CRM_Contact_Form_Search_Interface {

    function __construct( &$formValues ) {
        parent::__construct( $formValues );

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

    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        $selectClause = "
contact_a.id           as contact_id  ,
contact_a.contact_type as contact_type,
contact_a.sort_name    as sort_name,
state_province.name    as state_province,
sum( c.total_amount )  as amount
";
        return $this->sql( $selectClause,
                           $offset, $rowcount, $sort,
                           $includeContactIDs,
                           'GROUP BY contact_a.id' );
    }
    
    function from( ) {
        return "
FROM       civicrm_contact      contact_a
INNER JOIN civicrm_contribution c                ON c.contact_id       = contact_a.id
LEFT  JOIN civicrm_address address               ON ( address.contact_id = contact_a.id AND address.is_primary = 1 )
LEFT  JOIN civicrm_state_province state_province ON state_province.id  = address.state_province_id
";
    }

    function where( $includeContactIDs = false ) {

        $where = "
      c.contribution_status_id = 1
AND   c.is_test                = 0";

        $count  = 1;
        $clause = array( );
        $params = array( );
        $name   = CRM_Utils_Array::value( 'name',
                                          $this->_formValues );
        if ( $name != null ) {
            if ( strpos( $name, '%' ) === false ) {
                $name = "%{$name}%";
            }
            $params[$count] = array( $name, 'String' );
            $clause[] = "contact_a.sort_name LIKE %{$count}";
            $count++;
        }

        $state = CRM_Utils_Array::value( 'state_province_id',
                                         $this->_formValues );
        if ( $state ) {
            $params[$count] = array( $state, 'Integer' );
            $clause[] = "state_province.id = %{$count}";
            $count++;
        }
        
        $startDate = CRM_Utils_Array::value( 'start_date',
                                             $this->_formValues );
        $startDate  = CRM_Utils_Date::format( $startDate );
        if ( $startDate ) {
            $params[$count] = array( $startDate, 'Date' );
            $clause[] = "c.receive_date >= $startDate";
            $count++;
        }
        
        $endDate = CRM_Utils_Array::value( 'end_date',
                                           $this->_formValues );
        $endDate  = CRM_Utils_Date::format( $endDate );
        if ( $endDate ) {
            $endDate .= '235959';
            $params[$count] = array( $endDate, 'Date' );
            $clause[] = "c.receive_date <= $endDate";
            $count++;
        }

        if ( ! empty( $clause ) ) {
            $where .= ' AND ' . implode( ' AND ', $clause );
        }

        return $this->whereClause( $where, $params );
    }

}

?>
