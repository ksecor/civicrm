<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

class CRM_Mailing_Form_Subscribe extends CRM_Core_Form
{
    protected $_groupID = null;

    function preProcess( ) 
    { 
        parent::preProcess( );
        $this->_groupID = CRM_Utils_Request::retrieve( 'gid', 'Integer', $this );

        if ( $this->_groupID ) {
            $this->assign( 'groupID'  , $this->_groupID );
            $groupName = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Group',
                                                       $this->_groupID,
                                                       'title' );
            $this->assign( 'groupName', $groupName );
            CRM_Utils_System::setTitle(ts('Subscribe to Mailing List - %1', array(1 => $groupName)));
            $this->assign( 'single', true  );
        } else {
            $this->assign( 'single', false );
            CRM_Utils_System::setTitle(ts('Mailing List Subscription'));
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */

    public function buildQuickForm( ) 
    {
        // add the email address
        $this->add( 'text',
                    'email',
                    ts( 'Email' ),
                    CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                               'email' ),
                    true );
        $this->addRule( 'email', ts('Please enter a valid email address (e.g. "yourname@example.com").'), 'email' );

        if ( ! $this->_groupID ) {
            // create a selector box of all public groups
            require_once 'CRM/Contact/BAO/Group.php';

            $groupTypeCondition = CRM_Contact_BAO_Group::groupTypeCondition( 'Mailing' );
            $query = "
SELECT id, title, description
  FROM civicrm_group
 WHERE ( saved_search_id = 0
    OR   saved_search_id IS NULL )
   AND visibility != 'User and User Admin Only'
   AND $groupTypeCondition";
            $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $rows   =  array( );
            while ( $dao->fetch( ) ) {
                $row                = array( );
                $row['id']          = $dao->id;
                $row['title']       = $dao->title;
                $row['description'] = $dao->description;
                $row['checkbox'   ] = CRM_Core_Form::CB_PREFIX . $row['id'];
                $this->addElement( 'checkbox',
                                   $row['checkbox'],
                                   null, null );
                $rows[] = $row;
            }
            if ( empty( $rows ) ) {
                CRM_Core_Error::fatal( ts( 'There are no public mailing list groups to display.' ) );
            }
            $this->assign( 'rows', $rows );
            $this->addFormRule( array( 'CRM_Mailing_Form_Subscribe', 'formRule' ) );
        }

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Subscribe'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                                 
                           );
    }
    
    static function formRule( &$fields ) {
        foreach ( $fields as $name => $dontCare ) {
            if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                return true;
            }
        }
        return array( '_qf_default' => 'Please select one or more mailing lists.' );
    }

    /**
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {    
        $params = $this->controller->exportValues( $this->_name );

        $groups = array( );
        if ( $this->_groupID ) {
            $groups[] = $this->_groupID;
        } else {
            foreach ( $params as $name => $dontCare ) {
                if ( substr( $name, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $groups[] = substr( $name, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
        }

        require_once 'CRM/Mailing/Event/BAO/Subscribe.php';
        $domainID = CRM_Core_Config::domainID( );
        $success = true;
        foreach ( $groups as $groupID ) {
            $se = CRM_Mailing_Event_BAO_Subscribe::subscribe( $domainID,
                                                              $groupID,
                                                              $params['email'] );
            if ( $se !== null ) {
                /* Ask the contact for confirmation */
                $se->send_confirm_request($params['email']);
            } else {
                $success = false;
            }
        }

        if ( $success ) {
            CRM_Utils_System::setUFMessage( ts( "Your subscription request has been submitted. Check your inbox shortly for the confirmation email(s)." ) );
        } else {
            CRM_Utils_System::setUFMessage( ts( "We had a problem processing your subscription request. Please contact the site administrator" ) );
        }

    }//end of function

}
?>
