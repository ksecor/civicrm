<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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
     * maximum profile fields that will be displayed
     *
     */
    protected $_maxFields = 9;

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

        require_once "CRM/Core/BAO/UFGroup.php";
        $this->_title = ts('Batch Update for Events') . ' - ' . CRM_Core_BAO_UFGroup::getTitle ( $ufGroupId );
        CRM_Utils_System::setTitle( $this->_title );
        $this->addDefaultButtons( ts('Save') );
        $this->_fields  = array( );
        $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $ufGroupId, false, CRM_Core_Action::VIEW );

        // remove file type field and then limit fields
        foreach ($this->_fields as $name => $field ) {
            $type = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $field['title'], 'data_type', 'label' );
            if ( $type == 'File' ) {                        
                $fileFieldExists = true;
                unset($this->_fields[$name]);
            }
        }

        $this->_fields  = array_slice($this->_fields, 0, $this->_maxFields);

        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Update Participant(s)'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        
        $this->assign( 'profileTitle', $this->_title );
        $this->assign( 'componentIds', $this->_participantIds );
        $fileFieldExists = false;
        
        //fix for CRM-2752
        require_once "CRM/Core/BAO/CustomField.php";
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Participant' );
        foreach ( $this->_participantIds as $participantId ) {
            $roleId = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Participant", $participantId, 'role_id' ); 
            foreach ( $this->_fields as $name => $field ) {
                if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID( $name ) ) {
                    $customValue = CRM_Utils_Array::value( $customFieldID, $customFields );
                    if ( ( $roleId == $customValue['extends_entity_column_value'] ) ||
                         CRM_Utils_System::isNull( $customValue['extends_entity_column_value'] ) ) {
                        CRM_Core_BAO_UFGroup::buildProfile( $this, $field, null, $participantId );
                    }
                } else {
                    // handle non custom fields
                    CRM_Core_BAO_UFGroup::buildProfile( $this, $field, null, $participantId );
                }
            }
        }
        
        $this->assign( 'fields', $this->_fields     );

        // don't set the status message when form is submitted.
        $buttonName = $this->controller->getButtonName('submit');

        if ( $fileFieldExists && $buttonName != '_qf_Batch_next' ) {
            CRM_Core_Session::setStatus( "FILE type field(s) in the selected profile are not supported for Batch Update and have been excluded." );
        }
        
        $this->addDefaultButtons( ts( 'Update Participant(s)' ) );
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
            CRM_Core_BAO_UFGroup::setProfileDefaults( null, $this->_fields, $defaults, false, $participantId, 'Event');
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
        $dates = array( 'participant_register_date' );
        if ( isset( $params['field'] ) ) {
            foreach ( $params['field'] as $key => $value ) {
                foreach ( $dates as $d ) {
                    if ( ! CRM_Utils_System::isNull( $value[$d] ) ) {
                        $value[$d]['H'] = '00';
                        $value[$d]['i'] = '00';
                        $value[$d]['s'] = '00';
                        $value[$d]      =  CRM_Utils_Date::format( $value[$d] );
                    }   
                }
                
                //check for custom data
                $value['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
                                                                          CRM_Core_DAO::$_nullObject,
                                                                          $key,
                                                                          'Participant' );

                $value['id'] = $key;
                if ( $value['participant_register_date'] ) {
                    $value['register_date'] = $value['participant_register_date'];
                } 
                
                if ( $value['participant_role_id'] ) {
                    $value['role_id'] = $value['participant_role_id'];
                } 
                
                if ( $value['participant_status_id'] ) {
                    $value['status_id'] = $value['participant_status_id'];
                } 
                
                if ( $value['participant_source'] ) {
                    $value['source'] = $value['participant_source'];
                }            
                unset($value['participant_register_date']);
                unset($value['participant_status_id']);
                unset($value['participant_source']);
                
                CRM_Event_BAO_Participant::create( $value );  
            }
            CRM_Core_Session::setStatus("Your updates have been saved.");  
        } else {
            CRM_Core_Session::setStatus("No updates have been saved.");
        }
    }//end of function
}

