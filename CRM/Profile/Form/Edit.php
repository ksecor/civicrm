<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Profile/Form.php';
/**
 * This class generates form components for custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
  */
class CRM_Profile_Form_Edit extends CRM_Profile_Form
{
    protected $_postURL   = null;
    protected $_cancelURL = null;
    protected $_errorURL  = null;

    /**
     * pre processing work done here.
     *
     * @param
     * @return void
     *
     * @access public
     *
     */
    function preProcess()
    {
        $this->_mode = CRM_Profile_Form::MODE_CREATE;

        if ( $this->get( 'edit' ) ) {
            // make sure we have right permission to edit this user
            $session =& CRM_Core_Session::singleton();
            $userID = $session->get( 'userID' );
            $id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this, false, $userID );
            
            require_once 'CRM/Contact/BAO/Contact.php';
            if ( $id != $userID &&
                 ! CRM_Contact_BAO_Contact::permissionedContact( $id, CRM_Core_Permission::EDIT ) ) {
                CRM_Core_Error::statusBounce( ts( 'You do not have permission to edit this contact' ) );
            }
        }

        parent::preProcess( );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
     function &setDefaultValues()
    {
        return parent::setContactValues( );
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        require_once 'CRM/UF/Form/Group.php';

        // add the hidden field to redirect the postProcess from

        require_once 'CRM/Core/DAO/UFGroup.php';
        $ufGroup =& new CRM_Core_DAO_UFGroup( );
        
        $ufGroup->id = $this->_gid;
        $ufGroup->find(true);

        // set the title
        CRM_Utils_System::setTitle( $ufGroup->title );
        $this->assign( 'recentlyViewed', false );
        
        $this->_postURL   = CRM_Utils_Array::value( 'postURL', $_POST );
        $this->_cancelURL = CRM_Utils_Array::value( 'cancelURL', $_POST );
        
        if ( ! $this->_postURL ) {
            $this->_postURL = $ufGroup->post_URL;
        }
        
        if ( ! $this->_postURL ) {
            if ( $this->_context == 'Search' ) {
                $this->_postURL = CRM_Utils_System::url( 'civicrm/contact/search' );
            } elseif ( $this->_id && $this->_gid ) {
                $this->_postURL = CRM_Utils_System::url('civicrm/profile/view',
                                                        "reset=1&id={$this->_id}&gid={$this->_gid}" );
            }
        }
        
        if ( ! $this->_cancelURL ) {
            $this->_cancelURL = $ufGroup->cancel_URL;
        } 

        // we do this gross hack since qf also does entity replacement
        $this->_postURL   = str_replace( '&amp;', '&', $this->_postURL   );
        $this->_cancelURL = str_replace( '&amp;', '&', $this->_cancelURL );
        
        $this->addElement( 'hidden', 'postURL', $this->_postURL );
        if ( $this->_cancelURL ) {
            $this->addElement( 'hidden', 'cancelURL', $this->_cancelURL );
        }

        // also retain error URL if set
        $this->_errorURL = CRM_Utils_Array::value( 'errorURL', $_POST );
        if ( $this->_errorURL ) {
            // we do this gross hack since qf also does entity replacement 
            $this->_errorURL = str_replace( '&amp;', '&', $this->_errorURL ); 
            $this->addElement( 'hidden', 'errorURL', $this->_errorURL ); 
        }
        
        // replace the session stack in case user cancels (and we dont go into postProcess)
        $session =& CRM_Core_Session::singleton(); 
        $session->replaceUserContext( $this->_postURL ); 
        
        parent::buildQuickForm( );
        
        //get the value from session, this is set if there is any file
        //upload field
        
        $session =& CRM_Core_Session::singleton( );
        $uploadNames = $session->get('uploadNames');
        
        if ( !empty($uploadNames) ) {
            $buttonName = 'upload'; 
        } else {
            $buttonName = 'next'; 
        }
        
        $this->addButtons(array(
                                array ('type'      => $buttonName,
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel'),
                                       'isDefault' => true)
                                )
                          );
        
        
        $this->addFormRule( array( 'CRM_Profile_Form', 'formRule' ), $this->_id );
    }
    
    /**
     * Process the user submitted custom data values.
     *
     * @access public
     * @return void
     */
    public function postProcess( ) 
    {
        parent::postProcess( );

        CRM_Core_Session::setStatus(ts('Thank you. Your information has been saved.'));

        // only replace user context if we do not have a postURL
        if ( ! $this->_postURL ) {
            $session =& CRM_Core_Session::singleton( );
            $session->replaceUserContext( CRM_Utils_System::url( 'civicrm/profile/view',
                                                                 "reset=1&id={$this->_id}&gid={$this->_gid}" ) );
        }

    }
    
    /**
     * Function to intercept QF validation and do our own redirection
     *
     * We use this to send control back to the user for a user formatted page
     * This allows the user to maintain the same state and display the error messages
     * in their own theme along with any modifications
     *
     * This is a first version and will be tweaked over a period of time
     *
     * @access    public                                                              
     * @return    boolean   true if no error found 
     */
    function validate( ) {
        $errors = parent::validate( );

        if ( ! $errors &&
             CRM_Utils_Array::value( 'errorURL', $_POST ) ) {
            $message = null;
            foreach ( $this->_errors as $name => $mess ) {
                $message .= $mess;
                $message .= '<p>';
            }
            
            if ( function_exists( 'drupal_set_message' ) ) {
                drupal_set_message( $message );
            }
            
            $message = urlencode( $message );

            $errorURL = $_POST['errorURL'];
            if ( strpos( $errorURL, '?' ) !== false ) {
                $errorURL .= '&';
            } else {
                $errorURL .= '?';
            }
            $errorURL .= "gid={$this->_gid}&msg=$message";
            CRM_Utils_System::redirect( $errorURL );
        }
        
        return $errors;
    }
}
?>
