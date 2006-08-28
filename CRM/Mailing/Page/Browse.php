<?php 

/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.5                                                | 
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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo 01/15/2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Mailing/Selector/Browse.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Core/Page.php';
/**
 * This implements the profile page for all contacts. It uses a selector
 * object to do the actual dispay. The fields displayd are controlled by
 * the admin
 */
class CRM_Mailing_Page_Browse extends CRM_Core_Page {

    /**
     * all the fields that are listings related
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /**
     * the mailing id of the mailing we're operating on
     *
     * @int
     * @access protected
     */
    protected $_mailingId;

    /**
     * the action that we are performing (in CRM_Core_Action terms)
     *
     * @int
     * @access protected
     */
    protected $_action;

    /**
     * cancel a mailing
     */
    function cancel() {
        if (CRM_Utils_Request::retrieve('confirmed', 'Boolean', $this)) {
            require_once 'CRM/Mailing/BAO/Job.php';
            $url = CRM_Utils_System::url('civicrm/mailing/browse', 'reset=1');
            CRM_Mailing_BAO_Job::cancel($this->_mailingId);
            CRM_Utils_System::redirect($url);
        } else {
            require_once 'CRM/Mailing/BAO/Mailing.php';
            $mailing =& new CRM_Mailing_BAO_Mailing();
            $mailing->id = $this->_mailingId;
            if ($mailing->find(true)) {
                $this->assign('subject', $mailing->subject);
            }
        }
    }

    /**
     * Heart of the viewing process. The runner gets all the meta data for
     * the contact and calls the appropriate type of page to view.
     *
     * @return void
     * @access public
     *
     */
    function preProcess() {
        $this->_mailingId = CRM_Utils_Request::retrieve('mid', 'Positive', $this);
        $this->_action    = CRM_Utils_Request::retrieve('action', 'String', $this);
        $this->assign('action', $this->_action);
    }

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        $this->preProcess();
        if ($this->_action & CRM_Core_Action::DISABLE) {
            $this->cancel();
        }
        CRM_Utils_System::setTitle(ts('Mailings'));

        $selector =& new CRM_Mailing_Selector_Browse( );
        
        $controller =& new CRM_Core_Selector_Controller(
                        $selector ,
                        $this->get( CRM_Utils_Pager::PAGE_ID ),
                        $this->get( CRM_Utils_Sort::SORT_ID  ),
                        CRM_Core_Action::VIEW, 
                        $this, 
                        CRM_Core_Selector_Controller::TEMPLATE );

        $controller->setEmbedded( true );
        $controller->run( );

        return parent::run( );
    }

}

?>
