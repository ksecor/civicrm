<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contribute/DAO/Contribution.php';

/**
 * Create a page for displaying Contributions
 *
 */
class CRM_Contribute_Page_Contribution extends CRM_Core_Page {

    /** 
     * the id of the contribution that we are proceessing 
     * 
     * @var int 
     * @protected 
     */ 
    protected $_id;

    /** 
     * the id of the contact associated with this contribution 
     * 
     * @var int 
     * @protected 
     */ 
    protected $_contactID;

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
            $deleteExtra = ts('Are you sure you want to delete this Contribution?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit'),
                                                                          'url'   => 'civicrm/contribute/contribution',
                                                                          'qs'    => 'reset=1&action=update&id=%%id%%&cid=%%cid%%',
                                                                          'title' => ts('Edit') 
                                                                          ),
                                        CRM_Core_Action::DELETE  => array(
                                                                          'name'  => ts('Delete'),
                                                                          'url'   => 'civicrm/contribute/contribution',
                                                                          'qs'    => 'action=delete&reset=1&id=%%id%%',
                                                                          'title' => ts('Delete Contribution'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
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
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);

        $this->_id        = CRM_Utils_Request::retrieve('id' , $this );
        $this->_contactID = CRM_Utils_Request::retrieve('cid', $this );

        $url = CRM_Utils_System::url( 'civicrm/contribute/search',
                                      'reset=1&force=1&cid=' . $this->_contactID );

        // what action to take ?
        if ( $action & CRM_Core_Action::ADD    ||
             $action & CRM_Core_Action::UPDATE ||
             $action & CRM_Core_Action::DELETE ) {
            $session =& CRM_Core_Session::singleton( ); 
            $session->pushUserContext( $url );
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Contribution',
                                                           'Create Contribution',
                                                           $action );
            $controller->set( 'id' , $this->_id );
            $controller->set( 'cid', $this->_contactID );
            return $controller->run( );
        }

        // for view mode
        $this->view( );
        
        return parent::run( );
    }

    function view( ) {
        $values = array( );
        $ids    = array( );
        $params = array( 'id' => $this->_id );
        CRM_Contribute_BAO_Contribution::getValues( $params,
                                                    $values,
                                                    $ids );
        CRM_Contribute_BAO_Contribution::resolveDefaults( $values );

        $this->assign( $values );
    }
    
    /** 
     * compose the url to show details of this specific contribution 
     * 
     * @param int $id 
     * @param int $activityHistoryId 
     * 
     * @static 
     * @access public 
     */ 
    static function details($id, $activityHistoryId) { 
        $params   = array(); 
        $defaults = array(); 
        $params['id'          ] = $activityHistoryId; 
        $params['entity_table'] = 'civicrm_contact'; 
 
        require_once 'CRM/Core/BAO/History.php'; 
        $history        = CRM_Core_BAO_History::retrieve($params, $defaults); 
        $contributionId = CRM_Utils_Array::value('entity_id', $defaults); 
 
        if ($contributionId) { 
            return CRM_Utils_System::url('civicrm/contribute/contribution', "reset=1&action=view&id=$contributionId"); 
        } else { 
            return CRM_Utils_System::url('civicrm'); 
        } 
    } 


}
?>
