<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Contact/Page/View.php';

/**
 * This class handle grant related functions
 *
 */
class CRM_Contact_Page_View_Grant extends CRM_Contact_Page_View 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * View details of a note
     *
     * @return void
     * @access public
     */
    function view( ) {
       
    }

    /**
     * This function is called when action is browse
     *
     * return null
     * @access public
     */
    function browse( ) {
        $links  =& self::links( );
        $action = array_sum(array_keys($links));

        require_once 'CRM/Grant/BAO/Grant.php';

        $grantStatus = CRM_Grant_BAO_Grant::getGrantStatuses();

        $grantType = CRM_Grant_BAO_Grant::getGrantTypes();
                                
        require_once 'CRM/Grant/DAO/Grant.php';
        $grant = new CRM_Grant_DAO_Grant( );
        $grant->contact_id = $this->_contactId;
        $grant->find();
        while ( $grant->fetch() ) { 
            CRM_Core_DAO::storeValues( $grant, $values[$grant->id] );
            $values[$grant->id]['action'] = CRM_Core_Action::formLink( $links,
                                                                       $action,
                                                                       array( 'id'  => $grant->id,
                                                                       'cid' => $this->_contactId ) );
            $values[$grant->id]['status_id'] = $grantStatus[$values[$grant->id]['status_id']];
        }

        $this->assign( 'grants', $values );

    }

    /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    function edit( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Grant_Form_Grant', 
                                                       'Create grant', 
                                                       $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 
        
        return $controller->run( );
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

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->view( );
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD ) ) {
            $this->edit( );
        } else if ( $this->_action & CRM_Core_Action::DELETE ) {
            // we use the edit screen the confirm the delete
            $this->edit( );
        }

        $this->browse( );
        return parent::run( );
    }

    /**
     * delete the note object from the db
     *
     * @return void
     * @access public
     */
    function delete( ) {

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
            $deleteExtra = ts('Are you sure you want to delete this grant?');

            self::$_links = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/contact/view/grant',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=grant',
                                                                    'title' => ts('View Grant')
                                                                    ),
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/grant',
                                                                    'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=grant',
                                                                    'title' => ts('Edit Grant')
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/grant',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=grant',
                                                                    'extra' => 'onclick = "if (confirm(\'' . $deleteExtra . '\') ) this.href+=\'&amp;confirmed=1\'; else return false;"',
                                                                    'title' => ts('Delete Grant')
                                                                    ),
                                  );
        }
        return self::$_links;
    }
                                  

}

?>
