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

require_once 'CRM/Contact/Page/View/UserDashBoard.php';

class CRM_Contact_Page_View_UserDashBoard_Facebook extends CRM_Contact_Page_View_UserDashBoard 
{
    
    function getFriendList ( ) 
    {
        require_once "CRM/Core/BAO/Facebook.php";

        // get facebook friends
        $allFacebookFriends = array( );
        $allFacebookFriends = CRM_Core_BAO_Facebook::getUserFriends( $this->_contactId );
        
        // get civicrm facebook users
        $allCiviFacFriends = CRM_Core_BAO_Facebook::getCiviFaceUsers( );
        
        //get facbook friends who are also in CiviCRM
        $friends = array( );
        foreach ( $allFacebookFriends as $key => $values ) {
            if ( array_key_exists( $values['uid'], $allCiviFacFriends ) ) {
                $friends[] = array( 'first_name' => $values['first_name'],
                                    'last_name'  => $values['last_name'],
                                    'image'      => $values['pic'],
                                    'sex'        => $values['six'],
                                    'birthday'   => $values['birthday'],
                                    'status'     => $values['status'],
                                    'contact_id' => $allCiviFacFriends[$values['uid']]
                                    );
            }
        }
        
        $this->assign( 'friends', $friends);
    }

    /**
     * This function is the main function that is called when the page
     * loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        parent::preProcess( );
        $this->getFriendList( );
    }
}
?>
