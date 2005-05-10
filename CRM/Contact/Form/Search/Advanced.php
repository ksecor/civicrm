<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * advanced search, extends basic search
 */
class CRM_Contact_Form_Search_Advanced extends CRM_Contact_Form_Search {

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        // add checkboxes for contact type
        $cb_contact_type = array( );
        foreach (CRM_Core_SelectValues::$contactType as $k => $v) {
            if ( ! empty( $k ) ) {
                $cb_contact_type[] = HTML_QuickForm::createElement('checkbox', $k, null, $v);
            }
        }
        $this->addGroup($cb_contact_type, 'cb_contact_type', 'Contact Type(s)', '<br />');
        
        // checkboxes for groups
        $cb_group = array();
        foreach ($this->_group as $groupID => $groupName) {
            $this->_groupElement = $this->addElement('checkbox', "cb_group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        foreach ($this->_category as $categoryID => $categoryName) {
            $this->_categoryElement = $this->addElement('checkbox', "cb_category[$categoryID]", null, $categoryName);
        }

        // add text box for last name, first name, street name, city
        $this->addElement('text', 'sort_name', 'Find...', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        $this->addElement('text', 'street_name', 'Street Name', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address', 'street_name'));
        $this->addElement('text', 'city', 'City',CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address', 'city'));

        // select for state province
        $stateProvince = array('' => ' - any state/province - ') + CRM_Core_PseudoConstant::stateProvince( );
        $this->addElement('select', 'state_province', 'State/Province', $stateProvince);

        // select for country
        $country = array('' => ' - any country - ') + CRM_Core_PseudoConstant::country( );
        $this->addElement('select', 'country', 'Country', $country);

        // add text box for postal code
        $this->addElement('text', 'postal_code', 'Postal Code', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addRule('postal_code', 'Enter valid Postal Code', 'numeric' );
        
        $this->addElement('text', 'postal_code_low', 'Range-From', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address','postal_code') );
        $this->addRule('postal_code_low', 'Enter valid From range of Postal Code', 'numeric' );

        $this->addElement('text', 'postal_code_high', 'To', CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addRule('postal_code_high', 'Enter valid To range of Postal Code', 'numeric' );

        // checkboxes for location type
        $cb_location_type = array();
        $locationType = CRM_Core_PseudoConstant::locationType( );
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $cb_location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $this->addGroup($cb_location_type, 'cb_location_type', 'Location Types', '&nbsp;');
        
        // checkbox for primary location only
        $this->addElement('checkbox', 'cb_primary_location', null, 'Search primary locations only');        

        $this->buildQuickFormCommon( );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = $this->_formValues;

        if ( $this->_context === 'amtg' ) {
            $defaults['task'] = CRM_Contact_Task::GROUP_CONTACTS;
        } else {
            $defaults['task'] = CRM_Contact_Task::PRINT_CONTACTS;
        }

        return $defaults;
    }

    /**
     * The post processing of the form gets done here.
     *
     * Key things done during post processing are
     *      - check for reset or next request. if present, skip post procesing.
     *      - now check if user requested running a saved search, if so, then
     *        the form values associated with the saved search are used for searching.
     *      - if user has done a submit with new values the regular post submissing is 
     *        done.
     * The processing consists of using a Selector / Controller framework for getting the
     * search results.
     *
     * @param none
     *
     * @return none 
     * @access public
     */
    function postProcess() 
    {
        if ( ! $this->_force ) {
            // get user submitted values
            $this->_formValues = $this->controller->exportValues($this->_name);
        }

        $this->postProcessCommon( );
    }

}

?>