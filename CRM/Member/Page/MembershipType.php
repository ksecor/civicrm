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

require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Member/BAO/MembershipType.php';
/**
 * Page for displaying list of membership types
 */
class CRM_Member_Page_MembershipType extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;
   
    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Member_BAO_MembershipType';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this membership type?');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/member/membershipType',
                                                                    'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                    'title' => ts('Edit Membership Type') 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/member/membershipType',
                                                                    'qs'    => 'action=disable&id=%%id%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Membership Type') 
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/member/membershipType',
                                                                    'qs'    => 'action=enable&id=%%id%%',
                                                                    'title' => ts('Enable Membership Type') 
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/member/membershipType',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'title' => ts('Delete Membership Type') 
                                                                   )
                                 );
        }
        return self::$_links;
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
      
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'
        
        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($action, $id) ;
        } 
        // finally browse the custom groups
        $this->browse();
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all membership types.
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        // get all membership types sorted by weight
        $membershipType = array();
        require_once 'CRM/Member/DAO/MembershipType.php';
        $dao =& new CRM_Member_DAO_MembershipType();

        $dao->orderBy('weight');
        $dao->find();

        require_once 'CRM/Utils/Money.php';

        while ($dao->fetch()) {
            $membershipType[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $membershipType[$dao->id]);

            // fix the display of the monetary value, CRM-4038
            $membershipType[$dao->id]['minimum_fee'] = CRM_Utils_Money::format($membershipType[$dao->id]['minimum_fee'], null, '%a');

            // form all action links
            $action = array_sum(array_keys($this->links()));

            // update enable/disable links depending on if it is is_reserved or is_active
            if ( ! isset( $dao->is_reserved ) ) {
                if ($dao->is_active) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
                $membershipType[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                                array('id' => $dao->id));
            }            
        }

        $returnURL = CRM_Utils_System::url( 'civicrm/admin/member/membershipType', "reset=1&action=browse" );
        require_once 'CRM/Utils/Weight.php';
        CRM_Utils_Weight::addOrder( $membershipType, 'CRM_Member_DAO_MembershipType',
                                    'id', $returnURL );
        
        CRM_Member_BAO_MembershipType::convertDayFormat( $membershipType );
        $this->assign('rows', $membershipType);
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Member_Form_MembershipType';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Membership Types';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/member/membershipType';
    }
}


