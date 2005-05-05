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
 * addition of contacts to groups.
 */
class CRM_Contact_Form_Task_AddToGroup extends CRM_Contact_Form_Task {
    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     */
    protected $_groupId;

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

        $this->_context = CRM_Request::retrieve( 'context', $this );
        $this->_groupId = CRM_Request::retrieve( 'amtgID' , $this );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        // add select for groups
        $group = array( '' => ' - any group - ') + CRM_PseudoConstant::group( );
        $groupElement = $this->add('select', 'group_id', 'Select Group', $group, true);

        if ( $this->_context === 'amtg' ) {
            $groupElement->freeze( );

            // also set the group title
            $groupValues = array( 'id' => $this->_groupId, 'title' => $group[$this->_groupId] );
            $this->assign_by_ref( 'group', $groupValues );
        }

        $this->addDefaultButtons( 'Add To Group' );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        if ( $this->_context === 'amtg' ) {
            $defaults['group_id'] = $this->_groupId;
        }
        return $defaults;
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $groupId    = $this->controller->exportValue( 'AddToGroup', 'group_id'  );
        
        CRM_Contact_BAO_GroupContact::addContactsToGroup( $this->_contactIds, $groupId );

    }//end of function


}

?>