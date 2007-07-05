<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

class CRM_Grant_BAO_Query 
{
   
    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 7) == 'grant_' ) {
                
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        switch( $name ) {
            
        case 'grant_money_transfer_date_low':
        case 'grant_money_transfer_date_high':
            $query->dateQueryBuilder( $values,
                                      'civicrm_grant', 'grant_money_transfer_date_low', 'money_transfer_date', 'Money Transfer Date' );
            return;

        case 'grant_application_received_date_low':
        case 'grant_application_received_date_high':
            $query->dateQueryBuilder( $values,
                                       'civicrm_grant', 'grant_application_received_date', 'application_received_date', 'Application Received Date' );
            return;

        case 'grant_type_id':
            
            $value = strtolower(addslashes(trim($value)));

            $query->_where[$grouping][] = "civicrm_grant.grant_type_id $op '{$value}'";
            
            require_once 'CRM/Core/OptionGroup.php';
            $grantTypes  = CRM_Core_OptionGroup::values("grant_type" );
           
            $query->_qill[$grouping ][] = ts( 'Grant Type %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_grant'] = $query->_whereTables['civicrm_grant'] = 1;

            return;

        case 'grant_status_id':
            
            $value = strtolower(addslashes(trim($value)));

            $query->_where[$grouping][] = "civicrm_grant.grant_status_id $op '{$value}'";
            
            require_once 'CRM/Core/OptionGroup.php';
            $grantTypes  = CRM_Core_OptionGroup::values("grant_status" );
           
            $query->_qill[$grouping ][] = ts( 'Grant Type %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_grant'] = $query->_whereTables['civicrm_grant'] = 1;

            return;
   
        case 'grant_report_received':
            $query->_where[$grouping][] = "civicrm_grant.grant_report_received $op $value";
            $query->_qill[$grouping][]  = "Grant Report Received?";
            $query->_tables['civicrm_grant'] = $query->_whereTables['civicrm_grant'] = 1;
            
            return;
        }
    }


    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    /**
     * add all the elements shared between grant search and advanaced search
     *
     * @access public 
     * @return void
     * @static
     */   
    static function buildSearchForm( &$form ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $domainID = CRM_Core_Config::domainID( );
        require_once 'CRM/Core/OptionGroup.php'; 
        $grantType = CRM_Core_OptionGroup::values( 'grant_type' );
        $form->add('select', 'grant_type_id',  ts( 'Grant Type' ),
                   array( '' => ts( '-select-' ) ) + $grantType );

        $grantStatus = CRM_Core_OptionGroup::values( 'grant_status' );
        $form->add('select', 'grant_status_id',  ts( 'Grant Status' ),
                   array( '' => ts( '-select-' ) ) + $grantStatus );
        
        $form->addElement('date', 'grant_application_received_date_low', ts('Application Recieved Date - From'), 
                          CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_application_received_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'grant_application_received_date_high', ts('To'), 
                          CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_application_received_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->addElement('date', 'grant_money_transfer_date_low', ts('Money Sent Date - From'), 
                          CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_money_transfer_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'grant_money_transfer_date_high', ts('To'),
                          CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_money_transfer_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->addElement('checkbox','grant_report_received', ts('Grant report received'),null );
        
        $form->add( 'text', 'grant_amount_total', ts('Amount total') );
        
        $form->assign( 'validGrant', true );
        
    }
    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'grantForm' );
        $showHide->addShow( 'grantForm_show' );
    }
   
}

?>
