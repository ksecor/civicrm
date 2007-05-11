<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';

class CRM_Contact_Form_Search_Zandigo extends CRM_Core_Form {

    protected $_customFields;

    public function preProcess( ) {
        $rows =& $this->get( 'rows' );
        $this->assign( 'rowCount', count( $rows ) );
        $this->assign( 'rows'    , $rows );

        $this->_customFields = array( 
                                     89 => array( 'name' => 'Member Type'       ,
                                                  'loc'  => 'top'               ),
                                     90 => array( 'name' => 'Organization Type' ,
                                                  'loc'  => 'top'               ),
                                     91 => array( 'name' => 'High School'       ,
                                                  'loc'  => 'bottom'            ),
                                     92 => array( 'name' => 'CEEB/ACT Code'     ,
                                                  'loc'  => 'bottom'            ),
                                     93 => array( 'name' => 'Graduation Year'   ,
                                                  'loc'  => 'bottom'            ),
                                     94 => array( 'name' => 'Type'              ,
                                                  'loc'  => 'bottom'            ),
                                     95 => array( 'name' => 'City'              ,
                                                  'loc'  => 'bottom'            ),
                                     96 => array( 'name' => 'State/Province'    ,
                                                  'loc'  => 'bottom'            ),
                                     97 => array( 'name' => 'Postal Code'       ,
                                                  'loc'  => 'bottom'            ),
                                     );


    }

    public function buildQuickForm( ) { 
        $config   =& CRM_Core_Config::singleton( );
        $domainID =  CRM_Core_Config::domainID( );
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Individual' );

        $this->add( 'text', 'first_name', ts('First Name'),
                    $attributes['first_name'] );
        $this->add( 'text', 'middle_name', ts('Middle Name'),
                    $attributes['middle_name'] );
        $this->add( 'text', 'last_name', ts('Last Name'),
                    $attributes['last_name'] );

        $this->add( 'text', 'organization_name', ts('Name'),
                    CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Organization', 'organization_name' ) );
        $this->add( 'text', 'email', ts('Email'),
                    CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Email', 'email' ) );

        $gender = array('' => ts('- any gender -')) + CRM_Core_PseudoConstant::gender( );
        $this->addElement('select', 'gender', ts('Gender'), $gender );

        $this->add( 'text', 'city', ts('City'),
                    CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Address', 'city' ) );
        $this->add( 'text', 'Postal Code', ts('Postal Code'),
                    CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Address', 'postal_code' ) );
                    

        // select for state province
        $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
        $this->addElement('select', 'state_province', ts('State/Province'), $stateProvince );

        // select for country
        $country = array('' => ts('- any country -')) + CRM_Core_PseudoConstant::country( );
        $this->addElement('select', 'country', ts('Country'), $country );

        // add all custom fields
        require_once 'CRM/Core/BAO/CustomField.php';
        foreach ( $this->_customFields as $key => $field ) {
            CRM_Core_BAO_CustomField::addQuickFormElement( $this,
                                                           "custom_$key",
                                                           $key,
                                                           false,
                                                           false, false, null );
        }
        $this->assign( 'customFields', $this->_customFields );
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => ts('Search'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );

    }

    public function postProcess( ) {
        $values = $this->exportValues( );

        require_once 'api/Search.php';
        list( $result, $options ) = crm_contact_search( $values, null, null, 0, 0 );

        $rows = array_values( $result );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'rowCount', count( $rows ) );
        $this->set( 'rows', $rows );
    }

}

?>