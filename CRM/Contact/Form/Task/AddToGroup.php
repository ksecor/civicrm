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
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * deletion.
 */
class CRM_Contact_Form_Task_AddToGroup extends CRM_Contact_Form_Task {

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
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );

        $this->assign_by_ref( 'rows', $this->_rows );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        CRM_PseudoConstant::getGroup();

        // add select for groups
        $group = array( '' => ' - any group - ') + CRM_PseudoConstant::$group;
        $this->add('select', 'group', 'Select Group', $group, true);

        $this->add('select', 'status', 'Status of the Contact', CRM_SelectValues::$groupContactStatus, true);

        $this->addDefaultButtons( 'Add To Group' );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $groupId    = $this->controller->exportValue( 'AddToGroup', 'group'  );
        $status     = $this->controller->exportValue( 'AddToGroup', 'status' );
        $contactIds = array_keys( $this->_rows );
        CRM_Contact_BAO_GroupContact::addContactsToGroup( $groupId, $contactIds, $status );
        foreach ( $rows as $id => &$row ) {
            CRM_Contact_BAO_Contact::deleteContact( $id );
        }
    }//end of function


}

?>
