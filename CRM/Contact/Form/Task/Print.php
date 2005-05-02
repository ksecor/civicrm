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
class CRM_Contact_Form_Task_Print extends CRM_Contact_Form_Task {

    /**
     * class constructor
     *
     */
    function __construct( $name, $state, $mode = self::MODE_NONE ) {
        parent::__construct($name, $state, $mode);
    }

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        /*
         * initialize the task and row fields
         */
        parent::preProcess();

        // parent preprocess populates the variable $_rows
        // with contact_id and display name
        // since we need more data we'll populate it with address, city, state, postal, country, email, phone

        // get the ids
        $ids = array_keys($this->_rows);
        $addressDetail = CRM_Contact_BAO_Contact::getAddress($ids);
        
        // need to save address detail for print template
        $template = CRM_Core_Smarty::singleton( );
        $template->assign_by_ref('printRows', $addressDetail);
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
        //
        // just need to add a javacript to popup the window for printing
        // 
        $this->addDefaultButtons('Print Contact List');
        // get the print button and add the javascript
        //$printButton = $this->getElement('Print');
        $this->updateElementAttr('_qf_Print_next', array('onclick' => 'window.print()'));
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess()
    {
        // redirect to the main search page after printing is over
    }
}
?>