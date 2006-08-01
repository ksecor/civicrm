<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Quest/Form/App.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_Verify extends CRM_Quest_Form_App
{
    protected $_hash;
    protected $_md5;

    function preProcess( ) 
    {
        $this->_hash = CRM_Utils_Request::retrieve( 'h', 'String', $this, true );
        $this->_md5  = CRM_Utils_Request::retrieve( 'm', 'String', $this, true );
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addElement('text'    , 'email'      , ts('Email Address'     ) );
        $this->addElement('password', 'password_1'   , ts('Password'          ) );
        $this->addElement('password', 'password_2'   , ts('Re-enter Password' ) );
        $this->addRule( array( 'password_1', 'password_2' ), ts( 'The passwords do not match' ), 'compare', null );

        $verifyBtn = ts('Verify Registration');
        $this->addElement( 'submit', $this->getButtonName('refresh'), $verifyBtn, array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );
    }

    /**
     *  This function is called when the form is submitted 
     *
     * @access public
     * @return None
     */
    function postProcess( ) {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        // make sure that the hash, md5 and email match
        require_once 'CRM/Quest/API.php';
        $drupalID = CRM_Quest_API::getContactByHash( $this->_hash, $this->_md5, $params['email'] );

        if ( $drupalID ) {
            $params = array( 'email'    => $params['email'],
                             'drupalID' => $drupalID,
                             'password' => $params['password_1'] );
            $result = quest_drupal_user_verify( $params );
        }

        // if we are here, something messed up, so redirect
        $session =& CRM_Core_Session::singleton( );
        $session->setStatus( ts( 'We could not verify your information, please check your email and try again' ) );
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/verify' ) );
    }
    
}

?>
