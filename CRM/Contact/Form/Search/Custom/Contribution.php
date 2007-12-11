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

require_once 'CRM/Contact/Form/Search/Interface.php';

class CRM_Contact_Form_Search_Custom_Contribution
   implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    function __construct( &$formValues ) {     
        $this->_formValues = $formValues;

        $this->_columns = array( ts('Contact Id')   => 'contact_id'  ,
                                 ts('Name'      )   => 'sort_name',
                                 ts('Donation Count') => 'donation_count',
                                 ts('Donation Amount') => 'donation_amount' );
    }

    function buildForm( &$form ) {
        $form->add( 'text',
                    'min_amount',
                    ts( 'Min Amount' ) );
        $form->add( 'text',
                    'max_amount',
                    ts( 'Max Amount' ) );

        $form->add( 'date',
                    'start_date',
                    ts('Start Date'),
                    CRM_Core_SelectValues::date('custom', 10, 3 ) );
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');

        $form->add( 'date',
                    'end_date',
                    ts('End Date'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('end_date', ts('Select a valid date.'), 'qfDate');

        $tag = array('' => ts('- any tag -')) + CRM_Core_PseudoConstant::tag( );
        $form->add('select', 'tag', ts('Tagged'), $tag);

        $form->assign( 'elements', array( 'min_amount', 'max_amount', 'start_date', 'end_date', 'tag' ) );
    }

    function count( ) {
        $sql = $this->all( );

        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray );
        return $dao->N;
    }

    function alphabet( ) {
    }


    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) { 
    }
    
    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        $where = $this->where( );
        if ( ! empty( $where ) ) {
            $where = " AND $where";
        }

        $having = $this->having( );
        if ( $having ) {
            $having = " HAVING $having ";
        }

        $sql = "
SELECT distinct(contact.id) as contact_id,
       contact.sort_name as sort_name,
       sum(contrib.total_amount) AS donation_amount,
       count(contrib.id) AS donation_count
FROM civicrm_contribution AS contrib,
civicrm_contact AS contact
WHERE contrib.contact_id = contact.id
AND contrib.is_test = 0 
$where
GROUP BY contact.id
$having
ORDER BY donation_amount desc";

        return $sql;
    }

    function from( ) {
        return '';
    }

    function where( $includeContactIDs = false ) {
        $clauses = array( );

        $startDate = CRM_Utils_Date::format( $this->_formValues['start_date'] );
        if ( $startDate ) {
            $clauses[] = "contrib.receive_date >= $startDate";
        }

        $endDate = CRM_Utils_Date::format( $this->_formValues['end_date'] );
        if ( $endDate ) {
            $clauses[] = "contrib.receive_date <= $endDate";
        }

        return implode( ' AND ', $clauses );
    }

    function having( $includeContactIDs = false ) {
        $clauses = array( );
        $min = CRM_Utils_Array::value( 'min_amount', $this->_formValues );
        if ( $min ) {
            $clauses[] = "sum(contrib.total_amount) >= $min";
        }

        $max = CRM_Utils_Array::value( 'max_amount', $this->_formValues );
        if ( $max ) {
            $clauses[] = "sum(contrib.total_amount) <= $max";
        }

        return implode( ' AND ', $clauses );
    }

    function &columns( ) {
        return $this->_columns;
    }

    function templateFile( ) {
        return 'CRM/Contact/Form/Search/Custom/Contribution.tpl';
    }

}

?>
