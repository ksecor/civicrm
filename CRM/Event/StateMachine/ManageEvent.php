<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/StateMachine.php';

/**
 * State machine for managing different states of the EventWizard process.
 *
 */
class CRM_Event_StateMachine_ManageEvent extends CRM_Core_StateMachine {

    /**
     * class constructor
     *
     * @param object  CRM_Event_EventWizard_Controller
     * @param int     $action
     *
     * @return object CRM_Event_EventWizard_StateMachine
     */
    function __construct( $controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );
        
        $this->_pages = array(
                              'CRM_Event_Form_ManageEvent_EventInfo'    => null,
                              'CRM_Event_Form_ManageEvent_Location'     => null,
                              'CRM_Event_Form_ManageEvent_Fee'          => null,
                              'CRM_Event_Form_ManageEvent_Registration' => null,
                              'CRM_Friend_Form_Event'                   => null
                              );
        
        $this->addSequentialPages( $this->_pages, $action );
    }

}


