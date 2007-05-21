<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
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
require_once 'CRM/Utils/Pager.php';

class CRM_Contact_Form_Search_Zandigo extends CRM_Core_Form {

    protected $_customFields;

    protected $_pager;
    protected $_rowCount;
    protected $_offset;

    protected $_force;

    protected $_rows;

    public function preProcess( ) {
        $this->initialize( );

        $this->_force = CRM_Utils_Request::retrieve( 'force', 'Boolean',
                                                     CRM_Core_DAO::$_nullObject );

        $storeRowCount = $this->get( CRM_Utils_Pager::PAGE_ROWCOUNT );
        if ( $storeRowCount ) {
            $pagerParams['rowCount'] = $storeRowCount;
        } else {
            $pagerParams['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
        }

        $pagerParams['rowCount' ]  = 2;
        $pagerParmas['status'   ]  = 'People %%StatusMessage%%';
        $pagerParmas['buttonTop']  = 'PagerTopButton';

        $pagerParams['total'    ]  = $this->get( 'totalCount' );

        $this->_pager = new CRM_Utils_Pager( $pagerParams );
        list( $this->_offset, $this->_rowCount ) = $this->_pager->getOffsetAndRowCount( );
        $this->assign_by_ref( 'pager', $this->_pager );

        if ( $this->_force ) {
            $this->postProcess( );

            // redo pager stuff
            $this->_pager = new CRM_Utils_Pager( $pagerParams );
            list( $this->_offset, $this->_rowCount ) = $this->_pager->getOffsetAndRowCount( );
            $this->assign_by_ref( 'pager', $this->_pager );
        }

        $this->_rows =& $this->get( 'rows' );
        $this->assign_by_ref( 'rows'     , $this->_rows          );
        $this->assign       ( 'rowsEmpty',
                              $this->get( 'rowsEmpty' ) );
    }

    public function initialize( ) {
        $this->_customFields = array( 
                                     89 => array( 'name'   => 'People'            ,
                                                  'loc'    => 'top'               ,
                                                  'return' => 1                   ),
                                     90 => array( 'name'   => 'Organizations'     ,
                                                  'loc'    => 'top'               ,
                                                  'return' => 1                   ),
                                     91 => array( 'name'   => 'High School'       ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 1                   ),
                                     92 => array( 'name'   => 'CEEB/ACT Code'     ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 0                   ),
                                     93 => array( 'name'   => 'Graduation Year'   ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 0                   ),
                                     94 => array( 'name'   => 'Type'              ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 0                   ),
                                     95 => array( 'name'   => 'City'              ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 1                   ),
                                     96 => array( 'name'   => 'State/Province'    ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 1                   ),
                                     97 => array( 'name'   => 'Postal Code'       ,
                                                  'loc'    => 'bottom'            ,
                                                  'return' => 1                   ),
                                     );

    }
    
    /**
        * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );

        // Set default search type to "Students" if we don't have one yet.
        $params = $_POST;
        if ( empty( $params['custom_89'] ) && empty( $params['custom_90'] ) ) {
            $defaults['custom_89'] = 'Student';
        }
        return $defaults;
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
        $js = array( 'onclick' => "return showHideZ(this);");
        foreach ( $this->_customFields as $key => $field ) {
            CRM_Core_BAO_CustomField::addQuickFormElement( $this,
                                                           "custom_$key",
                                                           $key,
                                                           false,
                                                           false, false, $field['name'],
                                                           $js );
        }
        $this->assign( 'customFields', $this->_customFields );

        // add checkboxes
        if ( is_array( $this->_rows ) ) {
            $this->addElement( 'checkbox', 'toggleSelect', null, null, array( 'onclick' => "return toggleCheckboxVals('mark_x_',this.form);" ) );
            foreach ( $this->_rows as $row ) {
                $this->addElement( 'checkbox', $row['checkbox'],
                                   null, null,
                                   array( 'onclick' => "return checkSelectedBox('" . $row['checkbox'] . "', '" . $this->getName() . "');" ) );
            }
        }

        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => ts('Search'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );


        $this->addFormRule( array( 'CRM_Contact_Form_Search_Zandigo', 'formRule' ) );
    }

    static function formRule( &$fields ) {
        if ( ! empty( $fields['custom_89'] ) &&
             ! empty( $fields['custom_90'] ) ) {
            $errors = array( 'custom_89' => ts( 'Only one of People or Organizations can be selected' ) );
            return $errors;
        }

        if ( empty( $fields['custom_89'] ) &&
             empty( $fields['custom_90'] ) ) {
            $errors = array( 'custom_89' => ts( 'At least one of People or Organizations should be selected' ) );
            return $errors;
        }

        return true;
    }

    public function postProcess( ) {
        $values = $this->controller->exportValues( $this->_name );

        $returnProperties = array( 'contact_id'   => 1,
                                   'display_name' => 1,
                                   'image_URL'    => 1,
                                   );

        foreach ( $this->_customFields as $key => $field ) {
            if ( $field['return'] ) {
                $returnProperties["custom_$key"] = 1;
            }
        }


        require_once 'api/Search.php';
        list( $result, $options, $totalCount ) = crm_contact_search( $values, $returnProperties, null,
                                                                     $this->_offset, $this->_rowCount,
                                                                     true );

        $this->set( 'totalCount', $totalCount );
        $this->set( 'rowsEmpty',
                    $totalCount == 0 ? true : false );
        $this->_rows = array_values( $result );
        foreach ( $this->_rows as $key => $row ) {
            $this->_rows[$key]['checkbox'] = CRM_Core_Form::CB_PREFIX . $row['contact_id'];
        }
        $this->set( 'rows', $this->_rows );
    }

}

?>