<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

/**
 * Files required
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';

class CRM_Contact_Form_Search_Simple extends CRM_Core_Form {

    public function preProcess( ) {
        $this->assign( 'rows', $this->get( 'rows' ) );
    }

    public function buildQuickForm( ) { 
        $config   =& CRM_Core_Config::singleton( );
        
        $this->assign( 'dojoIncludes',
                       "dojo.require('dojo.widget.Select');dojo.require('dojo.widget.ComboBox');dojo.require('dojo.widget.Tooltip');" );

//         $attributes = array( 'dojoType'     => 'ComboBox',
//                              'mode'         => 'remote',
//                              'dataUrl'      => CRM_Utils_System::url( 'civicrm/ajax/search',
//                                                                       "s=%{searchString}" ),
//                              );
//         $attributes += CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'sort_name' );
//         $this->add( 'text', 'sort_name', ts('Name'), $attributes );

//         $attributes = array( 'dojoType'     => 'Select',
//                              'style'        => 'width: 300px;',
//                              'autocomplete' => 'true' );

//         // select for state province
//         $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
//         $this->addElement('select', 'state_province', ts('State/Province'), $stateProvince, $attributes);

//         // select for country
//         $country = array('' => ts('- any country -')) + CRM_Core_PseudoConstant::country( );
//         $this->addElement('select', 'country', ts('Country'), $country, $attributes );


        //state - country widget
        $attributes = array( 'dojoType'       => 'ComboBox',
                             'mode'           => 'remote',
                             'style'          => 'width: 300px;',
                             'dataUrl'        => CRM_Utils_System::url( "civicrm/ajax/country",
                                                                        "s=%{searchString}&node=root",
                                                                       false, null, false ),
//                              'dataUrl'        => CRM_Utils_System::url( 'civicrm/ajax/country',
//                                                                         's=a&node=root',
//                                                                         true, null, false ),
                             'onValueChanged' => 'checkParamChildren',
                             'id'             => 'wizCardDefGroupId' );

        $this->addElement('select', 'country', ts('Country'), null, $attributes );

//         $this->addButtons( array(
//                                  array ( 'type'      => 'refresh',
//                                          'name'      => ts('Search'),
//                                          'isDefault' => true   ),
//                                  array ( 'type'      => 'cancel',
//                                          'name'      => ts('Cancel') ),
//                                  )
//                            );

    }

    public function postProcess( ) {
        $values = $this->exportValues( );

        if ( $values['sort_name'] ) {
            $string = CRM_Utils_Type::escape( $values['sort_name'], 'String' );
            $query = "
SELECT civicrm_contact.id as contact_id, civicrm_contact.sort_name as sort_name
FROM   civicrm_contact
WHERE  civicrm_contact.sort_name LIKE '%{$string}%'";
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $rows = array( );
            while ( $dao->fetch( ) ) {
                $rows[$dao->contact_id] = $dao->sort_name;
            }

            $this->assign_by_ref( 'rows', $rows );
            $this->set( 'rows', $rows );
        }
    }

}


