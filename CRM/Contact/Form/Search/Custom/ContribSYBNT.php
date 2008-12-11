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
    
require_once 'CRM/Contact/Form/Search/Interface.php';
    
class CRM_Contact_Form_Search_Custom_ContribSYBNT
implements CRM_Contact_Form_Search_Interface {
        
    protected $_formValues;
        
    function __construct( &$formValues ) {     
        $this->_formValues = $formValues;
            
        $this->_columns = array( ts('Contact Id')   => 'contact_id'  ,
                                 ts('Name'      )   => 'display_name',
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
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');
            
        $form->add( 'date',
                    'end_date',
                    ts('End Date'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('end_date', ts('Select a valid date.'), 'qfDate');
            
        $form->add( 'text',
                    'exclude_min_amount',
                    ts( 'Exclusion Min Amount' ) );
        $form->add( 'text',
                    'exclude_max_amount',
                    ts( 'Exclusion Max Amount' ) );
            
        $form->add( 'date',
                    'exclude_start_date',
                    ts('Exclusion Date Start'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('exclude_start_date', ts('Select a valid date.'), 'qfDate');
            
        $form->add( 'date',
                    'exclude_end_date',
                    ts('Exclusion Date End'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('exclude_end_date', ts('Select a valid date.'), 'qfDate');
        // @TODO: Decide on better names for "Exclusion"
        // @TODO: Add rule to ensure that exclusion dates are not in the inclusion range
        $form->assign( 'elements', array( 'min_amount', 'max_amount', 'start_date', 'end_date', 'exclude_min_amount', 'exclude_max_amount', 'exclude_start_date', 'exclude_end_date') );
    }
        
    function count( ) {
        $sql = $this->all( );
            
        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray );
        return $dao->N;
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
            SELECT DISTINCT contact.id as contact_id,
            contact.display_name as display_name,
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
 
        //           CRM_Core_Error::debug('sql',$sql); exit();
        return $sql;
    }
        
    function from( ) {
        return '';
    }
        
    function where( $includeContactIDs = false ) {
        $clauses = array( );
            
        $startDate = CRM_Utils_Date::format( $this->_formValues['start_date'] );
        $endDate = CRM_Utils_Date::format( $this->_formValues['end_date'] );
            
        $excludeStart = CRM_Utils_Date::format( $this->_formValues['exclude_start_date'] );
        $excludeEnd = CRM_Utils_Date::format( $this->_formValues['exclude_end_date'] );
            
        if ( ($startDate && $endDate && $excludeStart && $excludeEnd) ) {
            $clauses[] = "contrib.receive_date >= $startDate";
            $clauses[] = "contrib.receive_date <= $endDate";
                
            $excludeMin = CRM_Utils_Array::value( 'exclude_min_amount', $this->_formValues );                
            $excludeMax = CRM_Utils_Array::value( 'exclude_max_amount', $this->_formValues );
                
            // Run subquery
            $eligible_query =
                "SELECT DISTINCT contact_id AS eligible_id
                FROM civicrm_contribution AS contrib_check
                WHERE contrib_check.is_test = 0
                And contrib_check.receive_date >= $excludeStart
                AND contrib_check.receive_date <= $excludeEnd
                GROUP BY contrib_check.contact_id 
                HAVING sum(contrib_check.total_amount) >= $excludeMin
                AND sum(contrib_check.total_amount) <= $excludeMax";
            $dao = CRM_Core_DAO::executeQuery( $eligible_query, 
                                               CRM_Core_DAO::$_nullArray );
            $eligible_results = array( );
            while ( $dao->fetch( ) ) {
                $eligible_results[] = $dao->eligible_id;
            }
            if ( ! empty($eligible_results) ) {
                $eligible_ids = '('.implode(',', $eligible_results).')';
                $clauses[] = "contact.id NOT IN $eligible_ids";
            }
        } else if ( $startDate && $endDate ) {
            $clauses[] = "contrib.receive_date >= $startDate";
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
        return null;
    }

    function summary( ) {
        return null;
    }
        
}
    

