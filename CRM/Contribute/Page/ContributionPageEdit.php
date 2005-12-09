<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contribute/DAO/ContributionPage.php';

/**
 * Create a page for displaying Contribute Pages
 * Contribute Pages are pages that are used to display
 * donations of different types. Pages consist
 * of many customizable sections which can be
 * accessed.
 *
 * This page provides a top level browse view
 * of all the donation pages in the system.
 *
 */
class CRM_Contribute_Page_ContributionPageEdit extends CRM_Core_Page {

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
        $this->_id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        if ( ! $this->_id ) {
            $dao =& new CRM_Contribute_DAO_ContributionPage( ); 
            $dao->domain_id = CRM_Core_Config::domainID( ); 
            $dao->save( ); 
 
            $this->_id = $dao->id; 
            $this->set( 'id', $dao->id );
        }

        $this->assign( 'id', $this->_id );
        $subPage = CRM_Utils_Request::retrieve('subPage', $this );

        $this->assign( 'title', CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title'));
        $this->assign( 'is_active', CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'is_active'));
        CRM_Utils_System::setTitle( ts('Configure Contribution Page') );
        
        $form = null;
        switch ( $subPage ) {
        case 'Amount':
            $form = 'CRM_Contribute_Form_ContributionPage_Amount';
            break;

        case 'Custom':
            $form = 'CRM_Contribute_Form_ContributionPage_Custom';
            break;

        case 'Settings':
            $form = 'CRM_Contribute_Form_ContributionPage_Settings';
            break;

        case 'ThankYou':
            $form = 'CRM_Contribute_Form_ContributionPage_ThankYou';
            break;
        }

        if ( $form ) {
            $session =& CRM_Core_Session::singleton( );

            require_once 'CRM/Core/Controller/Simple.php'; 
            $controller =& new CRM_Core_Controller_Simple($form, $subPage, $action); 
            $session =& CRM_Core_Session::singleton(); 
            $session->pushUserContext( CRM_Utils_System::url('civicrm/admin/contribute', 'action=update&reset=1&id=' . $this->_id ) );
            $controller->set('id', $this->_id); 
            $controller->set('single', true );
            $controller->process(); 
            return $controller->run(); 
        }

        return parent::run();
    }


    /**
     * Browse all contribution pages
     *
     * @return void
     * @access public
     * @static
     */
    function browse($action=null)
    {
        
        // get all custom groups sorted by weight
        $donation =  array();
        $dao      =& new CRM_Contribute_DAO_ContributionPage();

        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );

        $dao->orderBy('title');
        $dao->find();

        while ($dao->fetch()) {
            $donation[$dao->id] = array();
            CRM_Core_DAO::storeValues($dao, $donation[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->actionLinks()));
            
            // update enable/disable links depending on custom_group properties.
            if ($dao->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            
            $donation[$dao->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                          array('id' => $dao->id));
        }
        $this->assign('rows', $donation);
    }
}
?>
