<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.9                                                |
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
        $session->set('singleForm', false);

        $config =& CRM_Core_Config::singleton( );
        if( in_array("CiviMember", $config->enableComponents )) {
            $this->_pages = array(
                                  'CRM_Contribute_Form_ContributionPage_Settings' => null,
                                  'CRM_Contribute_Form_ContributionPage_Amount'   => null,
                                  'CRM_Member_Form_MembershipBlock'               => null,
                                  'CRM_Contribute_Form_ContributionPage_ThankYou' => null,
                                  'CRM_Friend_Form_Contribute_TellAFriend'        => null,
                                  'CRM_Contribute_Form_ContributionPage_Custom'   => null,
                                  'CRM_Contribute_Form_ContributionPage_Premium'  => null,
                                  );
        } else {
            $this->_pages = array(
                                  'CRM_Contribute_Form_ContributionPage_Settings' => null,
                                  'CRM_Contribute_Form_ContributionPage_Amount'   => null,
                                  'CRM_Contribute_Form_ContributionPage_ThankYou' => null,
                                  'CRM_Friend_Form_Contribute_TellFriend'         => null,
                                  'CRM_Contribute_Form_ContributionPage_Custom'   => null,
                                  'CRM_Contribute_Form_ContributionPage_Premium'  => null,
                                  );

        }
        $this->addSequentialPages( $this->_pages, $action );
    }

}

?>
