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
     * activity type Id
     */
    protected $_actTypeId = null;
    
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    {        
        $this->_actTypeId  = CRM_Utils_Request::retrieve( 'atype', 'Positive', $this );

        if ( $this->_actTypeId ) {
            $actName  = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Category', $this->_actTypeId, 'name' );
            $actLabel = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Category', $this->_actTypeId, 'label' );
            CRM_Utils_System::setTitle(ts('%1', array('1' => $actLabel)));
        }

        if ( $actName ) {
            $this->_caseAction = trim(str_replace(' ', '', $actName));
        }

        global $civicrm_root;
        if ( !file_exists(rtrim($civicrm_root, '/') . "/CRM/Case/Form/Activity/{$this->_caseAction}.php") ) {
            CRM_Core_Error::fatal(ts('File not found to handle this activity type id.'));
        } else {
            require_once "CRM/Case/Form/Activity/{$this->_caseAction}.php";
        }

        $this->assign( 'caseAction', $this->_caseAction );
        
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
        $this->set( 'type'    , 'Case' );
        CRM_Custom_Form_Customdata::preProcess( $this );
        CRM_Custom_Form_Customdata::buildQuickForm( $this );
        CRM_Custom_Form_Customdata::setDefaultValues( $this );
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
        return eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::setDefaultValues( \$this );");
    }

    public function buildQuickForm( ) 
    {
   
        eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::buildQuickForm( \$this );");
                
        $this->addButtons(array( 
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
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

        // 2. format custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            $params['hidden_custom'] = 1;
                        
            $customData = array( );
            foreach ( $params as $key => $value ) {
                if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) { 
                    $params[$key] = $value;
                    CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                                 $value, 'Case', null, $this->_id );
                }
            }
           
            if ( !empty($customData) ) {
                $params['custom'] = $customData;
            }
            
            //special case to handle if all checkboxes are unchecked
            $customFields = CRM_Core_BAO_CustomField::getFields( 'Case' );
            
            if ( !empty($customFields) ) {
                foreach ( $customFields as $k => $val ) {
                    if ( in_array ( $val[3], array ('CheckBox', 'Multi-Select', 'Radio') ) &&
                         ! CRM_Utils_Array::value( $k, $params['custom'] ) ) {
                        CRM_Core_BAO_CustomField::formatCustomField( $k, $params['custom'],
                                                                     '', 'Case', null, $this->_id );
                    }
                }
            }
        }

        // 3. create/edit case
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
        unset($params['id'], $params['custom']);

        // 4. call end post process
        if ( $this->_caseAction ) {
            eval("CRM_Case_Form_Activity_{$this->_caseAction}" . "::endPostProcess( \$this, \$params );");
        }

        // 5. auto populate activites

        // 6. set status
        CRM_Core_Session::setStatus( "{$params['statusMsg']}" );
    }
}


