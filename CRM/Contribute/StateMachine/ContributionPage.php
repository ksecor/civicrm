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

require_once 'CRM/Core/StateMachine.php';

/**
 * State machine for managing different states of the Import process.
 *
 */
class CRM_Contribute_StateMachine_ContributionPage extends CRM_Core_StateMachine {

    /**
     * class constructor
     *
     * @param object  CRM_Contribute_Controller_ContributionPage
     * @param int     $action
     *
     * @return object CRM_Contribute_StateMachine_ContributionPage
     */
    function __construct( $controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );

        
        $session =& CRM_Core_Session::singleton();
        $session->set('singleForm',false);
        $this->_pages = array(
                              'CRM_Contribute_Form_ContributionPage_Settings',
                              'CRM_Contribute_Form_ContributionPage_Amount',
                              'CRM_Contribute_Form_ContributionPage_ThankYou',
                              'CRM_Contribute_Form_ContributionPage_Custom',
                              'CRM_Contribute_Form_ContributionPage_Premium',
                              );
        
        $this->addSequentialPages( $this->_pages, $action );
    }

}

?>