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
require_once 'CRM/Core/Form.php';
/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_PCP_PCPAccount extends CRM_Core_Form
{
    /**
     *Variable defined for Contribution Page Id
     *
     */

    public  $_pageId = null;
    
    public function preProcess()  
    {
        $session =& CRM_Core_Session::singleton( );
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false );
        $this->_pageId = CRM_Utils_Request::retrieve( 'pageId', 'Positive', $this );
        $this->_id     = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        $this->set( 'action', $this->_action );
        $this->set( 'page_id', $this->_pageId );
        $this->set('contribution_page_id', $this->_pageId );
    }

    function setDefaultValues( ) 
    {
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get('userID');
        if ( $this->_action == CRM_Core_Action::ADD ){
            $id = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_PCPBlock', $this->_pageId, 'supporter_profile_id', 'entity_id' );
        } else {
            $id = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_PCPBlock', $this->_id, 'supporter_profile_id' );
        }
        $fields = null;
        require_once "CRM/Core/BAO/UFGroup.php";
        if ( $contactID ) {
            if ( CRM_Core_BAO_UFGroup::filterUFGroups($id, $contactID)  ) {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD );
            }
        } else {
            $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD );
        }
        
        if ( $fields ) {
            // unset any email-* fields since we already collect it
            foreach ( array_keys( $fields ) as $fieldName ) {
                if ( substr( $fieldName, 0, 6 ) == 'email-' ) {
                    unset( $fields[$fieldName] );
                }
            }
            
            $this->assign( 'fields', $fields );
            
            $addCaptcha = false;
            foreach($fields as $key => $field) {
                if ( $viewOnly &&
                     isset( $field['data_type'] ) &&
                     $field['data_type'] == 'File' ) {
                    // ignore file upload fields
                    continue;
                }
                require_once "CRM/Core/BAO/UFGroup.php";
                require_once "CRM/Profile/Form.php";
                CRM_Core_BAO_UFGroup::buildProfile($this, $field, CRM_Profile_Form::MODE_CREATE);
                $this->_fields[$key] = $field;
                if ( $field['add_captcha'] ) {
                    $addCaptcha = true;
                }
            }
            
            if ( $addCaptcha &&
                 ! $viewOnly ) {
                require_once 'CRM/Utils/ReCAPTCHA.php';
                $captcha =& CRM_Utils_ReCAPTCHA::singleton( );
                $captcha->add( $this );
                $this->assign( "isCaptcha" , true );
            }
        }
        
        $this->addButtons( array( 
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'), 
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                 )
                           );
    }
    
    /**  
     * global form rule  
     *  
     * @param array $fields  the input form values  
     * @param array $files   the uploaded files if any  
     * @param array $options additional user data  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * @static  
     */  
    static function formRule( &$fields, &$files, $self ) 
    {
    }
    
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {
    }
            
}
?>