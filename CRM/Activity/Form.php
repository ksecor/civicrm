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
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components
 * 
 */
class CRM_Activity_Form extends CRM_Core_Form
{
    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    protected $_id;

    /**
     * The contact id, used when add / edit 
     *
     * @var int
     */
    protected $_contactId;

    /**
     * The id of the logged in user, used when add / edit 
     *
     * @var int
     */
    protected $_userId;

    /**
     *  Boolean variable to show followup if it is set to true
     *
     */
    protected $_status;

    /**
     *  Boolean variable set for differentiating between log and schedule
     *
     */
    protected $_log;

    /**
     * this variable to store parent id for the follow up activity
     *
     */
    protected $_pid;

    function preProcess( ) 
    {
        $session =& CRM_Core_Session::singleton( );
        $this->_userId = $session->get( 'userID' );

        $page =& new CRM_Contact_Page_View();
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', $page);
        
        //$this->_log = CRM_Utils_Request::retrieve( 'log', $page);
        $this->_pid  = CRM_Utils_Request::retrieve( 'pid', $page, false, null, 'GET');
        $this->_log  = CRM_Utils_Request::retrieve( 'log', $this, false, null, 'GET');
        $this->assign('log',$this->_log);
                
        $this->_contactId = $this->get('contactId');
        if ($this->_action != CRM_Core_Action::ADD) {
            $this->_id = $this->get('id');
        }
        $this->_status = CRM_Utils_Request::retrieve( 'status', $this, false, null, 'GET');
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $params   = array( );

        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            require_once(str_replace('_', DIRECTORY_SEPARATOR, $this->_BAOName) . ".php");
            eval( $this->_BAOName . '::retrieve( $params, $defaults );' );
        }

        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->assign( 'delName', $defaults['subject'] );
        }
       
        if ($this->_log) { 
            $defaults['status'] = 'Completed';
        }

        // set the default date if we are creating a new meeting/call or 
        // marking one as complete
        if ( $this->_log || ! isset( $this->_id ) ) {
            $currentDay = date("Y-m-d G:");
            $currentTime = date("s");
            // rounding of minutes
            $min = (int ) ( date("i") / 15 ) * 15;
            
            $currentDate = $currentDay . ' ' . $min . ':' . $currentTime;
            $defaults['scheduled_date_time'] = $currentDate;
        }
        
        return $defaults;

    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $button = ts('Save');
        if ($this->_action == CRM_Core_Action::VIEW) { 
            $this->freeze();
            $button = ts('Done');
        }
        
        if ($this->_status) { 
            $this->assign('status', $this->_status);
            $this->assign('pid', $this->_id);
            $this->addButtons( array(
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Done') ),
                                     )
                               );

        } else {

            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => $button,
                                         'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
        }
    }

}

?>
