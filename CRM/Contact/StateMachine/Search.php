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

require_once 'CRM/Core/StateMachine.php';

class CRM_Contact_StateMachine_Search extends CRM_Core_StateMachine {

    /**
     * class constructor
     */
    function __construct( $controller, $mode = CRM_Core_Form::MODE_NONE ) {
        parent::__construct( $controller, $mode );

        $task = $this->taskName( $controller, 'Search' );

        $this->_pages = array(
                              'CRM_Contact_Form_Search',
                              $task,
                              );

        switch ($task) {
        case 'CRM_Contact_Form_Task_AddToGroup':
        case 'CRM_Contact_Form_Task_AddToTag':
        case 'CRM_Contact_Form_Task_SaveSearch':
        case 'CRM_Contact_Form_Task_SaveSearch_Update':
            array_push($this->_pages, 'CRM_Contact_Form_Task_Result');
            break;
        }

        $this->addSequentialPages( $this->_pages, $mode );
    }

    /**
     * Determine the form name based on the action. This allows us
     * to avoid using  conditional state machine, much more efficient
     * and simpler
     *
     * @param CRM_Core_Controller $controller the controller object
     *
     * @return string the name of the form that will handle the task
     * @access protected
     * @static
     */
    static function taskName( $controller, $formName = 'Search' ) {
        // total hack, first check POST vars and then check controller vars
        $value = CRM_Utils_Array::value( 'task', $_POST );
        if ( ! isset( $value ) ) {
            $value = $controller->exportValue( $formName, 'task' );
        }

        switch ( $value ) {
        case  CRM_Contact_Task::GROUP_CONTACTS:
            $task = 'CRM_Contact_Form_Task_AddToGroup';
            break;

        case CRM_Contact_Task::DELETE_CONTACTS:
            $task = 'CRM_Contact_Form_Task_Delete';
            break;

        case CRM_Contact_Task::SAVE_SEARCH:
            $task = 'CRM_Contact_Form_Task_SaveSearch';
            break;

        case CRM_Contact_Task::SAVE_SEARCH_UPDATE:
            $task = 'CRM_Contact_Form_Task_SaveSearch_Update';
            break;

        case CRM_Contact_Task::TAG_CONTACTS:
            $task = 'CRM_Contact_Form_Task_AddToTag';
            break;

        case CRM_Contact_Task::PRINT_CONTACTS:
            $task = 'CRM_Contact_Form_Task_Print';
            break;

        default:
            $task = 'CRM_Contact_Form_Task_Print';
            break;
        }

        return $task;
    }
}

?>