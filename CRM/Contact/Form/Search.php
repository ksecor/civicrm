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
class CRM_Contact_Form_Search extends CRM_Form {

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

        // need to populate constants for group and category
        CRM_PseudoConstant::populateGroup();
        CRM_PseudoConstant::populateCategory();

        // get the formvalues
       //         $formValues = $this->controller->exportValues($this->_name);
//         CRM_Error::debug_var('formValues', $formValues);
         $container = $this->controller->container();
         CRM_Error::debug_var('container->values->Search',  $container['values']['Search']);        
         $container['values']['Search']['KEY1'] = "BFC11";
         CRM_Error::debug_var('container->values->Search',  $container['values']['Search']);        
//         CRM_Error::debug_var('container',  $container);
//         // check for container -> values -> Search - >cb_contact_type
//         CRM_Error::debug_var('container->values->Search',  $container['values']['Search']);        


        switch($this->_mode) {
        case CRM_Form::MODE_BASIC:
            $this->buildBasicSearchForm();
            break;
        case CRM_Form::MODE_ADVANCED:
            $this->buildAdvancedSearchForm();
            break;        
        }

        CRM_Error::ll_method();
    }

    /**
     * Build the basic search form
     *
     * @access public
     * @return void
     */
    function buildBasicSearchForm( ) 
    {
        CRM_Error::le_method();

        // get the container
        $container =& $this->controller->container();

        //CRM_Error::debug_var('container',  $container);
        // check for container -> values -> Search - >cb_contact_type
        CRM_Error::debug_var('container->values->Search',  $container['values']['Search']);        

//         // we could have container filled with advanced search checkboxes so lets get those values
//         if($container['values']['Search']['contact_type']) {
//             $array1 = $container['values']['Search']['contact_type'];    
//             CRM_Error::debug_var('array1', $array1);
//             $array2 = array_slice($array1, 0, 1);
//             CRM_Error::debug_var('array2', $array2);
//             $key = key($array2);
//             CRM_Error::debug_var('key', $key);
//             $container['values']['Search']['contact_type'] = key(array_slice($array1, 0, 1));
//             CRM_Error::debug_var('container',  $container);
//         }


//         // we could have container filled with advanced search checkboxes so lets get those values
//         if($container['values']['Search']['group']) {
//             $group = $container['values']['Search']['group'];    
//             $container['values']['Search']['group'] = key(array_slice($group, 0, 1));
//         }

//         // we could have container filled with advanced search checkboxes so lets get those values
//         if($container['values']['Search']['category']) {
//             $category = $container['values']['Search']['category'];    
//             $container['values']['Search']['category'] = key(array_slice($category, 0, 1));
//         }


        $contactType = array('any' => ' - any contact - ') + CRM_PseudoConstant::$contactType;
        $this->add('select', 'contact_type', 'Show me.... ', $contactType);

        // add select for groups
        $group = array('any' => ' - any group - ') + CRM_PseudoConstant::$group;
        $this->add('select', 'group', 'in', $group);

        // add select for categories
        $category = array('any' => ' - any category - ') + CRM_PseudoConstant::$category;
        $this->add('select', 'category', 'Category', $category);

        // text for sort_name
        $this->add('text', 'sort_name', 'Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        
        // some actions.. what do we want to do with the selected contacts ?
        $actions = array( '' => '- actions -',
                          1  => 'Add Contacts to a Group',
                          2  => 'Tag Contacts (assign category)',
                          3  => 'Add to Household',
                          4  => 'Delete',
                          5  => 'Print',
                          6  => 'Export' );
        $this->add('select', 'action'   , 'Actions: '    , $actions    );

        // add buttons
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => 'Search' ,
                                         'isDefault' => true     )
                                 )        
                           );
        
        /*
         * add the go button for the action form, note it is of type 'next' rather than of type 'submit'
         *
         */
        $this->add('submit', $this->getButtonName( 'next' ), 'Go', array( 'class' => 'form-submit' ) );

        CRM_Error::le_method();
    }

    /**
     * Build the advanced search form
     *
     * @access public
     * @return void
     */
    function buildAdvancedSearchForm() 
    {

        // populate stateprovince, country, locationtype
        CRM_PseudoConstant::populateStateProvince();
        CRM_PseudoConstant::populateCountry();
        CRM_PseudoConstant::populateLocationType();

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

    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues( ) {
        $defaults = array( );
        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
        $this->addFormRule( array( 'CRM_Contact_Form_Search', 'formRule' ) );
    }

    function preProcess( ) {
        /*
         * since all output is generated by postProcess which will never be invoked by a GET call
         * we need to explicitly call it if we are invoked by a GET call
         *
         * Scenarios where we make a GET call include
         *  - pageID/sortID change
         *  - user hits reload
         *  - user clicks on menu
         */
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            $this->postProcess( );
        }

        
    }

    function postProcess() 
    {
        // if we are in reset state, i.e. just entered the form, dont display any result
        if($_GET['reset'] == 1) {
            return;
        }

        // check actionName and if next, then do not repeat a search, since we are going to the next page
        list( $pageName, $action ) = $this->controller->getActionName( );
        if ( $action == 'next' ) {
            return;
        }

        $formValues = $this->controller->exportValues($this->_name);

        // important - we need to store the formValues in the session in case we want to save it.
        if ($this->_mode == CRM_Form::MODE_ADVANCED) {
            $session = CRM_Session::singleton( );
            $session->set("formValues", serialize($formValues), "advancedSearch");
        }

        $selector = new CRM_Contact_Selector($formValues, $this->_mode);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this);
        $controller->run();
    }

    /**
     * Add a form rule for this form. If Go is pressed then we must select some checkboxes
     * and an action
     */
    static function formRule( &$fields ) {
        // check actionName and if next, then do not repeat a search, since we are going to the next page
        
        if ( array_key_exists( '_qf_Search_next', $fields ) ) {
            if ( ! CRM_Array::value( 'action', $fields ) ) {
                return array( 'action' => 'Please select a valid action.' );
            }

            foreach ( $fields as $name => $dontCare ) {
                if ( substr( $name, 0, self::CB_PREFIX_LEN ) == self::CB_PREFIX ) {
                    return true;
                }
            }
            return array( 'action' => 'Please select one or more checkboxes to perform the action on.' );
        }
        return true;
    }

}
?>