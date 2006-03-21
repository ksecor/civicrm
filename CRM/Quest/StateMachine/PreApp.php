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
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Quest_StateMachine_PreApp extends CRM_Core_StateMachine {

    /**
     * class constructor
     *
     * @param object  CRM_Quest_Controller_PreApp
     * @param int     $action
     *
     * @return object CRM_Quest_StateMachine_PreApp
     */
    function __construct( &$controller, $action = CRM_Core_Action::NONE ) {
        parent::__construct( $controller, $action );

        $firstPages = array(
                            'CRM_Quest_Form_App_Personal'     => null,
                            'CRM_Quest_Form_App_Scholarship'  => null,
                            'CRM_Quest_Form_App_Educational'  => null,
                            'CRM_Quest_Form_App_Household'    => null,
                            );

        $householdDetails  = $controller->get( 'householdDetails' );
        if ( ! $householdDetails ) {
            $householdDetails = array( 'Mother' => 'Mother Details',
                                       'Father' => 'Father Details' );
            $controller->set( 'householdDetails', $householdDetails );
        }

        $householdDetailPages = array( );
        foreach ( $householdDetails as $name => $title ) {
            $householdDetailPages[$name] = array( 'className' => 'CRM_Quest_Form_App_Guardian',
                                                  'title'     => $title );
        }

        $totalSiblings = $controller->exportValue( 'Personal', 'number_siblings' );
        $siblingPages = array( );
        if ( is_numeric( $totalSiblings ) && $totalSiblings > 0 ) {
            for ( $i = 1; $i <= $totalSiblings; $i++ ) {
                $siblingPages["Sibling $i"] = array( 'className' => 'CRM_Quest_Form_App_Sibling',
                                                     'title'     => "Sibling $i" );
            }
        }

        $incomePages  = array( );

        $lastPages = array(
                           'CRM_Quest_Form_App_HighSchool'   => null,
                           'CRM_Quest_Form_App_SchoolOther'  => null,
                           'CRM_Quest_Form_App_Academic'     => null,
                           'CRM_Quest_Form_App_Testing'      => null,
                           'CRM_Quest_Form_App_Essay'        => null,
                           );

        $this->_pages = array_merge( $firstPages, $householdDetailPages, $siblingPages, $incomePages, $lastPages );

        $this->addSequentialPages( $this->_pages, $action );
    }

}

?>