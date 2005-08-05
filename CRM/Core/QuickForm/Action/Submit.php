<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * Redefine the submit action.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/QuickForm/Action.php';

class CRM_Core_QuickForm_Action_Submit extends CRM_Core_QuickForm_Action {

    /**
     * class constructor
     *
     * @param object $stateMachine reference to state machine object
     *
     * @return object
     * @access public
     */
    function __construct( &$stateMachine ) {
        parent::__construct( $stateMachine );
    }

    /**
     * Processes the request. 
     *
     * @param  object    $page       CRM_Core_Form the current form-page
     * @param  string    $actionName Current action name, as one Action object can serve multiple actions
     *
     * @return void
     * @access public
     */
    function perform(&$page, $actionName) {
        $page->isFormBuilt() or $page->buildForm();

        $pageName =  $page->getAttribute('name');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        $data['valid'][$pageName]  = $page->validate();
        /**
        CRM_Core_Error::debug( 'data', $data );
        CRM_Core_Error::debug( 'data', $page );
        **/
        // Modal form and page is invalid: don't go further
        if ($page->controller->isModal() && !$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        // the page is valid, process it before we jump to the next state
        $page->postProcess( );
    }

}

?>