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
 * We use QFC for both single page and multi page wizards. We want to make
 * creation of single page forms as easy and as seamless as possible. This
 * class is used to optimize and make single form pages a relatively trivial
 * process
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Controller.php';

class CRM_Controller_Simple extends CRM_Controller {

    /**
     * constructor
     *
     * @param string path   the class Path of the form being implemented
     * @param string name   the descriptive name for the page
     * @param int    mode   the mode that the form will operate on
     *
     * @return object
     * @access public
     */
    function __construct($path, $name, $mode) {
        // by definition a single page is modal :)
        parent::__construct( $name, true );

        $this->_stateMachine = new CRM_StateMachine( $this );

        $params = array($path);


        // CRM_Error::debug_var("params", $params);
        $this->_stateMachine->addSequentialStates($params, $mode);

        $this->addPages( $this->_stateMachine, $mode );
        $this->addDefault( );

    }
}

?>