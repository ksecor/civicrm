<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Custom/Form/CustomData.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_Contact extends CRM_Core_Form
{
    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactType;

    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactSubType;

    /**
     * The contact id, used when editing the form
     *
     * @var int
     */
    public $_contactId;

    /**
     * the default group id passed in via the url
     *
     * @var int
     */
    protected $_gid;

    /**
     * the default tag id passed in via the url
     *
     * @var int
     */
    protected $_tid;
    
    /**
     * the group tree data
     *
     * @var array
     */
    public $_groupTree;    

    /**
     * what blocks should we show and hide.
     *
     * @var CRM_Core_ShowHideBlocks
     */
    protected $_showHide;

    /**
     * name of de-dupe button
     *
     * @var string
     * @access protected
     */
    protected $_dedupeButtonName;

    /**
     * name of optional save duplicate button
     *
     * @var string
     * @access protected
     */
    protected $_duplicateButtonName;

    protected $_maxLocationBlocks = 0;

    protected $_editOptions = array( );

    protected $_blocks;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        $this->_action  = CRM_Utils_Request::retrieve('action', 'String',$this, false, 'add' );
                                                       
        $this->_dedupeButtonName    = $this->getButtonName( 'refresh', 'dedupe'    );
        $this->_duplicateButtonName = $this->getButtonName( 'next'   , 'duplicate' );
        
        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, false, true );            
        $this->assign( 'editOptions', $this->_editOptions );

        // make blocks semi-configurable
        $this->_blocks = array( 'Email'  => 1,
                                'Phone'  => 1,
                                'IM'     => 1,
                                'OpenID' => 1);
        
        $this->assign( 'blocks', $blocks );
                          
        $session = & CRM_Core_Session::singleton( );
        if ( $this->_action == CRM_Core_Action::ADD ) {
            // check for add contacts permissions
            require_once 'CRM/Core/Permission.php';
            if ( ! CRM_Core_Permission::check( 'add contacts' ) ) {
                CRM_Utils_System::permissionDenied( );
                return;
            }

            $this->_contactType = CRM_Utils_Request::retrieve( 'ct', 'String',
                                                               $this, true, null, 'REQUEST' );
            if ( ! in_array( $this->_contactType,
                             array( 'Individual', 'Household', 'Organization' ) ) ) {
                CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
            }

            $this->_contactSubType = CRM_Utils_Request::retrieve( 'cst','String', 
                                                                  CRM_Core_DAO::$_nullObject,
                                                                  false,null,'GET' );
            $this->_gid = CRM_Utils_Request::retrieve( 'gid', 'Integer',
                                                       CRM_Core_DAO::$_nullObject,
                                                       false, null, 'GET' );
            $this->_tid = CRM_Utils_Request::retrieve( 'tid', 'Integer',
                                                       CRM_Core_DAO::$_nullObject,
                                                       false, null, 'GET' );
            if ( $this->_contactSubType ) {
                CRM_Utils_System::setTitle( ts( 'New %1', array(1 => $this->_contactSubType ) ) );
            } else {
                $title = ts( 'New Individual' );
                if ( $this->_contactType == 'Household' ) {
                    $title = ts( 'New Household' );
                } else if ( $this->_contactType == 'Organization' ) {
                    $title = ts( 'New Organization' );
                }
                CRM_Utils_System::setTitle( $title );
            }
            $this->assign( 'contactType', $this->_contactType );
            $session->pushUserContext(CRM_Utils_System::url());
            $this->_contactId = null;
        } else {
            //update mode
        }
        
        
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {

        $defaults = array( );
        $params   = array( );

        return $defaults;
    }

    /**
     * This function is used to add the rules (mainly global rules) for form.
     * All local rules are added near the element
     *
     * @return None
     * @access public
     * @see valid_date
     */
    function addRules( )
    {
        $this->addFormRule( array( 'CRM_Contact_Form_Edit_' . $this->_contactType, 'formRule' ), $this->_contactId );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        //build contact type specific fields
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $this->_contactType) . ".php");
        eval( 'CRM_Contact_Form_Edit_' . $this->_contactType . '::buildQuickForm( $this, $this->_action );' );
        
        //build blocks ( email, phone, im, openid )
        
        // build edit blocks ( custom data, address, communication preference, notes, tags and groups )
        foreach( $this->_editOptions as $name => $status ) {                
            if ( $status && $name != 'CustomData' ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name) . ".php");
                eval( 'CRM_Contact_Form_Edit_' . $name . '::buildQuickForm( $this );' );
            }
        }

        // add the dedupe button
        $this->addElement('submit', 
                          $this->_dedupeButtonName,
                          ts( 'Check for Matching Contact(s)' ) );
        $this->addElement('submit', 
                          $this->_duplicateButtonName,
                          ts( 'Save Matching Contact' ) );
        $this->addElement('submit', 
                          $this->getButtonName( 'next'   , 'sharedHouseholdDuplicate' ),
                          ts( 'Save With Duplicate Household' ) );

        // make this form an upload since we dont know if the custom data injected dynamically
        // is of type file etc $uploadNames = $this->get( 'uploadNames' );
        $this->addButtons( array(
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Save and New'),
                                         'subName'   => 'new' ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }
    
    
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        
    }

}


