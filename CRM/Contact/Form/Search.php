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
//require_once 'CRM/Contact/Selector/Individual.php';
require_once 'CRM/Contact/Selector/Selector.php';

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
    function __construct( $name, $state, $mode = self::MODE_NONE ) {

        //        CRM_Error::debug_stacktrace(2);

        parent::__construct($name, $state, $mode);
    }

    /**
     * Build the form
     *
     * @access protected
     * @return void
     */
    function buildQuickForm( ) {
        
        switch($this->_mode) {
        case self::MODE_SEARCH:
            $this->addElement('text','mode',self::MODE_SEARCH);
            $this->_buildSearchForm();
            break;
        case self::MODE_SEARCH_MINI:
            $this->addElement('text', 'mode', self::MODE_SEARCH_MINI);
            $this->_buildMiniSearchForm();
            break;          
        }
    }


    private function _buildSearchForm()
    {
        //$this->setFormAction('index.php?q=crm/contact/list');
        $this->add('select', 'contact_type', 'Contact Type', CRM_SelectValues::$contactType);
        
        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'refresh',
                                                'name'      => 'Submit' ,
                                                'isDefault' => true     ),
                                        array ( 'type'      => 'done'  ,
                                                'name'      => 'Done'   ),
                                        array ( 'type'      => 'reset' ,
                                                'name'      => 'Reset'  ),
                                        array ( 'type'      => 'cancel',
                                                'name'      => 'Cancel' ),
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
    }

    /**
     * Pre-process the environment before building the form
     *
     * @access protected
     * @return void
     */
    function preProcess() 
    {
        switch ($this->_mode) {
        case self::MODE_SEARCH:
            $this->_searchPreProcess();
            break;            
        case self::MODE_SEARCH_MINI:
            $this->_miniSearchPreProcess();
            break;            
        }
    }


    function postProcess() 
    {
        switch ($this->_mode) {
        case self::MODE_SEARCH:
            $this->_searchPostProcess();
            break;            
        case self::MODE_SEARCH_MINI:
            $this->_miniSearchPostProcess();
            break;            
        }
    }

    function _searchPreProcess() {
    }

    /**
     * Process the form submit
     *
     *
     * @access protected
     * @return void
     */
    function _searchPostProcess( ) {
        $params = array( );
        $contact_type = trim($this->controller->exportValue($this->_name, 'contact_type'));
        if (!empty( $contact_type ))  {
            $params['contact_type'] = $contact_type;
        }

        $selector   = new CRM_Contact_Selector_Selector($params);
        $controller = new CRM_Selector_Controller($contact , null, null, CRM_Action::VIEW, CRM_Selector_Controller::TEMPLATE);
        $controller->run();
    }

}

?>