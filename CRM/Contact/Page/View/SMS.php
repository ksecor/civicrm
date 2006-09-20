<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Core/DAO/ActivityHistory.php';
require_once 'CRM/SMS/DAO/History.php';
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * Dummy page for details of SMS
 *
 */
class CRM_Contact_Page_View_SMS extends CRM_Core_Page {
    /**
     * Run the page.
     *
     * This method is called after the page is created.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the callback, module and activity id
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse');
        $id     = CRM_Utils_Request::retrieve('id', 'Positive',
                                              $this);
        
        $dao =& new CRM_Core_DAO_ActivityHistory();
        $dao->activity_id   = $id;
        $dao->activity_type = ts( 'SMS Sent' );
        if ( $dao->find(true) ) {
            $cid = $dao->entity_id;
        }

        require_once 'CRM/SMS/DAO/History.php';
        $dao =& new CRM_SMS_DAO_History();
        $dao->id = $id;
       
        if ( $dao->find(true) ) {
            $this->assign('fromName',
                          CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                       $dao->contact_id,
                                                       'display_name' ) );
            $this->assign('toName',
                          CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                       $cid,
                                                       'display_name' ) );
            $this->assign('sentDate', $dao->sent_date);
            $this->assign('message', $dao->message);

            // get the display name and images for the contact
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $dao->contact_id );
            
            CRM_Utils_System::setTitle( $contactImage . ' ' . $displayName );

            require_once 'CRM/Core/Menu.php';
            // also add the cid params to the Menu array
            CRM_Core_Menu::addParam( 'cid',  $cid);
          
            // create menus ..
            $startWeight = CRM_Core_Menu::getMaxWeight('civicrm/contact/view');
            $startWeight++;
            require_once 'CRM/Core/BAO/CustomGroup.php';
            CRM_Core_BAO_CustomGroup::addMenuTabs(CRM_Contact_BAO_Contact::getContactType($cid), 'civicrm/contact/view/cd', $startWeight);
        }
        parent::run();
    }
}
?>