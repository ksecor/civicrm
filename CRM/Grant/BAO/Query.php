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
        
        $form->addElement('date', 'grant_application_received_date_low', ts('Application Recieved Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_application_received_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'grant_application_received_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_application_received_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->addElement('date', 'grant_money_transfer_date_low', ts('Money Sent Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('grant_money_transfer_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('date', 'grant_money_transfer_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
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
