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

/**
 * Main page for viewing contact.
 *
 */
require_once 'CRM/Core/Page.php';
class CRM_Profile_Page_View extends CRM_Core_Page {

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
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, true);
        $gid = CRM_Utils_Request::retrieve('gid', 'Positive',
                                           $this);

        if ($gid) {
            require_once 'CRM/Profile/Page/Dynamic.php';
            $page =& new CRM_Profile_Page_Dynamic($id, $gid);
            $profileGroup = array( );
            $profileGroup['title'] = $title;
            $profileGroup['content'] = $page->run();
            $profileGroups[] = $profileGroup;
            $map = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gid, 'is_map' );
            if ( $map ) {
                $this->assign( 'mapURL',
                               CRM_Utils_System::url( "civicrm/profile/map",
                                                      "&reset=1&cid=$id&gid=$gid" ) );
            }
            
        } else {
            $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Profile'); 

            $profileGroups = array();
            foreach ($ufGroups as $groupid => $group) {
                require_once 'CRM/Profile/Page/Dynamic.php';
                $page =& new CRM_Profile_Page_Dynamic($id, $groupid);
                $profileGroup = array( );
                $profileGroup['title'] = $group['title'];
                $profileGroup['content'] = $page->run();
                $profileGroups[] = $profileGroup;
            }
        }
        
        $this->assign('profileGroups', $profileGroups);
        $this->assign('recentlyViewed', false);
        CRM_Utils_System::setTitle(ts('Contact\'s Profile'));
    }


    /**
     * build the outcome basing on the CRM_Profile_Page_Dynamic's HTML
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        $this->preProcess();
        parent::run();
    }

}

?>
