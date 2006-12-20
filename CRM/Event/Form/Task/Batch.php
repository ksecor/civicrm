<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Profile/Form.php';

/**
 * This class provides the functionality for batch profile update for events
 */
class CRM_Event_Form_Task_Batch extends CRM_Event_Form_Task 
{
    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * variable to store redirect path
     *
     */
    protected $_userContext;


    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    }
  
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {    
        $ufGroupId = $this->get('ufGroupId');
        
        if ( ! $ufGroupId ) {
            CRM_Core_Error::fatal( 'ufGroupId is missing' );
        }

        $this->_title = ts('Batch Update for Events') . ' - ' . CRM_Core_BAO_UFGroup::getTitle ( $ufGroupId );
        CRM_Utils_System::setTitle( $this->_title );
        $this->addDefaultButtons( ts('Save') );
        $this->_fields  = array( );
        $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $ufGroupId, false, CRM_Core_Action::VIEW );
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Update Event Participantion(s)'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        $this->assign( 'fields', $this->_fields     );
        $this->assign( 'profileTitle', $this->_title );
        $this->assign( 'participantIds', $this->_participantIds );
        
        foreach ($this->_participantIds as $participantId) {
            foreach ($this->_fields as $name => $field ) {
                CRM_Core_BAO_UFGroup::buildProfile($this, $field, null, $participantId );
            }
        }
        
        $this->addDefaultButtons( ts( 'Update Event Participations' ) );
    }

    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        if (empty($this->_fields)) {
            return;
        }
        
        foreach ($this->_participantIds as $participantId) {
            $details[$participantId] = array( );
            
            require_once 'CRM/Event/BAO/Participant.php';
            $details[$participantId] = CRM_Event_BAO_Participant::participantDetails( $participantId );
            CRM_Core_BAO_UFGroup::setProfileDefaults( null, $this->_fields, $defaults, false, $participantId );
        }

        $this->assign('details',   $details);
        return $defaults;
    }


    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params     = $this->exportValues( );
        CRM_Core_Session::setStatus("Your updates have been saved.");
    }//end of function
}
?>
