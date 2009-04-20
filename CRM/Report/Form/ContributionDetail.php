<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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

class CRM_Report_Form_ContributionDetail extends CRM_Report_Form {

    function __construct( ) {
        $this->_columns = array( 'civicrm_contact'      =>
                                 array( 'dao'    => 'CRM_Contact_DAO_Contact',
                                        'fields' =>
                                        array( 'display_name' => array( 'label'    => ts( 'Contact Name' ),
                                                                        'required' => true ) )
                                        ),
                                 'civicrm_contribution' =>
                                 array( 'dao'    => 'CRM_Contribute_DAO_Contribution',
                                        'fields' =>
                                        array( 'amount'        => array( 'label'    => ts( 'Amount' ),
                                                                         'required' => true ),
                                               'trxn_id'       => null,
                                               'received_date' => null,
                                               'receipt_date'  => null,
                                               ) ) );

        $this->_filter =
            array( 'receive_date' => array( 'label'      => ts( 'Date Range' ),
                                            'table'      => 'civicrm_contribution',
                                            'operator'   => 'date_range'
                                            'type'       => 'date'
                                            'default'    => 'this month' ),
                   'total_amount' => array( 'label'      => ts( 'Aggregate Total Between' ),
                                            'table'      => 'civicrm_contribution',
                                            'operator'   => 'money_range',
                                            'type'       => 'money' ),
                   'sort_name'    => array( 'label'      => ts( 'Contact Name' ),
                                            'table'      => 'civicrm_contact',
                                            'field'      => 'sort_name',
                                            'operator'   => 'like' )
                   );

        $this->_options = array( 'include_statistics' => array( 'label' => ts( 'Include Contribution Statistics' ),
                                                                'type'  => 'checkbox' ),
                                 );
        
    }

    function preProcess( ) {
    }

    function buildQuickForm( ) {
        $this->addColumns( );
        
        $this->addFilters( );
    }

    function setDefaultValues( ) {
        $this->setDefaults( );
    }

    function postProcess( ) {
    }

}



