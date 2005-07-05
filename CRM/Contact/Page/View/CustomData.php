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

require_once 'CRM/Contact/Page/View.php';

/**
 * Page for displaying custom data
 *
 */
class CRM_Contact_Page_View_CustomData extends CRM_Contact_Page_View {
    /**
     * the id of the object being viewed (note/relationship etc)
     *
     * @int
     * @access protected
     */
    protected $_groupId;

    /**
     * class constructor
     *
     * @param int $groupId - group Id of the custom group
     *
     * @return CRM_Contact_Page_View_CustomData
     */
    public function __construct($groupId)
    {
        CRM_Core_Error::le_method();
        CRM_Core_Error::debug_var('groupId', $groupId);
        $this->_groupId = $groupId;
        parent::__construct();
    }



    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     *
     * @access public
     * @param object $page - the view page which created this one 
     * @return none
     * @static
     *
     */
    function run( )
    {
        $this->preProcess( );

        $controller =& new CRM_Core_Controller_Simple('CRM_Contact_Form_CustomData', 'Custom Data', $this->_action);
        $controller->setEmbedded(true);

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();

        $doneURL = 'civicrm/contact/view/cd';

        if ($this->_groupId) {
            $doneURL .= "/$this->_groupId";
        }

        $session->pushUserContext(CRM_Utils_System::url($doneURL, 'action=browse'));
        $controller->set('tableId'   , $this->_contactId );
        $controller->set('groupId'   , $this->_groupId);
        $controller->set('entityType', CRM_Contact_BAO_Contact::getContactType( $this->_contactId ) );
        $controller->process();
        $controller->run();

        return parent::run();
    }

}
?>