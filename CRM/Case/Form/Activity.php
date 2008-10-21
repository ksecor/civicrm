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
require_once 'CRM/Custom/Form/CustomData.php';
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
             eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::preProcess();");
        }

        //handle custom data.
        $this->_cdType = CRM_Utils_Array::value( 'type', $_GET );
        $this->assign('cdType', false);
        if ( $this->_cdType ) {
            $this->assign('cdType', true);
            return CRM_Custom_Form_CustomData::preProcess( $this );
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

    
        
        //when custom data is included in this page
        if ( CRM_Utils_Array::value( "hidden_custom", $_POST ) ) {
            $this->set( 'type'    , 'Activity' );
            CRM_Custom_Form_Customdata::preProcess( $this );
            CRM_Custom_Form_Customdata::buildQuickForm( $this );
            CRM_Custom_Form_Customdata::setDefaultValues( $this );
        }

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
        if ( isset($this->_caseAction) ) {
            return eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::setDefaultValues( \$this );");
        }
    }

    public function buildQuickForm( ) 
    {
   
        //build custom data form.
        if ( $this->_cdType ) {
            return CRM_Custom_Form_CustomData::buildQuickForm( $this );
        }

        // $activityAction =
//             array( ''   => ' - select activity - ' ) + 
//             CRM_Core_PseudoConstant::ActivityType( true );

        //FIXME: hardcoded for now. We can move them to actual activity types.
        $activityAction = array('OpenCase'       => ts('Open Case'),
                                'ChangeCaseType' => ts('Change Case Type'),
                                );

        $this->add('select', 'case_action',  ts( 'Activity Type' ),  
                   array( '' => ts( '- select case action -' ) ) + $activityAction,
                   true,
                   array('onchange' => "buildCaseBlock( this.value );buildCustomData( this.value );") );

        if ( isset($this->_caseAction) ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::buildQuickForm( \$this );");
        }
                
        //need to assign custom data type and subtype to the template
        $this->assign('customDataType', 'Activity');
        $this->assign('customDataSubType',  $this->_activityTypeId );
        $this->assign('entityId',  $this->_activityId );

    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        if ( isset($this->_caseAction) ) {
            eval('$this->addFormRule' . "(array('CRM_Case_Form_Activity_{$this->_caseAction}', 'formrule'), \$this);");
        }
        $this->addFormRule( array( 'CRM_Case_Form_Activity', 'formRule'), $this );
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values, $files, &$form ) 
    {
        return true;
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
        $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
            implode(CRM_Case_BAO_Case::VALUE_SEPERATOR, $params['case_type_id'] ) .
            CRM_Case_BAO_Case::VALUE_SEPERATOR;

        if ( CRM_Utils_Array::value('is_reset_timeline', $params ) == 0 ) {
            unset($params['start_date']);
        } else if( CRM_Utils_System::isNull( $params['start_date'] ) ) {
            $params['start_date'] = date("Y-m-d");
        } else {
            $params['start_date'] = CRM_Utils_Date::format( $params['start_date'] );
        }
        
        $caseObj = CRM_Case_BAO_Case::create( $params );
        $params['case_id'] = $caseObj->id;
        
        // unset any ids
        unset($params['id']);

        // 3. call end post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::endPostProcess( \$this, \$params );");
        }

        // 4. auto populate activites

        // 5. set status
        CRM_Core_Session::setStatus( "{$params['statusMsg']}" );
    }
}


