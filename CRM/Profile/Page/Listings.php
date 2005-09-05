<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.1                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Social Source Foundation                        | 
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have | 
 | questions about the Affero General Public License or the licensing | 
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   | 
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo 01/15/2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Profile/Selector/Listings.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * This implements the profile page for all contacts. It uses a selector
 * object to do the actual dispay. The fields displayd are controlled by
 * the admin
 */
class CRM_Profile_Page_Listings extends CRM_Core_Page {

    /**
     * all the fields that are listings related
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /**
     * extracts the parameters from the request and constructs information for
     * the selector object to do a query
     *
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) {
        $this->_fields = CRM_Core_BAO_UFGroup::getListingFields( CRM_Core_Action::UPDATE,
                                                                 CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY );

        $where  = array( );
        $criteria = array( );
        $this->_tables = array( );

        $where[] = " ( civicrm_contact.contact_type = 'Individual' ) ";
        foreach ( $this->_fields as $key => $field ) {
            $value = CRM_Utils_Request::retrieve( $field['name'], $this, false, null, 'REQUEST' );
            if ( isset( $value ) && $value != null ) {
                $criteria[$field['title']] = str_replace( "", ', ', $value );
                $this->_fields[$key]['value'] = $value;

                if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $field['name'] ) ) {
                    $params[$cfID] = $value;
                    $sql = CRM_Core_BAO_CustomValue::whereClause($params);  
                    if ( $sql ) { 
                        $this->_tables['civicrm_custom_value'] = 1; 
                        $where[] = $sql; 
                    } 
                } else {
                    if ( $field['name'] === 'state_province_id' ) {
                        if ( is_numeric( $value ) ) {
                            $states =& CRM_Core_PseudoConstant::stateProvince();
                            $value  =  $states[$value];
                        }
                    } else if ( $field['name'] === 'country_id' ) {
                        if ( is_numeric( $value ) ) {
                            $countries =& CRM_Core_PseudoConstant::country( );
                            $value     =  $countries[$value];
                        }
                    }
                    $value = strtolower( $value );
                    $where[] = 'LOWER(' . $field['where'] . ') LIKE "%' . addslashes( $value ) . '%"'; 

                    list( $tableName, $fieldName ) = explode( '.', $field['where'], 2 ); 
                    if ( isset( $tableName ) ) {
                        $this->_tables[$tableName] = 1; 
                    }
                }
            }
        }
        
        $template = CRM_Core_Smarty::singleton( );
        $template->assign( 'criteria', $criteria );
        $this->_clause = implode( ' AND ', $where ); 
   }

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        $this->preProcess( );

        $selector =& new CRM_Profile_Selector_Listings( $this->_clause, $this->_tables );
        $controller =& new CRM_Core_Selector_Controller($selector ,
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                        $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                        CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TEMPLATE );
        $controller->setEmbedded( true );
        $controller->run( );

        $formController =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Search', 'Search Profile', CRM_Core_Action::ADD );
        $formController->setEmbedded( true );
        $formController->process( ); 
        $formController->run( ); 

        return parent::run( );

    }

}

?>
