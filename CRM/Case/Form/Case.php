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
require_once "CRM/Activity/BAO/Activity.php";
require_once 'CRM/Custom/Form/CustomData.php';

/**
 * This class generates form components for case activity
 * 
 */
class CRM_Case_Form_Case extends CRM_Core_Form
{
    /**
     * The context
     *
     * @var string
     */
    public $_context = 'case';

    /**
     * Case Id
     */
    public $_caseId  = null;

    /**
     * Client Id
     */
    public $_currentlyViewedContactId = null;

    /**
     * Activity Type File
     */
    public $_activityTypeFile = null;
    
    /**
     * logged in contact Id
     */
    public $_currentUserId    = null;

    /**
     * activity type Id
     */
    public $_activityTypeId   = null;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_caseId        = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return true;
        }
        $this->_activityTypeId  = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this, true );

        if ( $this->_activityTypeFile = CRM_Activity_BAO_Activity::getFileForActivityTypeId($this->_activityTypeId, 'Case') ) {
            require_once "CRM/Case/Form/Activity/{$this->_activityTypeFile}.php";
            $this->assign( 'activityTypeFile', $this->_activityTypeFile );
        }

        $details  = CRM_Case_PseudoConstant::activityType( false );
       
        CRM_Utils_System::setTitle(ts('%1', array('1' => $details[$this->_activityTypeId]['label'])));
        $this->assign('activityType', $details[$this->_activityTypeId]['label']);
       
        $this->_currentlyViewedContactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

        if ( isset($this->_currentlyViewedContactId) ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_currentlyViewedContactId;
            if ( ! $contact->find( true ) ) {
                CRM_Core_Error::statusBounce( ts('Client contact does not exist: %1', array(1 => $this->_currentlyViewedContactId)) );
            }
            $this->assign( 'clientName', $contact->display_name );
        }
        
        
        $session              =& CRM_Core_Session::singleton();
        $this->_currentUserId = $session->get('userID');
        
        //when custom data is included in this page
        CRM_Custom_Form_Customdata::preProcess( $this, null, $this->_activityTypeId, 1, 'Activity' );
        eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}::preProcess( \$this );");
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return true;
        }
        CRM_Custom_Form_Customdata::setDefaultValues( $this );
        eval('$defaults = CRM_Case_Form_Activity_'. $this->_activityTypeFile. '::setDefaultValues($this);');
        return $defaults;
    }

    public function buildQuickForm( ) 
    {
        CRM_Custom_Form_Customdata::buildQuickForm( $this );
        // we don't want to show button on top of custom form
        $this->assign('noPreCustomButton', true);
    
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }
        $this->add( 'text', 'activity_subject', ts('Subject'), 
                   array_merge( CRM_Core_DAO::getAttribute( 'CRM_Activity_DAO_Activity', 'subject' ), array('maxlength' => '128') ), true);

        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}::buildQuickForm( \$this );");
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return true;
        }
        eval('$this->addFormRule' . "(array('CRM_Case_Form_Activity_{$this->_activityTypeFile}', 'formrule'), \$this);");
        $this->addFormRule( array( 'CRM_Case_Form_Case', 'formRule'), $this );
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once 'CRM/Case/BAO/Case.php';
            CRM_Case_BAO_Case::deleteCase( $this->_caseId, true );
            return;
        }
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        $params['now'] = date("Ymd");
        
        require_once 'CRM/Case/XMLProcessor/Process.php';

        // 1. call begin post process
        if ( $this->_activityTypeFile ) {
            eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}" . "::beginPostProcess( \$this, \$params );");
        }

        // 2. create/edit case
        require_once 'CRM/Case/BAO/Case.php';
        if ( CRM_Utils_Array::value('case_type_id', $params ) ) {
            $caseType = CRM_Core_OptionGroup::values('case_type');
            $params['case_type']    = $caseType[$params['case_type_id']];
            $params['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR . 
                $params['case_type_id'] . CRM_Case_BAO_Case::VALUE_SEPERATOR;
        }
        $caseObj = CRM_Case_BAO_Case::create( $params );
        $params['case_id'] = $caseObj->id;
        // unset any ids, custom data
        unset($params['id'], $params['custom']);

        // user context
        $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                      "reset=1&action=view&cid={$this->_currentlyViewedContactId}&id={$caseObj->id}" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );

        // 3. format activity custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
			$customFields = CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, $this->_activityTypeId );
			$customFields = 
                CRM_Utils_Array::crmArrayMerge( $customFields, 
                                                CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, 
                                                                                     null, null, true ) );
	        $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
	                                                                   $customFields,
	                                                                   $this->_activityId,
	                                                                   'Activity' );
        }

        // 4. call end post process
        if ( $this->_activityTypeFile ) {
            eval("CRM_Case_Form_Activity_{$this->_activityTypeFile}" . "::endPostProcess( \$this, \$params );");
        }

        // 5. auto populate activites

        // 6. set status
        CRM_Core_Session::setStatus( "{$params['statusMsg']}" );
    }
}
