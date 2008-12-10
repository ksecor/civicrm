<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Profile/Form.php';

/**
 * This class provides the functionality for batch profile update
 */
class CRM_Contact_Form_Task_Batch extends CRM_Contact_Form_Task 
{

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * maximum contacts that should be allowed to update
     *
     */
    protected $_maxContacts = 100;

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
        $this->_title = ts('Batch Update') . ' - ' . CRM_Core_BAO_UFGroup::getTitle ( $ufGroupId );
        CRM_Utils_System::setTitle( $this->_title );
        
        $this->addDefaultButtons( ts('Save') );
        $this->_fields  = array( );
        $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $ufGroupId, false, CRM_Core_Action::VIEW );

        // remove file type field and then limit fields
        $fileFieldExists = false;
        foreach ($this->_fields as $name => $field ) {
            if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($name) && 
                 $this->_fields[$name]['data_type'] == 'File' ) {                        
                $fileFieldExists = true;
                unset($this->_fields[$name]);
            }
        }

        $this->_fields  = array_slice($this->_fields, 0, $this->_maxFields);
        
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Update Contact(s)'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        
        $this->assign( 'profileTitle', $this->_title );
        $this->assign( 'componentIds', $this->_contactIds );
        
        foreach ($this->_contactIds as $contactId) {
            foreach ($this->_fields as $name => $field ) {
                CRM_Core_BAO_UFGroup::buildProfile($this, $field, null, $contactId );
            }
        }
        
        $this->assign( 'fields', $this->_fields );

        // don't set the status message when form is submitted.
        $buttonName = $this->controller->getButtonName('submit');

        if ( $fileFieldExists && $buttonName != '_qf_BatchUpdateProfile_next' ) {
            CRM_Core_Session::setStatus( "FILE type field(s) in the selected profile are not supported for Batch Update and have been excluded." );
        }

        $this->addDefaultButtons( ts( 'Update Contacts' ) );
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
        
        foreach ($this->_contactIds as $contactId) {
            $details[$contactId] = array( );

            //build sortname
            $sortName[$contactId] = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                 $contactId,
                                                                 'sort_name' );
            
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactId, $this->_fields, $defaults, false );
        }
        
        $this->assign('sortName', $sortName);

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
        $params = $this->exportValues( );

        $ufGroupId = $this->get( 'ufGroupId' );
        foreach( $params['field'] as $key => $value ) {
            CRM_Contact_BAO_Contact::createProfileContact($value, $this->_fields, $key, null, $ufGroupId );
        }
        
        CRM_Core_Session::setStatus("Your updates have been saved.");
    }//end of function
}

