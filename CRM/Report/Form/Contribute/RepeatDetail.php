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

class CRM_Report_Form_Contribute_RepeatDetail extends CRM_Report_Form {

    function preProcess( ) {
        parent::preProcessCommon( );
    }

    function setDefaultValues( ) {
    }

    function buildQuickForm( ) {
        require_once 'CRM/Core/Form/Date.php';

        CRM_Core_Form_Date::buildDateRange( $this, 'receive_date_r1', true );
        CRM_Core_Form_Date::buildDateRange( $this, 'receive_date_r2', true );

        $this->add( 'checkbox',
                    'group_bys_country',
                    ts( 'Group Contacts by Country' ) );

        $this->add( 'checkbox',
                    'is_repeat',
                    ts( 'Show contacts who have donated in both ranges only' ) );

        parent::buildInstanceAndButtons( );
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );
        
        $r1_relative = CRM_Utils_Array::value( "receive_date_r1_relative", $this->_params );
        $r1_from     = CRM_Utils_Array::value( "receive_date_r1_from"    , $this->_params );
        $r1_to       = CRM_Utils_Array::value( "receive_date_r1_to"      , $this->_params );

        $c1_clause = $this->dateClause( 'c1.receive_date', $r1_relative, $r1_from, $r1_to );

        $r2_relative = CRM_Utils_Array::value( "receive_date_r2_relative", $this->_params );
        $r2_from     = CRM_Utils_Array::value( "receive_date_r2_from"    , $this->_params );
        $r2_to       = CRM_Utils_Array::value( "receive_date_r2_to"      , $this->_params );

        $c2_clause = $this->dateClause( 'c2.receive_date', $r2_relative, $r2_from, $r2_to );

        if ( $this->_params['is_repeat'] ) {
            $whereOP = 'AND';
        } else {
            $whereOP = 'OR';
        }
        $sql = "
SELECT    c.id, c.display_name,
          sum(c1.total_amount) as c1_amount,
          sum(c2.total_amount) as c2_amount          
FROM      civicrm_contact c
LEFT JOIN civicrm_contribution c1 on c1.contact_id = c.id AND $c1_clause
LEFT JOIN civicrm_contribution c2 on c2.contact_id = c.id AND $c2_clause
WHERE ( ( c1.id IS NOT NULL ) $whereOP ( c2.id IS NOT NULL ) )
GROUP BY c.id
";

        CRM_Core_Error::debug( $sql );
        $dao = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );

        while ( $dao->fetch( ) ) {
            $row = array( 'c.id'         => $dao->id,
                          'display_name' => $dao->display_name,
                          'c1_amount'    => $dao->c1_amount   ,
                          'c2_amount'    => $dao->c2_amount   ,
                          );
            $rows[] = $row;
        }
        CRM_Core_Error::debug( $rows );
        exit( );
    }

}


