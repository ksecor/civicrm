<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration extends CRM_Core_Form
{

    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @protected
     */
    protected $_params;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        //print_r($this);
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false );
        
        $this->_values = $this->get( 'values' );
        
        if ( ! $this->_values ) {
            // get all the values from the dao object
            $this->_values = array( );
            
            //retrieve event information
            $params = array( 'id' => $this->_id );
            require_once 'CRM/Event/BAO/ManageEvent.php';
            CRM_Event_BAO_ManageEvent::retrieve($params, $this->_values['event']);
            
            //retrieve custom information
            require_once 'CRM/Core/BAO/CustomOption.php'; 
            CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event', $this->_id, $this->_values['event']['custom'] );
            
            $this->_values['event']['feeLevel'] = CRM_Core_BAO_CustomOption::getCustomOption( $this->_id, true, 'civicrm_event' );
            
            $params = array( 'event_id' => $this->_id );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve($params, $this->_values['event_page']);
            
            $this->set( 'values', $this->_values );
        }
    }

}
?>
