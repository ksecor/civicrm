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
     * @access protected
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->add('select', 'contact_type', 'Show me.... ', CRM_SelectValues::$contactType);
        $this->add('text', 'sort_name', 'Name:',
                   CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        
        $groups     = array(''  => '- any group -', 
                            1   => 'Group A',
                            2   => 'Group B' );
        $categories = array(''  => '- any category -', 
                            1   => 'Category A',
                            2   => 'Category B' );
        $this->add('select', 'group_id'   , 'in '    , $groups    );
        $this->add('select', 'category_id', 'Category ', $categories);

        $actions = array( '' => '- actions -',
                          1  => 'Add Contacts to a Group',
                          2  => 'Tag Contacts (assign category)',
                          3  => 'Add to Household',
                          4  => 'Delete',
                          5  => 'Print',
                          6  => 'Export' );
        $this->add('select', 'action_id'   , 'Actions: '    , $actions    );
        
        //link to show advanced search form
        $this->addElement('link', 'adv_search', null, 'crm/contact/search', ' >> Advanced Search....');
        $this->addElement('link', 'select_all', null, 'crm/contact/search', 'All', array('onclick' => 'check('.$this->getName().',1); return false;'));
        $this->addElement('link', 'select_none', null, 'crm/contact/search', 'None', array('onclick' => 'check('.$this->getName().',0); return false;'));
        // $this->addElement('link', 'select_none', null, '#', 'None', array('onclick' => 'check(); return false;'));


        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'refresh',
                                                'name'      => 'Search' ,
                                                'isDefault' => true     )
                                        )        
                                  );
        
        /*
         * added one extra button, this is needed as per the design of the action form
         */
        $this->add('submit', 'go', 'Go');
        
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
        $params = array( );
        $contact_type = trim($this->controller->exportValue($this->_name, 'contact_type'));
        $sort_name = trim($this->controller->exportValue($this->_name, 'sort_name'));
        if (!empty( $contact_type ))  {
            $params['contact_type'] = $contact_type;
        }
        if (!empty( $sort_name ))  {
            $params['sort_name'] = $sort_name;
        }

        $selector   = new CRM_Contact_Selector($params);
        $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, CRM_Selector_Controller::TEMPLATE, $this);
        $controller->run();

    }
}

?>