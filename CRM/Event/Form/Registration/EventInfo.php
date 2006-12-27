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
class CRM_Event_Form_Registration_EventInfo extends CRM_Core_Form
{
    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $eventParams = array( );
        $params = array( 'id' => $this->_id );
        require_once 'CRM/Event/BAO/ManageEvent.php';
        CRM_Event_BAO_ManageEvent::retrieve($params, $eventParams);
        $this->assign('event', $eventParams);
        
        //print_r($eventParams);
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Continue'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
    }
      
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
//         $params = $id = array();
//         $id['event_id'] = $this->_id;
        
//         // store the submitted values in an array
//         $params = $this->exportValues();
//         $params['event_type_id'] = $params['event_type'];
//         $params['start_date'] = CRM_Utils_Date::format($params['start_date']);
//         $params['end_date'] = CRM_Utils_Date::format($params['end_date']);
//         require_once 'CRM/Event/BAO/ManageEvent.php';
//         if ($this->_action == CRM_Core_Action::DELETE) {
//             //CRM_Event_BAO_ManageEvent::del( $this->_id );
//         }
//         else {
//             $addParams =  CRM_Event_BAO_ManageEvent::add($params ,$id);
//         }

//         $addParams->_id =  $this->set('id',$addParams->id);   
        $this->set('id', $this->_id);
    }//end of function



    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Information and Settings');
    }
}
?>
