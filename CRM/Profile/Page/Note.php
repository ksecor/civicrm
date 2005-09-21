<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

/**
 * Main page for viewing Notes.
 *
 */
class CRM_Profile_Page_Note extends CRM_Core_Page
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * View details of a note
     *
     * @return void
     * @access public
     */
    function view( ) {
        $note =& new CRM_Core_DAO_Note( );
        $note->id = $this->_id;
        if ( $note->find( true ) ) {
            $values = array( );
            CRM_Core_DAO::storeValues( $note, $values );
            $this->assign( 'note', $values );
        }
    }

    /**
     * This function is called when action is browse
     *
     * return null
     * @access public
     */
    function browse( ) {
        $note =& new CRM_Core_DAO_Note( );
        $note->entity_table = 'civicrm_contact';
        $note->entity_id    = $this->_contactId;

        $note->orderBy( 'modified_date desc' );

        $values =  array( );
        $links  =& self::links( );
        $action = array_sum(self::links( ));
        
        $note->find( );
        while ( $note->fetch( ) ) {
            $values[$note->id] = array( );
            CRM_Core_DAO::storeValues( $note, $values[$note->id] );
            $values[$note->id]['action'] = CRM_Core_Action::formLink( $links,
                                                                      $action,
                                                                      array( 'id'  => $note->id,
                                                                             'cid' => $this->_contactId ) );
        }
        $this->assign( 'notes', $values );
    }

    /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    function edit( ) {
        $session = CRM_Core_Session::singleton( );
        $session->set( 'userID', $this->_contactId );

        $controller =& new CRM_Core_Controller_Simple( 'CRM_Note_Form_Note', ts('Contact Notes'), $this->_action );
        $controller->setEmbedded( true );

        // set the userContext stack
        $session =& CRM_Core_Session::singleton();
        $url = CRM_Utils_System::url('civicrm/profile/note', 'action=browse&cid=' . $this->_contactId );
        $session->pushUserContext( $url );

        $controller->reset( );
        $controller->set( 'entityTable', 'civicrm_contact' );
        $controller->set( 'entityId'   , $this->_contactId );
        $controller->set( 'id'         , $this->_id );

        $controller->process( );
        $controller->run( );

        $session->set( 'userID', '' );
    }

    /** 
     * Heart of the viewing process. The runner gets all the meta data for 
     * the contact and calls the appropriate type of page to view. 
     * 
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) 
    { 
        $this->_id = CRM_Utils_Request::retrieve( 'id', $this ); 
        $this->assign( 'id', $this->_id ); 
         
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', $this, true ); 
        $this->assign( 'contactId', $this->_contactId ); 

        $this->_action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); 
        $this->assign( 'action', $this->_action); 

        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );

        $this->assign( 'displayName' , $displayName  ); 
        $this->assign( 'contactImage', $contactImage );
        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName ); 
    }

    /**
     * This function is the main function that is called when the page loads,
     * it decides the which action has to be taken for the page.
     *
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->view( );
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD ) ) {
            $this->edit( );
        }

        $this->browse( );
        return parent::run( );
    }

    /**
     * delete the note object from the db
     *
     * @return void
     * @access public
     */
    function delete( ) {
        CRM_Core_BAO_Note::del( $this->_id );
    }

    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &links()
    {
        if (!(self::$_links)) {
            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/profile/note',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%',
                                                                    'title' => ts('View Note')
                                                                    ),
                                  );
        }
        return self::$_links;
    }
                                  

}

?>
