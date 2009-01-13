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
 * This class provides the functionality for batch profile update for contributions
 */
class CRM_Contribute_Form_Task_Batch extends CRM_Contribute_Form_Task {

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
        require_once "CRM/Core/BAO/CustomGroup.php";
        $this->_title = ts('Batch Update for Contributions') . ' - ' . CRM_Core_BAO_UFGroup::getTitle ( $ufGroupId );
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
            
            //fix to reduce size as we are using this field in grid
            if ( is_array( $field['attributes'] ) && $this->_fields[$name]['attributes']['size'] > 19 ) {
                //shrink class to "form-text-medium"
                $this->_fields[$name]['attributes']['size'] = 19;
            }
        }

        $this->_fields  = array_slice($this->_fields, 0, $this->_maxFields);
        
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Update Contribution(s)'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        
        $this->assign( 'profileTitle', $this->_title );
        $this->assign( 'componentIds', $this->_contributionIds );
        $fileFieldExists = false;
        
        //fix for CRM-2752
        require_once "CRM/Core/BAO/CustomField.php";
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Contribution' );
        foreach ( $this->_contributionIds as $contributionId ) {
            $typeId = CRM_Core_DAO::getFieldValue( "CRM_Contribute_DAO_Contribution", $contributionId, 'contribution_type_id' ); 
            foreach ( $this->_fields as $name => $field ) {
                if ( $customFieldID = CRM_Core_BAO_CustomField::getKeyID( $name ) ) {
                    $customValue = CRM_Utils_Array::value( $customFieldID, $customFields );
                    if ( ( $typeId == $customValue['extends_entity_column_value'] ) ||
                         CRM_Utils_System::isNull( $customValue['extends_entity_column_value'] ) ) {
                        CRM_Core_BAO_UFGroup::buildProfile( $this, $field, null, $contributionId );
                    }
                } else {
                    // handle non custom fields
                    CRM_Core_BAO_UFGroup::buildProfile( $this, $field, null, $contributionId );
                }
            }
        }
        
        $this->assign( 'fields', $this->_fields );
        
        // don't set the status message when form is submitted.
        $buttonName = $this->controller->getButtonName('submit');

        if ( $fileFieldExists && $buttonName != '_qf_Batch_next' ) {
            CRM_Core_Session::setStatus( "FILE type field(s) in the selected profile are not supported for Batch Update and have been excluded." );
        }

        $this->addDefaultButtons( ts( 'Update Contributions' ) );
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
        
        $defaults = array( );
        foreach ($this->_contributionIds as $contributionId) {
            $details[$contributionId] = array( );
            //build sortname
            require_once "CRM/Contribute/BAO/Contribution.php";
            $sortName[$contributionId] = CRM_Contribute_BAO_Contribution::sortName($contributionId);
            CRM_Core_BAO_UFGroup::setProfileDefaults( null, $this->_fields, $defaults, false, $contributionId, 'Contribute' );
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
        $params     = $this->exportValues( );
        $dates = array( 'receive_date',
                        'receipt_date',
                        'thankyou_date',
                        'cancel_date'
                        );
        if ( isset( $params['field'] ) ) {
            foreach ( $params['field'] as $key => $value ) {
                foreach ( $dates as $d ) {
                    if ( ! CRM_Utils_System::isNull( $value[$d] ) ) {
                        $value[$d]['H'] = '00';
                        $value[$d]['i'] = '00';
                        $value[$d]['s'] = '00';
                        $value[$d]      =  CRM_Utils_Date::format( $value[$d] );
                    } else {
                        unset( $value[$d] );
                    }   
                }
                
                $value['custom'] = CRM_Core_BAO_CustomField::postProcess( $value,
                                                                          CRM_Core_DAO::$_nullObject,
                                                                          $key,
                                                                          'Contribution' );
                
                $ids['contribution'] = $key;
                if ($value['contribution_type']) {
                    $value['contribution_type_id'] = $value['contribution_type'];
                }

                if ($value['payment_instrument']) {
                    $value['payment_instrument_id'] = $value['payment_instrument'];
                }
                
                if ($value['contribution_source']) {
                    $value['source'] = $value['contribution_source'];
                }
                
                unset($value['contribution_type']);
                unset($value['contribution_source']);
                $contribution = CRM_Contribute_BAO_Contribution::add( $value ,$ids ); 
                
                // add custom field values           
                if ( CRM_Utils_Array::value( 'custom', $value ) &&
                     is_array( $value['custom'] ) ) {
                    require_once 'CRM/Core/BAO/CustomValueTable.php';
                    CRM_Core_BAO_CustomValueTable::store( $value['custom'], 'civicrm_contribution', $contribution->id );
                }            
            }
            CRM_Core_Session::setStatus("Your updates have been saved."); 
        } else {
            CRM_Core_Session::setStatus("No updates have been saved.");
        }
    }//end of function
} 

