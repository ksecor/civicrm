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

        $idList = array('membership_type' => 'MembershipType',
                        'status'          => 'MembershipStatus',
                      );

        $membership = array();
        require_once 'CRM/Member/DAO/Membership.php';
        $dao =& new CRM_Member_DAO_Membership();
        $dao->contact_id = $this->_contactId;
        //$dao->orderBy('name');
        $dao->find();

        // check is the user has view/edit membership permission
        $permission = CRM_Core_Permission::VIEW;
        if ( CRM_Core_Permission::check( 'edit memberships' ) ) {
            $permission = CRM_Core_Permission::EDIT;
        }
        $mask = CRM_Core_Action::mask( $permission );
        
        //checks membership of contact itself
        while ($dao->fetch()) {
            $membership[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $membership[$dao->id]);            
            foreach ( $idList as $name => $file ) {
                if ( $membership[$dao->id][$name .'_id'] ) {
                    $membership[$dao->id][$name] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_' . $file, 
                                                                                $membership[$dao->id][$name .'_id'] );
                }
            }
            if ( $dao->status_id ) {
                $active = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipStatus', $dao->status_id, 'is_current_member');
                if ( $active ) {
                    $membership[$dao->id]['active'] = $active;
                }
            }
            
            $membership[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $mask, array('id' => $dao->id, 
                                                                                                   'cid'=> $this->_contactId));
        }

        // get all the membership records extended through relationship (check CRM-1645)
        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once 'CRM/Member/BAO/Membership.php';
        require_once 'CRM/Member/BAO/MembershipType.php';
        
        // get all the relationship for the current contact
        $getRelations = array ( );
        $getRelations = CRM_Contact_BAO_Relationship::getRelationship( $this->_contactId, null, null, null, null);

        if ( !empty($getRelations) ) {
            $relMembership = array( );
            foreach ( $getRelations as $key => $value ) {
                // get the membership records for each related contacts
                $membershipValues = array( );
                $membershipId     = array( 'contact_id' => $value['cid'] );
                CRM_Member_BAO_Membership::getValues($membershipId, $membershipValues, $ids);

                if ( !empty($membershipValues) ) {
                    foreach ($membershipValues as $k => $val) {
                        $membershipType = 
                            CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $val['membership_type_id'] ); 
                        
                        // check if relationship type for the
                        // membership record is same as the relation
                        // between current contact and the related contact  (check CRM-1645)
                        if ( $membershipType['relationship_type_id'] != $value['civicrm_relationship_type_id'] ) { 
                            continue;
                        }

                        $relMembership[$k]['start_date'     ] = $val['start_date'];
                        $relMembership[$k]['end_date'       ] = $val['end_date'  ];
                        $relMembership[$k]['membership_type'] = $membershipType['name'];
                        $relMembership[$k]['source'         ] = $val['source'    ];
                        $relMembership[$k]['status'] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_MembershipStatus',
                                                                                    $val['status_id'] );
                        
                        if ( $val['status_id'] ) {
                            $active = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipStatus', 
                                                                  $val['status_id'], 'is_current_member');
                            if ( $active ) {
                                $relMembership[$k]['active'] = $active;
                            }
                        }
                        
                        $relMembership[$k]['action'] = CRM_Core_Action::formLink(self::links(), $mask,
                                                                                 array('id' => $k, 
                                                                                       'cid'=> $value['cid'] ) ); 
                    }
                }
            }
            $membership = array_merge( $membership, $relMembership );
        }

        $activeMembers = CRM_Member_BAO_Membership::activeMembers($this->_contactId, $membership );
        $inActiveMembers = CRM_Member_BAO_Membership::activeMembers($this->_contactId, $membership, 'inactive');
        $this->assign('activeMembers', $activeMembers);
        $this->assign('inActiveMembers', $inActiveMembers);
    }

    /** 
     * This function is called when action is view
     *  
     * return null 
     * @access public 
     */ 
    function view( ) {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_MembershipView', 'View Membership',  
                                                       $this->_action ); 
        $controller->setEmbedded( true );  
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );  
        
        return $controller->run( ); 
    }

    /** 
     * This function is called when action is update or new 
     *  
     * return null 
     * @access public 
     */ 
    function edit( ) { 
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Member_Form_Membership', 'Create Membership', 
                                                       $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set('BAOName', $this->getBAOName());
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

        if ( $this->_permission == CRM_Core_Permission::EDIT && ! CRM_Core_Permission::check( 'edit memberships' ) ) {
            $this->_permission = CRM_Core_Permission::VIEW; // demote to view since user does not have edit membership rights
            $this->assign( 'permission', 'view' );
        }
               
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
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          'reset=1&cid=' . $this->_contactId );
            break;

        case 'dashboard':
            $url = CRM_Utils_System::url( 'civicrm/member',
                                          'reset=1' );
            break;

        case 'membership':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactId}&selectedChild=member" );
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

            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/contact/view/membership',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                    'title' => ts('View Membership')
                                                                    ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/membership',
                                                                    'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                    'title' => ts('Edit Membership')
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/membership',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                    'title' => ts('Delete Membership')
                                                                    ),
                                  );
        }
        return self::$_links;
    }

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Member_BAO_Membership';
    }

}

?>
