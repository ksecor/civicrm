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

class CRM_Contact_StateMachine_Advanced extends CRM_Core_StateMachine {

    /**
     * class constructor
     */
    function __construct( $controller, $mode = CRM_Core_Form::MODE_NONE ) {
        parent::__construct( $controller, $mode );

        $task = CRM_Contact_StateMachine_Search::taskName( $controller );

        $this->_pages = array(
                              'CRM_Contact_Form_Search_Advanced',
                              $task,
                              );

        switch ($task) {
        case 'CRM_Contact_Form_Task_AddToGroup':
        case 'CRM_Contact_Form_Task_AddToTag':
        case 'CRM_Contact_Form_Task_SaveSearch':
            array_push($this->_pages, 'CRM_Contact_Form_Task_Result');
            break;
        }

        $this->addSequentialPages( $this->_pages, $mode );
    }

}

?>