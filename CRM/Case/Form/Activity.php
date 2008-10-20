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

require_once "CRM/Core/Form.php";

/**
 * This class generates form components for case activity
 * 
 */
class CRM_Case_Form_Activity extends CRM_Core_Form
{

    /**
     * Client Id
     */
    public $_clientId = null;

    /**
     * Case Activity Action
     */
    public $_caseAction = null;
    
    /**
     * logged in contact Id
     */
    public $_uid = null;
    
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_caseAction = CRM_Utils_Array::value( 'caseaction', $_GET );
        $this->assign( 'caseAction', $this->_caseAction );
        
        if ( $this->_caseAction ) {
            require_once "CRM/Case/Form/Activity/{$this->_caseAction}.php";
        }

        $this->_clientId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

        if ( $this->_clientId ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_clientId;
            if ( ! $contact->find( true ) ) {
                CRM_Core_Error::statusBounce( ts('client contact does not exist: %1', array(1 => $this->_clientId)) );
            }
        }
        
        $session    =& CRM_Core_Session::singleton();
        $this->_uid = $session->get('userID');    
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
    }

    public function buildQuickForm( ) 
    {
        switch ( $this->_caseAction ) {
        case "OpenCase"  :
            return CRM_Case_Form_Activity_OpenCase::buildQuickForm( $this );
        case "ChangeCase":
            return CRM_Case_Form_Activity_ChangeCase::buildQuickForm( $this );
        }

        // FIXME: hardcoded for now. We can move them to actual activity types.
        $activityAction = array('OpenCase'       => ts('Open Case'),
                                'ChangeCaseType' => ts('Change Case Type'),
                                );

        $this->add('select', 'case_action',  ts( 'Activity Type' ),  
                   array( '' => ts( '- select case action -' ) ) + $activityAction,
                   true,
                   array('onchange' => "buildCaseBlock( this.value );") );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        switch ( $this->_caseAction ) {
        case "OpenCase"  :
            CRM_Case_Form_Activity_OpenCase::postProcess( $this, $params );
            break;
        case "ChangeCase":
            CRM_Case_Form_Activity_ChangeCase::postProcess( $this, $params );
            break;
        }

        // auto populate activites
        
    }
}


