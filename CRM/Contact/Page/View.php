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
require_once 'CRM/Utils/Recent.php';

/**
 * Main page for viewing contact.
 *
 */
class CRM_Contact_Page_View extends CRM_Core_Page {
    /**
     * the id of the object being viewed (note/relationship etc)
     *
     * @int
     * @access protected
     */
    protected $_id;

    /**
     * the contact id of the contact being viewed
     *
     * @int
     * @access protected
     */
    protected $_contactId;

    /**
     * The action that we are performing
     *
     * @string
     * @access protected
     */
    protected $_action;

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

        // check for permissions
        if ( ! CRM_Contact_BAO_Contact::permissionedContact( $this->_contactId, CRM_Core_Permission::VIEW ) ) {
            CRM_Core_Error::fatal( ts('You do not have the necessary permission to view this contact.') );
        }

        $this->getContactDetails();

        $contactImage = $this->get( 'contactImage' );
        $displayName  = $this->get( 'displayName'  );
        $this->assign( 'displayName', $displayName );

        CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );
        CRM_Utils_Recent::add( $displayName,
                               CRM_Utils_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $this->_contactId ),
                               $contactImage,
                               $this->_contactId );
        
        // also add the cid params to the Menu array
        CRM_Utils_Menu::addParam( 'cid', $this->_contactId );

        // create menus ..
        $startWeight = CRM_Utils_Menu::getMaxWeight('civicrm/contact/view');
        $startWeight++;
        CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($this->_contactId), 'civicrm/contact/view/cd', $startWeight);
    }


    /**
     * Get meta details of the contact.
     *
     * @return void
     * @access public
     */
    function getContactDetails()
    {
        $displayName = $this->get( 'displayName' );
             
        // if the display name is cached, we can skip the other processing
        if ( isset( $displayName ) ) {
            return;
        }

        list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );

        $this->set( 'displayName' , $displayName );
        $this->set( 'contactImage', $contactImage );
    }

}

?>
