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
class CRM_Event_Form_ManageEvent_EventInfo extends CRM_Core_Form
{


    function preProcess( ) {
        $this->_id      = $this->get( 'id' );
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
            require_once 'CRM/Event/BAO/ManageEvent.php';
            CRM_Event_BAO_ManageEvent::retrieve($params, $defaults);
            
        }
        return $defaults;
    }

    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->applyFilter('__ALL__', 'trim');

        $this->add('text','title',ts('Title'));
        require_once 'CRM/Core/OptionGroup.php';
        $event = CRM_Core_OptionGroup::values('event_type');

        $this->add('select','event_type',ts('Event Type'),array('' => ts('- select -')) + $event);
        
        $this->add('textarea','summary',ts('Event Summary'), array("rows"=>4,"cols"=>60));
        
        $this->add('textarea','description',ts('Full description'), array("rows"=>4,"cols"=>60));
        
        $this->addElement('checkbox', 'is_public', ts('Public?') );
        
        $this->add('date', 'start_date', ts('Start Date and Time'), CRM_Core_SelectValues::date('datetime'));
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date and Time'), CRM_Core_SelectValues::date('datetime'));
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
              
        $this->add('text','max_participants', ts('Max Number of Participants'));

        $this->add('text','event_full_text', ts('Event full text'));
        
        $this->addElement('checkbox', 'is_active', ts('Enabled?') );
        
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
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
        $params = $id = array();
        $id['event_id'] = $this->_id;
        
        // store the submitted values in an array
        $params = $this->exportValues();
        $params['event_type_id'] = $params['event_type'];
        $params['start_date'] = CRM_Utils_Date::format($params['start_date']);
        $params['end_date'] = CRM_Utils_Date::format($params['end_date']);
        require_once 'CRM/Event/BAO/ManageEvent.php';
        if ($this->_action == CRM_Core_Action::DELETE) {
            //CRM_Event_BAO_ManageEvent::del( $this->_id );
        }
        else {
            $addParams =  CRM_Event_BAO_ManageEvent::add($params ,$id);
        }

        $addParams->_id =  $this->set('id',$addParams->id);   
       
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
