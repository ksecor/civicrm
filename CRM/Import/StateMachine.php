<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
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
class CRM_Import_StateMachine extends CRM_Core_StateMachine {

    /**
     * class constructor
     *
     * @param object  CRM_Import_Controller
     * @param int     $action
     *
     * @return object CRM_Import_StateMachine
     */
    function __construct( &$controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );
        
        $this->_pages = array(
                              'CRM_Import_Form_UploadFile' => null,
                              'CRM_Import_Form_MapField' => null,
                              'CRM_Import_Form_Preview' => null,
                              'CRM_Import_Form_Summary' => null,
                              );
        
        $this->addSequentialPages( $this->_pages, $action );
    }

}

?>
