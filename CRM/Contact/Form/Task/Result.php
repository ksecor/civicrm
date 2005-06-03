<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * Used for displaying results
 *
 *
 */
class CRM_Contact_Form_Task_Result extends CRM_Contact_Form_Task {

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        $session = CRM_Core_Session::singleton( );

        $context = $this->get( 'context' );
        if ( $context == 'smog' || $context == 'amtg' ) {
            $url = CRM_Utils_System::url( 'civicrm/group/search', 'reset=1&force=1&context=smog&gid=' );
            if ( $this->get( 'context' ) == 'smog' ) {
                $session->replaceUserContext( $url . $this->get( 'gid'    ) );
            } else {
                $session->replaceUserContext( $url . $this->get( 'amtgID' ) );
            }
            return;
        }

        $ssID = $this->get( 'ssID' );
        if ( isset( $ssID ) ) {
            if ( $this->_action == CRM_Core_Action::BASIC ) {
                $fragment = 'search';
            } else {
                $fragment = 'search/advanced';
            }
            $url = CRM_Utils_System::url( 'civicrm/contact/' . $fragment, 'reset=1&force=1&ssID=' . $ssID );
            $session->replaceUserContext( $url );
            return;
        }
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'done',
                                         'name'      => ts('Done'),
                                         'isDefault' => true   ),
                                 )
                           );
    }

}
?>
