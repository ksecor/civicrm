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
require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * Advanced Search form for which provides more options
 * like Categories, Groups, Addresses and Locations.
 */
class CRM_Contact_Form_AdvancedSearch extends CRM_Form {

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

    public static $groups     = array(''  => '- any group -', 
                                      1   => 'Group A',
                                      2   => 'Group B' );
    public static $categories = array(''  => '- any category -', 
                                      1   => 'Category A',
                                      2   => 'Category B' );

    /**
     * Build the form
     *
     * @access protected
     * @return void
     */
    function buildQuickForm( ) 
    {
        /*code for advanced search start*/
        $contact_type = array( );
        foreach (CRM_SelectValues::$contactType as $keys => $values) {
            $contact_type[] = HTML_QuickForm::createElement('checkbox', $keys, null, $values);
        }

        /*$contact_typeA[] = HTML_QuickForm::createElement('checkbox', 'individual', null, 'Individual');
        $contact_typeA[] = HTML_QuickForm::createElement('checkbox', 'household', null, 'Household');
        $contact_typeA[] = HTML_QuickForm::createElement('checkbox', 'organization', null, 'Organization');*/

        $this->addGroup($contact_type, 'contact_type', 'Show Me....', '<br />');

        $group_id = array();
        foreach (CRM_Contact_Form_AdvancedSearch::$groups as $keys => $values) {
            $group_id[] = HTML_QuickForm::createElement('checkbox', $values, null, $values);
        }
        $this->addGroup($group_id, 'group_id', 'In Group (s)', '<br />');

        $category_id = array();
        foreach (CRM_Contact_Form_AdvancedSearch::$categories as $keys => $values) {
            $category_id[] = HTML_QuickForm::createElement('checkbox', $values, null, $values);
        }
        $this->addGroup($category_id, 'category_id', 'In Categorie (s)', '<br />');

        $this->add('text', 'last_name', 'Contact Name',
                   CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        $this->add('text', 'first_name', 'First Name',
                   CRM_DAO::getAttribute('CRM_Contact_DAO_Individual', 'first_name') );
        
        $this->add('text', 'street_name', 'Street Name:',
                   CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'street_name'));
        $this->add('text', 'city', 'City:',
                   CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'city'));        
        /*code for advanced search end*/
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
    }

}

?>