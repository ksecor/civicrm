<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/StateMachine.php';

/**
 * State machine for managing different states of the Import process.
 *
 */
class CRM_NewImport_StateMachine extends CRM_Core_StateMachine {

    /**
     * class constructor
     *
     * @param object  CRM_NewImport_Controller
     * @param int     $action
     *
     * @return object CRM_NewImport_StateMachine
     */
    function __construct( &$controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );
        
        $this->_pages = array(
                              'CRM_NewImport_Form_DataSource' => null,
                              'CRM_NewImport_Form_MapField' => null,
                              'CRM_NewImport_Form_Preview' => null,
                              'CRM_NewImport_Form_Summary' => null,
                              );
        
        $this->addSequentialPages( $this->_pages, $action );
    }

}