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

        if ( !$this->_caseAction ) {
            $this->_caseAction = $this->get('caseAction');
        } else {
            $this->set('caseAction', $this->_caseAction);
        }

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
        // FIXME: hardcoded for now. We can move them to actual activity types.
        $activityAction = array('OpenCase'   => ts('Open Case'),
                                'ChangeCase' => ts('Change Case Type'),
                                );

        $this->add('select', 'case_action',  ts( 'Activity Type' ),  
                   array( '' => ts( '- select case action -' ) ) + $activityAction,
                   true,
                   array('onchange' => "buildCaseBlock( this.value );") );

        if ( isset($this->_caseAction) ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::buildQuickForm( \$this );");
        }
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
        $params['now'] = date("Ymd");
        
        // 1. call begin post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::beginPostProcess( \$this, \$params );");
        }

        // 2. create/edit case
        require_once 'CRM/Case/BAO/Case.php';
        $params['start_date'  ] = CRM_Utils_Date::format( $params['now'] );
        $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
            implode(CRM_Case_BAO_Case::VALUE_SEPERATOR, $params['case_type_id'] ) .
            CRM_Case_BAO_Case::VALUE_SEPERATOR;
        
        $caseObj = CRM_Case_BAO_Case::create( $params );
        $params['case_id'] = $caseObj->id;

        // 3. call end post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::endPostProcess( \$this, \$params );");
        }

        // 4. auto populate activites

        // 5. set status
        require_once "CRM/Core/Session.php";
        CRM_Core_Session::setStatus( "{$params['statusMsg']}" );
    }
}


