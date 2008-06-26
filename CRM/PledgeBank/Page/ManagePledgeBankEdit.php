<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/PledgeBank/DAO/Pledge.php';


class CRM_PledgeBank_Page_ManagePledgeBankEdit extends CRM_Core_Page 
{
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
        
        $config =& CRM_Core_Config::singleton( );//crm_Core_error::Debug('e',$config->enableComponents);
        if ( in_array("PledgeBank", $config->enableComponents) ) {
            $this->assign('PledgeBank', true );
        }

        $this->_id = CRM_Utils_Request::retrieve('id', 'Positive',
                                                 $this, false, 0);
        // assign vars to templates
        $this->assign('action', $action);
        $this->assign( 'id', $this->_id );
        
        $subPage = CRM_Utils_Request::retrieve('subPage', 'String',
                                               $this );
        
        $this->assign( 'title', CRM_Core_DAO::getFieldValue( 'CRM_PledgeBank_DAO_Pledge', $this->_id, 'creator_pledge_desc' ) );

        $title = CRM_Core_DAO::getFieldValue( 'CRM_PledgeBank_DAO_Pledge', $this->_id, 'creator_pledge_desc' );
        CRM_Utils_System::setTitle(ts('Configure Pledge - %1', array(1 => $title)));//exit();

        $form = null;
        switch ( $subPage ) {
      
        case 'PledgeInfo':
            $form = 'CRM_PledgeBank_Form_ManagePledgeBank_PledgeInfo';
            break;

        case 'Location':
            $form = 'CRM_PledgeBank_Form_ManagePledgeBank_Location';
            break;

        case 'Friend':
            $form = 'CRM_Friend_Form_Pledge';
            break;
        }

        if ( $form ) {
            $session =& CRM_Core_Session::singleton( );

            require_once 'CRM/Core/Controller/Simple.php'; 
            $controller =& new CRM_Core_Controller_Simple($form, $subPage, $action); 
            $session =& CRM_Core_Session::singleton(); 
            $session->pushUserContext( CRM_Utils_System::url( CRM_Utils_System::currentPath( ), 'action=update&reset=1&id=' . $this->_id ) );
            $controller->set('id', $this->_id); 
            $controller->set('single', true );
            $controller->process(); 
            return $controller->run(); 
        }

        return parent::run();
    }


    /**
     * Browse Manage Pledge
     *
     * @return void
     * @access public
     * @static
     */
    function browse($action=null)
    {
        
        // get all custom groups sorted by weight
        $pledge =  array();
        $dao    =& new CRM_PledgeBank_DAO_Pledge();

        $dao->orderBy('creator_pledge_desc');
        $dao->find();

        while ($dao->fetch()) {
           
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on custom_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $pledge[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                          array('id' => $dao->id));
        }
        $this->assign('rows', $pledge);
    }
}

