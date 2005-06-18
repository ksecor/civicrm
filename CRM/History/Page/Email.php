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

require_once 'CRM/Core/Page.php';

/**
 * Dummy page for details of Email
 *
 */
class CRM_History_Page_Email extends CRM_Core_Page {
    /**
     * Run the page.
     *
     * This method is called after the page is created.
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run()
    {

        CRM_Core_Error::le_method();

        // get the callback, module and activity id
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse');
        $id     = CRM_Utils_Request::retrieve('id');

        $dao = new CRM_Core_DAO_EmailHistory();
        $dao->id = $id;
        if ( $dao->find(true) ) {
            $this->assign('sentDate', $dao->sent_date);
            $this->assign('subject', $dao->subject);
            $this->assign('message', $dao->message);

            // get email details and show on page
            // Call the parents run method
        }
        parent::run();
    }
}
?>