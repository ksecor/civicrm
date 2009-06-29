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
    public $_contactType;

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
        
        if ( !$this->get( 'maxLocationBlocks' )  ) {
            // find the system config related location blocks
            require_once 'CRM/Core/BAO/Preferences.php';
            $this->_maxLocationBlocks = CRM_Core_BAO_Preferences::value( 'location_count' );
            $this->set( 'maxLocationBlocks',  $this->_maxLocationBlocks );
        }
        
        // make blocks semi-configurable
        $this->_blocks = array( 'Email'  => 1,
                                'Phone'  => 1,
                                'IM'     => 1,
                                'OpenID' => 1);
        
        $this->assign( 'blocks', $this->_blocks );
        
        $this->_addBlockName  = CRM_Utils_Array::value( 'block', $_GET );
        $additionalblockCount = CRM_Utils_Array::value( 'count', $_GET );
        $this->assign( "addBlock", false );
        if ( $this->_addBlockName && $additionalblockCount ) {
            $this->assign( "addBlock", true );
            $this->assign( "blockName", $this->_addBlockName );
            $this->set( $this->_addBlockName."_Block_Count", $additionalblockCount );
        }
        
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
            
            //hack for now - remove when start edit code.
            $this->_contactId = 102;
            $this->_contactType = 'Individual';
            $this->assign( 'contactType', $this->_contactType );
        }

        require_once 'CRM/Core/BAO/Preferences.php';
        $this->_editOptions  = CRM_Core_BAO_Preferences::valueOptions( 'contact_edit_options', true, null, false, 'name', true );
        if ( $this->_contactType != 'Individual' &&
             array_key_exists( 'Demographics', $this->_editOptions ) ) {
            unset( $this->_editOptions['Demographics'] );
        }
        $this->assign( 'editOptions', $this->_editOptions );
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
           return true;
        }
        
        $this->addFormRule( array( 'CRM_Contact_Form_Contact', 'formRule'), $this );
    }

    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     * @param array $errors list of errors to be posted back to the form
     *
     * @return void
     * @static
     * @access public
     */
    static function formRule( &$fields, $files, &$form )
    {
        return $errors = array( );
        
        eval("\$formErrors = CRM_Contact_Form_Edit_".$form->_contactType."::formRule( \$fields, \$files );");
        $errors = array_merge($errors, $formErrors);
        
        $primaryID = false;
        foreach ( $form->_blocks as $name => $active ) {
            if ( $active ) {
                $name = strlower($name);
                foreach ( $fields[$name] as $count => $values ) {
                    if ( in_array($name, array('email', 'openid')) && !empty($values[$name]) ) {
                        $primaryID = true;
                    }
                    
                    if ( CRM_Utils_Array::value('is_primary', $values) && empty($values[$name]) ) {
                        $errors[$name][$count][$name] = ts('Primary %1 should not be empty.', array( 1 => $name) );
                    } 
                }
            }
        }
        
        if ( $form->_contactType == 'Individual' ) {   
            if ( !$primaryID && CRM_Utils_Array::value('missingRequired', $errors ) ) {
                $errors['_qf_default'] = ts('First Name and Last Name OR an email OR an OpenID in the Primary Location should be set.'); 
            }  
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        //load form for child blocks
        if ( $this->_addBlockName ) {
            require_once( str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $this->_addBlockName ) . ".php");
            return eval( 'CRM_Contact_Form_Edit_' . $this->_addBlockName . '::buildQuickForm( $this );' );
        }
        
        //build contact type specific fields
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $this->_contactType) . ".php");
        eval( 'CRM_Contact_Form_Edit_' . $this->_contactType . '::buildQuickForm( $this, $this->_action );' );
        
        //build blocks ( email, phone, im, openid )
        foreach ( $this->_blocks as $name => $active ) {
            if ( $active ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name ) . ".php");
                eval( 'CRM_Contact_Form_Edit_' . $name . '::buildQuickForm( $this );' );
            }
        }
        
        // build edit blocks ( custom data, address, communication preference, notes, tags and groups )
        foreach( $this->_editOptions as $name => $label ) {                
            if ( $name != 'CustomData' ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $name ) . ".php");
                eval( 'CRM_Contact_Form_Edit_' . $name . '::buildQuickForm( $this );' );
            }
        }
        
        foreach ( array_merge( array( 'Address' => 1 ), $this->_blocks ) as $blockName => $active ) {
            $hiddenCount = CRM_Utils_Array::value( "hidden_".$blockName ."_Count", $_POST );
            if (  $hiddenCount > 1 ) {
                require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_Form_Edit_" . $blockName ) . ".php");
                for ( $instance = 2; $instance <= $hiddenCount; $instance++ ) {
                    $this->assign( "addBlock", true );
                    $this->assign( 'blockName', $blockName );
                    $this->set( $blockName."_Block_Count", $instance );
                    eval( 'CRM_Contact_Form_Edit_' . $blockName . '::buildQuickForm( $this );' ); 
                }
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
        //get the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        CRM_Core_Error::debug( '$params', $params );
        exit( );
        
        //sample params array
        $params = array( 'contact_id'          => 102,
                         'prefix_id'           => 3,
                         'first_name'          => 'firstName',
                         'middle_name'         => '',
                         'last_name'           => 'lastName',
                         'suffix_id'           => 2,
                         'nick_name'           => '',
                         'job_title'           => '',
                         'current_employer'    => '',
                         'contact_source'      => '',
                         'external_identifier' => '', 
                         'hidden_Email_Count'  => 2,
                         'hidden_Phone_Count'  => 2,
                         'hidden_IM_Count'     => 2,
                         'hidden_OpenID_Count' => 2,
                         'hidden_Address_Count'=> 2,
                         'email' => array ( 1 => array (
                                                        'email'            => 'email_one@y.com',
                                                        'location_type_id' => 1,
                                                        'on_hold'          => false,
                                                        'is_bulkmail'      => 1,
                                                        'is_primary'       => 1,
                                                        ),
                                            2 => array (
                                                        'email'            => 'email_two@y.com',
                                                        'location_type_id' => 5,
                                                        'on_hold'          => 1,
                                                        'is_bulkmail'      => false,
                                                        'is_primary'       => false,
                                                        ) 
                                            ),
                         'phone' => array ( 1 => array ( 
                                                        'phone'            => 1111111,
                                                        'phone_type_id'    => 1,
                                                        'location_type_id' => 1,
                                                        'is_primary'       => true
                                                        ),
                                            2 => array ( 
                                                        'phone'            => 2222222,
                                                        'phone_type_id'    => 2,
                                                        'location_type_id' => 5,
                                                        'is_primary'       => false
                                                        ),
                                            ),
                         'im' => array ( 1 => array ( 'name'               => 'im_one',
                                                      'provider_id'        => 3,
                                                      'location_type_id'   => 1,
                                                      'is_primary'         => true
                                                      ),
                                         2 => array ( 'name'               => 'im_two',
                                                      'provider_id'        => 4,
                                                      'location_type_id'   => 5,
                                                      'is_primary'         => false
                                                      ),
                                         ),
                         'openid' => array ( 1 => array ( 'openid'           => 'http://civicrm.org/', 
                                                          'is_primary'       => 1, 
                                                          'location_type_id' => 1,
                                                          ),
                                             2 => array ( 'openid'           => 'http://civicrm.org/blog', 
                                                          'is_primary'       => false, 
                                                          'location_type_id' => 5,
                                                          ),
                                             ),
                         'address' => array ( 1 => array ( 'location_type_id'       => 1,
                                                           'is_primary'             => 1,
                                                           'street_address'         => 'Street Address 1',
                                                           'supplemental_address_1' => "Addt'l Address 1 1",
                                                           'city'                   => 'City 1',
                                                           'postal_code'            => '12345',
                                                           'postal_code_suffix'     => '123',
                                                           'state_province_id'      => '1004',
                                                           'country_id'             => '1228',
                                                           ),
                                              2 => array ( 'location_type_id'       => 5,
                                                           'is_billing'             => 1,
                                                           'street_address'         => 'Street Address 2',
                                                           'supplemental_address_1' => "Addt'l Address 1 2",
                                                           'city'                   => 'City 2',
                                                           'postal_code'            => 12345,
                                                           'postal_code_suffix'     => 123,
                                                           'state_province_id'      => 1000,
                                                           'country_id'             => 1228,
                                                           ),
                                              ),
                         'privacy' => array ( 'do_not_phone' => false,
                                              'do_not_email' => false,
                                              'do_not_mail' => false,
                                              'do_not_sms' => false,
                                              'do_not_trade' => false, 
                                              ),
                         'preferred_communication_method' => array ( 1 => true,
                                                                     2 => true,
                                                                     3 => true,
                                                                     4 => true,
                                                                     5 => true,
                                                                     ),
                         );
    
        
    }
    
}


