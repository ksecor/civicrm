<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
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
        $id = CRM_Utils_Request::retrieve('cid', $this, true);
        $gid = CRM_Utils_Request::retrieve('gid', $this);

        if ($gid) {
            require_once 'CRM/Profile/Page/Dynamic.php';
            $page =& new CRM_Profile_Page_Dynamic($id, $gid);
            $profileGroup = array( );
            $profileGroup['title'] = $title;
            $profileGroup['content'] = $page->run();
            $profileGroups[] = $profileGroup;
            
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
