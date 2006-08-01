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

require_once 'CRM/Quest/StateMachine/MatchApp.php';

/**
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Quest_StateMachine_MatchApp_Household extends CRM_Quest_StateMachine_MatchApp {

    static $_dependency = null;

    public function rebuild( &$controller, $action = CRM_Core_Action::NONE ) {
        // ensure the states array is reset
        $this->_states = array( );

        $this->_pages = array( 'CRM_Quest_Form_MatchApp_Household'     => null );

        $dynamic = array( 'Household', 'Sibling', 'Income' );
        foreach ( $dynamic as $d ) {
            require_once "CRM/Quest/Form/MatchApp/$d.php";
            eval( '$pages =& CRM_Quest_Form_MatchApp_' . $d . '::getPages( $controller );' );
            $this->_pages = array_merge( $this->_pages, $pages );
        }
        $this->_pages['CRM_Quest_Form_MatchApp_Income']       = null;

        if ( $this->includeNonCustodial( ) ) {
            $this->_pages['CRM_Quest_Form_MatchApp_Noncustodial'] = null;
        }
        parent::rebuild( $controller, $action );
    }

    public function &getDependency( ) {
        if ( self::$_dependency == null ) {
            self::$_dependency = array( 'Household'    => array( ),
                                        'Guardian'     => array( 'Household'  => 1 ),
                                        'Sibling'      => array( ),
                                        'Income'       => array( 'Guardian'   => 1 ),
                                        'Noncustodial' => array( ),
                                        );
        }

        return self::$_dependency;
    }

    public function includeNonCustodial( $force=false) {
        $includeNonCustodial = $this->_controller->get( 'includeNonCustodial' );
        if ( $includeNonCustodial === null || $force ) {
            $cid = $this->_controller->get( 'contactID' );
            $query = "
SELECT count( p.id )
  FROM quest_person p
 WHERE p.contact_id              = $cid
   AND p.is_parent_guardian      = 1
   AND p.is_contact_with_student = 0
";
            $includeNonCustodial = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray ) ? 1 : 0;
            $this->_controller->set( 'includeNonCustodial', $includeNonCustodial );
        }
        return $includeNonCustodial;
    }

}

?>
