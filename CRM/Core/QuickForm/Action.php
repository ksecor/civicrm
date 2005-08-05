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
 * This is the base Action class for all actions which we redefine. This is
 * integrated with the StateMachine, Controller and State objects
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
require_once 'HTML/QuickForm/Action.php' ;

class CRM_Core_QuickForm_Action extends HTML_QuickForm_Action {
    /**
     * reference to the state machine i belong to
     * @var object
     */
    protected $_stateMachine;

    /**
     * constructor
     *
     * @param object    $stateMachine    reference to state machine object
     *
     * @return object
     * @access public
     */
    function __construct( &$stateMachine ) {
        $this->_stateMachine =& $stateMachine;
    }

    /**
     * returns the user to the top of the user context stack.
     *
     * @return void
     * @access public
     */
    function popUserContext( ) {
        $session =& CRM_Core_Session::singleton( );
        $config  =& CRM_Core_Config::singleton( );

        $userContext = $session->popUserContext( );

        if ( empty( $userContext ) ) {
            $userContext = $config->mainMenu;
        }

        header( "Location: $userContext" );
        exit();
    }

}

?>