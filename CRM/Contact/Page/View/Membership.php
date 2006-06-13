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

require_once 'CRM/Contact/Page/View.php';
require_once 'CRM/Member/BAO/Membership.php';

class CRM_Contact_Page_View_Membership extends CRM_Contact_Page_View {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

   /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) {
        $links =& self::links( );
//         $mask  = CRM_Core_Action::mask( $this->_permission );

//         $currentMemberships = CRM_Contact_BAO_Membership::getMembership($this->_contactId, CRM_Contact_BAO_Membership::CURRENT);
//         $pastMemberships    = CRM_Contact_BAO_Membership::getMembership( $this->_contactId, CRM_Contact_BAO_Membership::PAST);
//         $disableMemberships = CRM_Contact_BAO_Membership::getMembership( $this->_contactId, CRM_Contact_BAO_Membership::DISABLED);
        
//         $this->assign( 'currentMemberships', $currentMemberships );
//         $this->assign( 'pastMemberships'   , $pastMemberships );
//         $this->assign( 'disableMemberships', $disableMemberships );
        
//         $memberships = CRM_Member_BAO_Membership::getMembership($this->_contactId);
//         print_r($memberships);
//         $this->assign( 'memberships', $memberships );

        $membership = array();
        require_once 'CRM/Member/DAO/Membership.php';
        $dao =& new CRM_Member_DAO_Membership();
        $dao->contact_id = $this->_contactId;
        //$dao->orderBy('name');
        $dao->find();

        while ($dao->fetch()) {
            $membership[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $membership[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->links()));

            // update enable/disable links depending on if it is is_reserved or is_active
            if ($dao->is_reserved) {
                continue;
            } else {
                if ($dao->is_active) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
            }
            
            $membership[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                            array('id' => $dao->id));
        }

        $this->assign('rows', $membership);

    }

    /** 
     * This function is called when action is view
     *  
     * return null 
     * @access public 
     */ 
    function view( ) {
//         $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_MembershipView',  
//                                                        'View Membership',  
//                                                        $this->_action ); 
//         $controller->setEmbedded( true );  
//         $controller->set( 'id' , $this->_id );  
//         $controller->set( 'cid', $this->_contactId );  
    
//         return $controller->run( ); 
    }

    /** 
     * This function is called when action is update or new 
     *  
     * return null 
     * @access public 
     */ 
    function edit( ) { 
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_Membership', 
                                                       'Create Membership', 
                                                       $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 

        return $controller->run( );
    }


   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        $this->setContext( );

        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            $this->view( ); 
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) { 
            $this->edit( ); 
        } else {
            $this->browse( );
        }

        return parent::run( );
    }

    function setContext( ) {
        $context = CRM_Utils_Request::retrieve( 'context', 'String',
                                                $this, false, 'search' );

        switch ( $context ) {
        case 'basic':
            $url = CRM_Utils_System::url( 'civicrm/contact/view/basic',
                                          'reset=1&cid=' . $this->_contactId );
            break;

        case 'dashboard':
            $url = CRM_Utils_System::url( 'civicrm/member',
                                          'reset=1' );
            break;

        case 'membership':
            $url = CRM_Utils_System::url( 'civicrm/contact/view/membership',
                                          'reset=1&force=1&cid=' . $this->_contactId );
            break;

        default:
            $cid = null;
            if ( $this->_contactId ) {
                $cid = '&cid=' . $this->_contactId;
            }
            $url = CRM_Utils_System::url( 'civicrm/member/search', 
                                          'reset=1&force=1' . $cid );
            break;
        }

        $session =& CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );
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
            $deleteExtra = ts('Are you sure you want to delete this membership?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&rtype=%%rtype%%',
                                                                    'title' => ts('View Membership')
                                                                    ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&rtype=%%rtype%%',
                                                                    'title' => ts('Edit Membership')
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/rel',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&rtype=%%rtype%%',
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',
                                                                    'title' => ts('Delete Membership')
                                                                    ),
                                  );
        }
        return self::$_links;
    }


}

?>
