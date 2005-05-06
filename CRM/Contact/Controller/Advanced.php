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

require_once 'CRM/Core/Controller.php';
require_once 'CRM/Core/Session.php';

class CRM_Contact_Controller_Advanced extends CRM_Core_Controller {

    /**
     * class constructor
     */
    function __construct( $title = null, $mode = CRM_Core_Form::MODE_NONE, $modal = true ) {
        parent::__construct( $title, $modal );

        $this->_stateMachine = new CRM_Contact_StateMachine_Advanced( $this, $mode );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $mode );

        // add all the actions
        $config = CRM_Core_Config::singleton( );
        $this->addActions( );
    }

    /**
     * function to destroy session scope for common search values (CSV);
     *
     * @access public
     * @return void
     */
    public function reset()
    {
        $session = CRM_Core_Session::singleton( );
        $session->resetScope(CRM_Contact_Form_Search::SESSION_SCOPE_SEARCH);
        parent::reset();
    }
}

?>