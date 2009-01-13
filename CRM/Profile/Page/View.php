<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

/**
 * Main page for viewing contact.
 *
 */
require_once 'CRM/Core/Page.php';
class CRM_Profile_Page_View extends CRM_Core_Page 
{
    /** 
     * The group id that we are editing
     * 
     * @var int 
     */ 
    protected $_gid; 

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
                                          $this, false);
        if ( ! $id ) {
            $session =& CRM_Core_Session::singleton();
            $id = $session->get( 'userID' );
            if ( ! $id ) {
                CRM_Core_Error::fatal( ts( 'Could not find the required contact id parameter (id=) for viewing a contact record with a Profile.' ) );
            }
        }
        $this->assign( 'cid', $id );

        $this->_gid = CRM_Utils_Request::retrieve('gid', 'Positive',
                                           $this);

        if ($this->_gid) {
            require_once 'CRM/Profile/Page/Dynamic.php';
            $page =& new CRM_Profile_Page_Dynamic($id, $this->_gid, 'Profile' );
            $profileGroup            = array( );
            $profileGroup['title']   = null;
            $profileGroup['content'] = $page->run();
            $profileGroups[]         = $profileGroup;
            $map = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $this->_gid, 'is_map' );
            if ( $map ) {
                $this->assign( 'mapURL',
                               CRM_Utils_System::url( "civicrm/profile/map",
                                                      "reset=1&pv=1&cid=$id&gid={$this->_gid}" ) );
            }
            $this->assign( 'listingURL',
                           CRM_Utils_System::url( "civicrm/profile",
                                                  "force=1&gid={$this->_gid}" ) );
        } else {
            require_once 'CRM/Core/BAO/UFGroup.php';
            $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Profile'); 

            $profileGroups = array();
            foreach ($ufGroups as $groupid => $group) {
                require_once 'CRM/Profile/Page/Dynamic.php';
                $page =& new CRM_Profile_Page_Dynamic( $id, $groupid, 'Profile');
                $profileGroup = array( );
                $profileGroup['title'] = $group['title'];
                $profileGroup['content'] = $page->run();
                $profileGroups[] = $profileGroup;
            }
            $this->assign( 'listingURL',
                           CRM_Utils_System::url( "civicrm/profile",
                                                  "force=1" ) );
        }
        
        $this->assign( 'groupID', $this->_gid );

        $this->assign('profileGroups', $profileGroups);
        $this->assign('recentlyViewed', false);

        $title = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $this->_gid, 'title' );
        CRM_Utils_System::setTitle( $title );
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

    function getTemplateFileName() {
        if ( $this->_gid ) {
            $templateFile = "CRM/Profile/Page/{$this->_gid}/View.tpl";
            $template     =& CRM_Core_Page::getTemplate( );
            if ( $template->template_exists( $templateFile ) ) {
                return $templateFile;
            }
        }
        return parent::getTemplateFileName( );
    }

}


