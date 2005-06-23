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
 * Dummy page for details of activity
 *
 */
class CRM_History_Page_Activity extends CRM_Core_Page {
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
        // get the callback and activity id
        $callback = CRM_Utils_Request::retrieve( 'callback', $this );
        $activityId = CRM_Utils_Request::retrieve('activity_id', $this );
        $errorString = "";
        list($className, $methodName) = explode('::', $callback);
        $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    
        if (!include_once($fileName)) {
            // we could not include the file
            $errorString .= ts('Cannot include file "%1" corresponding to class "%2". Please check include_path', array(1 => $fileName, 2 => $className));
            $this->_processError($errorString);
        }

        // file is included so lets move on to checking if class exists
        if (!class_exists($className)) {
            // we could not find the class
            $errorString .= ts('Cannot find class "%1"', array(1 => $className));
            $this->_processError($errorString);
        }

        // instantiate the class
        $object =& new $className();

        // class exists so lets move on to checking if method exists
        if (!method_exists($object, $methodName)) {
            // we could not find the method
            $errorString .= ts('Cannot find method "%1" for class "%2"', array(1 => $methodName, 2 => $className));
            $this->_processError($errorString);
        }
        
        // invoke the callback method and obtain the url to redirect to
        $url = $object->$methodName($activityId);
        // redirect to url
        CRM_Utils_System::redirect($url);
    }

    /**
     * Create the error page (since we had some problems invoking the callback
     *
     * @param string $errorString
     * @return none
     * @access private
     *
     */
    private function _processError($errorString) {
        $this->assign( 'callback'   , CRM_Utils_Request::retrieve( 'callback'   , $this ) );
        $this->assign( 'module'     , CRM_Utils_Request::retrieve( 'module'     , $this ) );
        $this->assign( 'activityId' , CRM_Utils_Request::retrieve( 'activity_id', $this ) );
        $this->assign( 'errorString', $errorString);

        // Call the parents run method
        parent::run();
    }
}

?>
