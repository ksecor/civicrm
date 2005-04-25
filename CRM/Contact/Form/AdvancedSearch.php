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
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_AdvancedSearch extends CRM_Contact_Form_Search {

    /**
     * Class construtor
     *
     * @param string    $name  name of the form
     * @param CRM_State $state State object that is controlling this form
     * @param int       $mode  Mode of operation for this form
     *
     * @return CRM_Contact_Form_Search
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE)
    {
        parent::__construct($name, $state, $mode);
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        CRM_Error::le_method();
        $this->populatePseudoConstant();

        // add checkboxes for contact type
        $cb_contact_type = array( );
        foreach (CRM_PseudoConstant::$contactType as $k => $v) {
            $cb_contact_type[] = HTML_QuickForm::createElement('checkbox', $k, null, $v);
        }
        $this->addGroup($cb_contact_type, 'cb_contact_type', 'Show Me....', '<br />');
        
        // checkboxes for groups
        $cb_group = array();
        foreach (CRM_PseudoConstant::$group as $groupID => $groupName) {
            $this->addElement('checkbox', "cb_group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        $cb_category = array();
        foreach (CRM_PseudoConstant::$category as $categoryID => $categoryName) {
            $cb_category[] = $this->addElement('checkbox', "cb_category[$categoryID]", null, $categoryName);
        }

        // add text box for last name, first name, street name, city
        $this->addElement('text', 'sort_name', 'Contact Name', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        $this->addElement('text', 'street_name', 'Street Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'street_name'));
        $this->addElement('text', 'city', 'City:',CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'city'));

        // select for state province
        $stateProvince = array('' => ' - any state/province - ') + CRM_PseudoConstant::$stateProvince;
        $this->addElement('select', 'state_province', 'State/Province', $stateProvince);

        // select for country
        $country = array('' => ' - any country - ') + CRM_PseudoConstant::$country;
        $this->addElement('select', 'country', 'Country', $country);

        // add text box for postal code
        $this->addElement('text', 'postal_code', 'Postal Code', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_low', 'Postal Code Range From', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_high', 'To', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );

        // checkboxes for location type
        $cb_location_type = array();
        $locationType = CRM_PseudoConstant::$locationType + array('any' => 'Any Locations');
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $cb_location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $this->addGroup($cb_location_type, 'cb_location_type', 'Include these locations', '&nbsp;');
        
        // checkbox for primary location only
        $this->addElement('checkbox', 'cb_primary_location', null, 'Search for primary locations only');        

        // add components for saving the search
        //$this->addElement('checkbox', 'cb_ss', null, 'Save Search ?');
        //$this->addElement('text', 'ss_name', 'Name', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name') );
        //$this->addElement('text', 'ss_description', 'Description', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description') );

        // add the buttons
        $this->addButtons(array(
                                array ( 'type'      => 'refresh',
                                        'name'      => 'Search',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => 'Reset'),
                                )
                          );
        
        CRM_Error::ll_method();
    }


    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        
        CRM_Error::le_method();

        $defaults = array();
        $csv = array();

        $session = CRM_Session::singleton( );        
        $session->getVars($csv, "commonSearchValues");
      
        CRM_Error::debug_var('csv', $csv);
        // name
        $defaults['sort_name'] = $csv['name'];

        // contact_type
        if($csv['contact_type']) {
            $defaults['cb_contact_type'] = array($csv['contact_type'] => 1);
        }

        // group
        if($csv['group']) {
            $defaults['cb_group'] = array($csv['group'] => 1);
        }

        // category
        if($csv['category']) {
            $defaults['cb_category'] = array($csv['category'] => 1);
        }

        CRM_Error::debug_var('defaults', $defaults);
        CRM_Error::ll_method();

        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
        $this->addFormRule( array( 'CRM_Contact_Form_AdvancedSearch', 'formRule' ) );
    }


    function postProcess() 
    {
        CRM_Error::le_method();
        // if we are in reset state, i.e. just entered the form, dont display any result
        if($_GET['reset'] == 1) {
            CRM_Error::ll_method();
            return;
        }
        
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        list( $pageName, $action ) = $this->controller->getActionName( );
        if ( $action == 'next' ) {
            CRM_Error::ll_method();
            return;
        }

        if($ssid=CRM_Request::retrieve('ssid')) {
            CRM_Error::debug_log_message("ssid is set");

            // ssid is set hence we need to set the formValues for it.
            // also we need to set the values in the form...
            $ssDAO = new CRM_Contact_DAO_SavedSearch();
            $ssDAO->id = $ssid;
            $ssDAO->selectAdd();
            $ssDAO->selectAdd('id, form_values');
            if($ssDAO->find(1)) {
                // make sure u unserialize - since it's stored in serialized form
                $formValues = unserialize($ssDAO->form_values);
            }
        } else {
            CRM_Error::debug_log_message("ssid is not set");
            // get user submitted values
            $formValues = $this->controller->exportValues($this->_name);
        }

        $session = CRM_Session::singleton( );

        // important - we need to store the formValues in the session in case we want to save it.
        $session->set("formValues", serialize($formValues), "advancedSearch");

        // store the user submitted values in the common search values scope
        $session->set("name", $formValues['sort_name'], "commonSearchValues");        

        // store contact_type, group and category
        $session->set("contact_type", $formValues['cb_contact_type'] ? key($formValues['cb_contact_type']) : "", "commonSearchValues");
        $session->set("group", $formValues['cb_group'] ? key($formValues['cb_group']) : "", "commonSearchValues");
        $session->set("category", $formValues['cb_category'] ? key($formValues['cb_category']) : "", "commonSearchValues");

        CRM_Error::debug_var('formValues', $formValues);
        CRM_Error::debug_var('csv', $csv);

        $selector = new CRM_Contact_Selector($formValues, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this, CRM_Selector_Controller::SESSION );
        $controller->run();

        CRM_Error::ll_method();
    }

    protected function populatePseudoConstant() {
        parent::populatePseudoConstant();

        // populate stateprovince, country, locationtype
        CRM_PseudoConstant::getStateProvince();
        CRM_PseudoConstant::getCountry();
        CRM_PseudoConstant::getLocationType();
    }
}
?>