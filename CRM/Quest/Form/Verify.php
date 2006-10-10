<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_Verify extends CRM_Core_Form
{
    protected $_hash;

    function preProcess( ) 
    {
        $this->_hash = CRM_Utils_Request::retrieve( 'h', 'String', $this );
    }

    function setDefaultValues( ) {
        $defaults = array( );

        if ( $this->_hash ) {
            $defaults['hash'] = trim( $this->_hash );
        }

        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->assign       ( 'displayRecent' , false );

        $this->add('text'    , 'hash'       , ts('Confirmation Code' ), 'maxlength="128" size="45" class="form-text huge"', true );
        $this->add('text'    , 'email'      , ts('Email Address'     ), 'maxlength="128" size="45" class="form-text huge"', true );
        $this->add('password', 'password_1' , ts('Password'          ), null, true );
        $this->add('password', 'password_2' , ts('Re-enter Password' ), null, true );
        $this->addRule( array( 'password_1', 'password_2' ), ts( 'The passwords do not match' ), 'compare', null );

        $verifyBtn = ts('Verify Registration and Sign-in');
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
        $drupalID = CRM_Quest_API::getContactByHash( trim( $params['hash'] ),
                                                     $params['email'] );

        if ( $drupalID ) {
            $params = array( 'email'    => $params['email'],
                             'drupalID' => $drupalID,
                             'password' => $params['password_1'] );
            $result = quest_drupal_user_update_and_redirect( $params );
        }

        // if we are here, something messed up, so redirect
        $session =& CRM_Core_Session::singleton( );
        $session->setStatus( ts( 'We could not verify your information. Please check that you have entered the email address where you received this Recommendation Request and try again.' ) );
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/quest/verify' ) );
    }
    
}

?>
