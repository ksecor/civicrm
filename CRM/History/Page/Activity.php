<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
     * @return void
     * @access public
     *
     */
    function run()
    {
        $id  = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                            $this, true );
        require_once 'CRM/Core/DAO/ActivityHistory.php';
        $dao =& new CRM_Core_DAO_ActivityHistory( );
        $dao->id = $id;
        if ( $dao->find( true ) ) {
            // get the callback and activity id
            $callback = $dao->callback;
            $activityId = $dao->activity_id;
            $errorString = "";
            if ( strpos( $callback, '::' ) !== false ) {
                list($className, $methodName) = explode('::', $callback);
                $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
                if (! @include_once($fileName)) {
                    // we could not include the file
                    // ignore this error, CRM-751
                }
                
                // file is included so lets move on to checking if class exists
                if (!class_exists($className)) {
                    // we could not find the class
                    $errorString .= ts('Cannot find class "%1"', array(1 => $className));
                    return $this->_processError($errorString);
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
                $url = $object->$methodName($activityId, $id);
            } else {
                if ( ! function_exists( $callback ) ) {
                    $errorString .= ts('Cannot find function "%1"', array(1 => $callback) );
                    $this->_processError($errorString);
                }

                // invoke the callback method and obtain the url to redirect to 
                $url = $callback( $activityId, $id );
            }

            // redirect to url
            CRM_Utils_System::redirect($url);
        }
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
        $this->assign( 'callback'   ,
                       CRM_Utils_Request::retrieve( 'callback'   , 'String',
                                                    $this ) );
        $this->assign( 'module'     ,
                       CRM_Utils_Request::retrieve( 'module'     , 'String',
                                                    $this ) );
        $this->assign( 'activityId' ,
                       CRM_Utils_Request::retrieve( 'activity_id', 'Positive',
                                                    $this ) );
        $this->assign( 'errorString', $errorString);

        // Call the parents run method
        return parent::run();
    }
}

?>
