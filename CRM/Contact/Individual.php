<?php
/**
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
 * An Individual is a contact that represents a human being.
 * The Individual class handles the different actions that need to performed 
 * on an individual. actions are
 *
 * - add
 * - view (single or multiple)
 * - edit
 * - search
 * - delete
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Base.php';
require_once 'CRM/Controller/Simple.php';
require_once 'CRM/Contact/BAO/Contact_Individual.php';

class CRM_Contact_Individual extends CRM_Base
{
    /**
     * Simple Controller
     *
     * The controller which will handle the display and processing of this page.
     *
     * @access protected
     */
    protected $_controller;


    /**
     * Constructor
     *
     * The constructor calls the constructor of the base class.
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function __construct()
    {
        parent::__construct();
    }



    /**
     * Run.
     *
     * The heart of the callback processing is done by this method.
     * An individual object will create a form for display.
     * forms are of different type and have different operations.
     *
     * @param string $formName    name of the form processing this action
     * @param string $formLabel   label for the above form
     * @param int    $mode        mode of operation.
     * @param string $userContext where should we go when done
     * @param int    $id          id of the contact.
     *
     * @returns none.
     * @access public
     */
    function run( $formName    = 'CRM_Contact_Form_Individual',
                  $formLabel   = 'Contact Individual Page'    ,
                  $mode        = CRM_Form::MODE_NONE,
                  $userContext = 'crm/contact/add?reset=1',
                  $id          = 0 ) {
        // CRM_Error::le_method();

        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();

        // store the return url. Note that this is typically computed by the framework at runtime
        // based on multiple things (typically where the link was clicked from / http_referer
        // since we are just starting and figuring out navigation, we are hard coding it here
        $session->pushUserContext( $config->httpBase . $userContext );

        $this->_controller = new CRM_Controller_Simple( $formName, $formLabel, $mode );
        $this->_controller->process();
        $this->_controller->run();
    }

    /**
     * getContent
     *
     * returns the content which is stored in the controller.
     *
     * @protected object
     */
    function getContent()
    {
        return $this->_controller->getContent();
    }
}
?>