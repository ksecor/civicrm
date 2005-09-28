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
     * The input params from the request
     *
     * @var array 
     * @access protected 
     */ 
    protected $_params;

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

        $criteria = array( );
        $this->_params   = array( );

        foreach ( $this->_fields as $key => $field ) {
            $name = $field['name'];
            if (substr_count($name, 'country'))        $name = str_replace('country', 'country_id', $name);
            if (substr_count($name, 'state_province')) $name = str_replace('state_province', 'state_province_id', $name);
            $value = CRM_Utils_Request::retrieve( $name, $this, false, null, 'REQUEST' );
            if ( isset( $value ) && $value != null ) {
                if (substr_count($name, 'country'))        $value = CRM_Core_PseudoConstant::country($value);
                if (substr_count($name, 'state_province')) $value = CRM_Core_PseudoConstant::stateProvince($value);
                $criteria[$field['title']] = str_replace( "", ', ', $value );
                $this->_fields[$key]['value'] = $value;
                $this->_params[$field['name']] = $value;
            }
        }
        
        $template = CRM_Core_Smarty::singleton( );
        $template->assign( 'criteria', $criteria );
   }

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        $this->preProcess( );

        $selector =& new CRM_Profile_Selector_Listings( $this->_params );
        $controller =& new CRM_Core_Selector_Controller($selector ,
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                        $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                        CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TEMPLATE );
        $controller->setEmbedded( true );
        $controller->run( );

        $formController =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Search', ts('Search Profile'), CRM_Core_Action::ADD );
        $formController->setEmbedded( true );
        $formController->process( ); 
        $formController->run( ); 

        return parent::run( );

    }

}

?>
