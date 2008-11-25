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
    public $_id = null;

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
     * activity type Id
     */
    public $_actTypeId = null;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_actTypeId  = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this, true );

        if ( $this->_caseAction = CRM_Case_BAO_Case::getFileForActivityTypeId($this->_actTypeId) ) {
            require_once "CRM/Case/Form/Activity/{$this->_caseAction}.php";
            $this->assign( 'caseAction', $this->_caseAction );
        }

        $details  = CRM_Case_PseudoConstant::activityType( false );
       
        CRM_Utils_System::setTitle(ts('%1', array('1' => $details[$this->_actTypeId]['label'])));
        $this->assign('activityType', $details[$this->_actTypeId]['label']);
        
        $this->_clientId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this );

        if ( isset($this->_clientId) ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_clientId;
            if ( ! $contact->find( true ) ) {
                CRM_Core_Error::statusBounce( ts('Client contact does not exist: %1', array(1 => $this->_clientId)) );
            }
            $this->assign( 'clientName', $contact->display_name );
        }
        
        $this->_id  = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        $session    =& CRM_Core_Session::singleton();
        $this->_uid = $session->get('userID');
        
        //when custom data is included in this page
        $this->set( 'type'    , 'Activity' );
        $this->set( 'subType' , $this->_actTypeId );
        CRM_Custom_Form_Customdata::preProcess( $this );

        eval("CRM_Case_Form_Activity_{$this->_caseAction}::preProcess( \$this );");
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
        CRM_Custom_Form_Customdata::setDefaultValues( $this );
        eval('$defaults = CRM_Case_Form_Activity_'. $this->_caseAction. '::setDefaultValues($this);');
        return $defaults;
    }

    public function buildQuickForm( ) 
    {
        CRM_Custom_Form_Customdata::buildQuickForm( $this );
        // we don't want to show button on top of custom form
        $this->assign('noPreCustomButton', true);
   
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        eval("CRM_Case_Form_Activity_{$this->_caseAction}::buildQuickForm( \$this );");
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        eval('$this->addFormRule' . "(array('CRM_Case_Form_Activity_{$this->_caseAction}', 'formrule'), \$this);");
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
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        $params['now'] = date("Ymd");
        
        require_once 'CRM/Case/XMLProcessor/Process.php';

        // 1. call begin post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::beginPostProcess( \$this, \$params );");
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
                                      "reset=1&action=view&cid={$this->_clientId}&id={$caseObj->id}" );
        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );

        // 3. format activity custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            $customData = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) { 
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Activity', null );
                }
            }
           
            if ( !empty($customData) ) {
                $params['custom'] = $customData;
            }
        }
        
        // 4. call end post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::endPostProcess( \$this, \$params );");
        }

        // 5. auto populate activites

        // 6. set status
        CRM_Core_Session::setStatus( "{$params['statusMsg']}" );
    }
}
