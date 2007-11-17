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

require_once 'CRM/Core/BAO/Facebook.php';
require_once 'CRM/Contact/Page/View.php';

class CRM_Contact_Page_View_Facebook extends CRM_Contact_Page_View 
{
   /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) {
        // get the primary city, state and zip for the contact
//         require_once 'CRM/Contact/BAO/Contact.php';
//         $ids = array( $this->_contactId );
//         $locations = CRM_Contact_BAO_Contact::getMapInfo( $ids );
        
//         require_once 'CRM/Utils/Sunlight.php';
//         $rows =& CRM_Utils_Sunlight::getInfo( $locations[0]['city'],
//                                               $locations[0]['state'],
//                                               $locations[0]['postal_code'] );
//         $this->assign( 'rowCount', count( $rows ) );
//         $this->assign_by_ref( 'rows', $rows );

        //crm_core_error::Debug('This is facebook page', 'test');

        $userInfo    = CRM_Core_BAO_Facebook::getUserProfile($this->_contactId);
        //$userFriends = CRM_Core_BAO_Facebook::getUserFriends($this->_contactId);
        $this->assign('user', $userInfo);
        $this->assign('userFriends', $userFriends);
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

        $this->browse( );

        return parent::run( );
    }

}

?>
