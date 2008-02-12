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
                    ts( 'Aggregate Total Between $' ) );
        $form->add( 'text',
                    'max_amount',
                    ts( '...and $' ) );

        $form->add( 'date',
                    'start_date',
                    ts('Contribution Date From'),
                    CRM_Core_SelectValues::date('custom', 10, 3 ) );
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');

        $form->add( 'date',
                    'end_date',
                    ts('...through'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('end_date', ts('Select a valid date.'), 'qfDate');

        /**
         * You can define a custom title for the search form
         */
        $this->setTitle('Find Contributors by Aggregate Totals');
        
        /**
         * if you are using the standard template, this array tells the template what elements
         * are part of the search criteria
         */
        $form->assign( 'elements', array( 'min_amount', 'max_amount', 'start_date', 'end_date') );
    }

    function count( ) {
        $sql = $this->all( );

        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray );
        return $dao->N;
    }

    

    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) { 
        return $this->all( $offset, $rowcount, $sort );
    }
    
    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        $select  = "
distinct(contact.id) as contact_id,
contact.sort_name as sort_name,
sum(contrib.total_amount) AS donation_amount,
count(contrib.id) AS donation_count
";

        $where = $this->where( $includeContactIDs );
        if ( ! empty( $where ) ) {
            $where = " AND $where";
        }

        $having = $this->having( );
        if ( $having ) {
            $having = " HAVING $having ";
        }

        $sql = "
SELECT $select
FROM civicrm_contribution AS contrib,
civicrm_contact AS contact
WHERE contrib.contact_id = contact.id
AND contrib.is_test = 0 
$where
GROUP BY contact.id
$having
";

        if ( ! empty( $sort ) ) {
            if ( is_string( $sort ) ) {
                $sql .= " ORDER BY $sort ";
            } else {
                $sql .= " ORDER BY " . trim( $sort->orderBy() );
            }
        } else {
            $sql .= "ORDER BY donation_amount desc";
        }

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

        $sql = implode( ' AND ', $clauses );
        if ( $includeContactIDs ) {
            $contactIDs = array( );
            foreach ( $this->_formValues as $id => $value ) {
                if ( $value &&
                     substr( $id, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $contactIDs[] = substr( $id, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
        
            if ( ! empty( $contactIDs ) ) {
                $contactIDs = implode( ', ', $contactIDs );
                if ( ! empty( $sql ) ) {
                    $sql .= " AND ";
                }
                $sql .= " contact.id IN ( $contactIDs )";
            }
        }

        return $sql;
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
        return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }

   function setTitle( $title ) {
       if ( $title ) {
           CRM_Utils_System::setTitle( $title );
       } else {
           CRM_Utils_System::setTitle(ts('Search'));
       }
   }
   }

?>
