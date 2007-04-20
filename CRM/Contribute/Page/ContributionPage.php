<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contribute/DAO/ContributionPage.php';

/**
 * Create a page for displaying Contribute Pages
 * Contribute Pages are pages that are used to display
 * contributions of different types. Pages consist
 * of many customizable sections which can be
 * accessed.
 *
 * This page provides a top level browse view
 * of all the contribution pages in the system.
 *
 */
class CRM_Contribute_Page_ContributionPage extends CRM_Core_Page 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;


    /**
     * Get the action links for this page.
     *
     * @return array $_actionLinks
     *
     */
    function &actionLinks()
    {
        // check if variable _actionsLinks is populated
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this Contribution page?');
            $deleteExtra = ts('Are you sure you want to delete this Contribution page?');
            $copyExtra = ts('Are you sure you want to make a copy of this Contribution page?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Configure'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'reset=1&action=update&id=%%id%%',
                                                                          'title' => ts('Configure') 
                                                                          ),
                                        CRM_Core_Action::PREVIEW => array(
                                                                          'name'  => ts('Test-drive'),
                                                                          'url'   => 'civicrm/contribute/transact',
                                                                          'qs'    => 'reset=1&action=preview&id=%%id%%',
                                                                          'title' => ts('Preview'),
                                                                          ),
                                        CRM_Core_Action::FOLLOWUP    => array(
                                                                          'name'  => ts('Live Page'),
                                                                          'url'   => 'civicrm/contribute/transact',
                                                                          'qs'    => 'reset=1&id=%%id%%',
                                                                          'title' => ts('FollowUp'),
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable'),
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => CRM_Utils_System::currentPath( ),
                                                                          'qs'    => 'action=delete&reset=1&id=%%id%%',
                                                                          'title' => ts('Delete Custom Field'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::COPY     => array(
                                                                           'name'  => ts('Copy Contribution Page'),
                                                                           'url'   => CRM_Utils_System::currentPath( ),                                                                                                'qs'    => 'action=copy&gid=%%id%%',
                                                                           'title' => ts('Make a Copy of CiviCRM Contribution Page'),
                                                                           'extra' => 'onclick = "return confirm(\'' . $copyExtra . '\');"',
                                                                           ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {

        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.SortableTable');" );
        
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);

        // set breadcrumb to append to 2nd layer pages
        $breadCrumbPath = CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'reset=1' );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Manage Contribution Page') . '</a>';
    
        // what action to take ?

        if ( $action & CRM_Core_Action::ADD ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ),
                                                             'action=browse&reset=1' ) );
            require_once 'CRM/Contribute/Controller/ContributionPage.php';
            $controller =& new CRM_Contribute_Controller_ContributionPage( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            CRM_Utils_System::setTitle( ts('Manage Contribution Page') );
            return $controller->run( );
        } else if ($action & CRM_Core_Action::UPDATE ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ),
                                                             "action=update&reset=1&id={$id}") );
            require_once 'CRM/Contribute/Page/ContributionPageEdit.php';
            $page =& new CRM_Contribute_Page_ContributionPageEdit( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            return $page->run( );
        } else if ($action & CRM_Core_Action::PREVIEW) {
            require_once 'CRM/Contribute/Page/ContributionPageEdit.php';
            $page =& new CRM_Contribute_Page_ContributionPageEdit( );
            CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
            return $page->run( );
        } else if ($action & CRM_Core_Action::COPY) {
            $session =& CRM_Core_Session::singleton();
            CRM_Core_Session::setStatus("A copy of the contribution page has been created" );
            $this->copy( );
        } else if ($action & CRM_Core_Action::DELETE) {
            $subPage = CRM_Utils_Request::retrieve( 'subPage', 'String',
                                                    $this );
            if ( $subPage == 'AddProductToPage' ) {
                require_once 'CRM/Contribute/Page/ContributionPageEdit.php';
                $page =& new CRM_Contribute_Page_ContributionPageEdit( );
                CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );
                return $page->run( );
            } else {
                $session =& CRM_Core_Session::singleton();
                $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'reset=1&action=browse' ) );
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_ContributionPage_Delete',
                                                               'Delete Contribution Page',
                                                               $mode );
                $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                                  $this, false, 0);
                $controller->set('id', $id);
                $controller->process( );
                return $controller->run( );
            }
        } else {
            require_once 'CRM/Contribute/BAO/ContributionPage.php';
            // if action is enable or disable to the needful.
            if ($action & CRM_Core_Action::DISABLE) {
                CRM_Core_DAO::setFieldValue( 'CRM_Contribute_BAO_ContributionPage', $id, 'is_active', 0);
            } else if ($action & CRM_Core_Action::ENABLE) {
                CRM_Core_DAO::setFieldValue( 'CRM_Contribute_BAO_ContributionPage', $id, 'is_active', 1);
            }

            // finally browse the contribution pages
            $this->browse();
            CRM_Utils_System::setTitle( ts('Manage Contribution Pages') );
        }
       
        return parent::run();
    }

    /**
     * This function is to make a copy of a contribution page, including
     * all the fields in the page
     *
     * @return void
     * @access public
     */
    function copy( ) 
    {
        $gid = CRM_Utils_Request::retrieve('gid', 'Positive',
                                           $this, true, 0, 'GET');

        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        CRM_Contribute_BAO_ContributionPage::copy( $gid );

        CRM_Utils_System::redirect( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'reset=1' ) );
    }

    /**
     * Browse all custom data groups.
     *
     * @return void
     * @access public
     * @static
     */
    function browse($action=null)
    {
        
        // get all custom groups sorted by weight
        $contribution =  array();
        $dao      =& new CRM_Contribute_DAO_ContributionPage();

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        $dao->orderBy('title');
        $dao->find();

        while ($dao->fetch()) {
            $contribution[$dao->id] = array();
            CRM_Core_DAO::storeValues($dao, $contribution[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on custom_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $contribution[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                          array('id' => $dao->id));
        }
        $this->assign('rows', $contribution);
    }
}
?>
