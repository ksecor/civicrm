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
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contact_Form_Task_SaveSearch extends CRM_Contact_Form_Task {
    /**
     * saved search id if any
     *
     * @var int
     */
    protected $_id;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        $this->_id   = null;

        $this->_task = $values['task'];
        $this->assign( 'taskName', CRM_Contact_Task::$tasks[$this->_task] );
    }

    /**
     * Build the form - it consists of
     *    - displaying the QILL (query in local language)
     *    - displaying elements for saving the search
     *
     * @param none
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        // get the qill 
        $qill = CRM_Contact_Selector::getQILL($this->get( 'formValues' ));

        // need to save qill for the smarty template
        $this->assign('qill', $qill);

        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name'), true);
        $this->addElement('text', 'description', ts('Description'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description'));
        if ( isset( $this->_id ) ) {
            $this->addDefaultButtons( ts('Update Saved Search') );
        } else {
            $this->addDefaultButtons( ts('Save Search') );
        }
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess()
    {
        // saved search form values
        $formValues = $this->controller->exportValues($this->_name);

        // save the search
        $savedSearch = new CRM_Contact_BAO_SavedSearch();
        $savedSearch->id          = $this->_id;
        $savedSearch->domain_id   = 1;   // hack for now
        $savedSearch->name        = $formValues['name'];
        $savedSearch->description = $formValues['description'];
        $savedSearch->form_values = serialize($this->get( 'formValues' ));
        $savedSearch->save();
        $this->set('ssID',$savedSearch->id);
        CRM_Core_Session::setStatus( ts('Your search has been saved as "%1".', array(1 => $formValues['name'])) );
    }
}
?>
