<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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
            if ( ! CRM_Utils_System::validCallback( $dao->callback ) ) {
                CRM_Core_Error::statusBounce( ts( "Could not find callback %1", array( 1 => $dao->callback ) ) );
            }
            $callback = $dao->callback;
            $activityId = $dao->activity_id;
            if ( strpos( $callback, '::' ) !== false ) {
                list($className, $methodName) = explode('::', $callback);

                // instantiate the class
                $object =& new $className();

                // invoke the callback method and obtain the url to redirect to
                $url = $object->$methodName($activityId, $id);
            } else {
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
