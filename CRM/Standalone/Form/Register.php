<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

class CRM_Standalone_Form_Register extends CRM_Core_Form {

    protected $_profileID;

    protected $_fields = array( );
    
    protected $_openID;
    

    function preProcess( ) {
        // pick the first profile ID that has user register checked
        require_once 'CRM/Core/BAO/UFGroup.php';
        $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('User Registration');

        if ( count( $ufGroups ) > 1 ) {
            CRM_Core_Error::fatal( ts( 'You have more than one profile that has been enabled for user registration.' ) );
        }

        foreach ( $ufGroups as $id => $dontCare ) {
            $this->_profileID = $id;
        }
        
        require_once 'CRM/Core/Session.php';
        $session =& CRM_Core_Session::singleton( );
        $this->_openID = $session->get( 'openid' );
    }
    
    function setDefaultValues( ) {
        $defaults = array( );
        
        $defaults['user_unique_id'] = $this->_openID;
        
        return $defaults;
    }

    function buildQuickForm( ) {
        $this->add( 'text',
                    'user_unique_id', 
                    ts( 'OpenID' ),
                    CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'user_unique_id' ),
                    true );
                    
        $this->add( 'text',
                    'email',
                    ts( 'Email' ),
                    CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'email' ),
                    true );

        $fields = CRM_Core_BAO_UFGroup::getFields( $this->_profileID,
                                                   false,
                                                   CRM_Core_Action::ADD,
                                                   null, null, false,
                                                   null, true );
        $this->assign( 'custom', $fields );
        
        require_once 'CRM/Profile/Form.php';
        foreach ( $fields as $key => $field ) {
            CRM_Core_BAO_UFGroup::buildProfile( $this,
                                                $field,
                                                CRM_Profile_Form::MODE_CREATE );
            $this->_fields[$key] = $field;
        }
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    function postProcess( ) {
        $formValues = $this->controller->exportValues( $this->_name );
        
        //print "formValues: <pre>";
        //print_r($formValues);
        //print "</pre>";
        
        require_once 'CRM/Standalone/User.php';
        require_once 'CRM/Utils/System/Standalone.php';
        require_once 'CRM/Core/BAO/OpenID.php';
        $user = new CRM_Standalone_User( $formValues['user_unique_id'], 
            $formValues['email'], $formValues['first_name'], $formValues['last_name']
        );
        CRM_Utils_System_Standalone::getUserID( $user );
        require_once 'CRM/Core/Session.php';
        $session =& CRM_Core_Session::singleton( );
        $contactId = $session->get( 'userID' );
        $openId = new CRM_Core_BAO_OpenId( );
        $openId->contact_id = $contact_id;
        $openId->find( true );
        $openId->allowed_to_login = 1;
        $openId->update( );
        
        // Set this to false if the registration is successful
        $session->set('new_install', false);
        
        header( "Location: index.php" );
    }

}

?>
