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
require_once 'CRM/Contact/BAO/Group.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/Form.php';

/**
 * This class is to build the form for adding Group
 */
class CRM_Group_Form_Delete extends CRM_Core_Form {

    /**
     * the group id
     *
     * @var int
     */
    var $_id;

    /**
     * The title of the group being deleted
     *
     * @var string
     */
    var $_title;

    /**
     * set up variables to build the form
     *
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        $this->_id    = $this->get( 'id' );

        $defaults = array( );
        $params   = array( 'id' => $this->_id );
        CRM_Contact_BAO_Group::retrieve( $params, $defaults );

        $this->_title = $defaults['title'];
        $this->assign( 'name' , $this->_title );
        $this->assign( 'count', CRM_Contact_BAO_Group::memberCount( $this->_id ) );
        CRM_Utils_System::setTitle( ts('Confirm Group Delete') );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
     function buildQuickForm( ) {

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Delete Group'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
     function postProcess( ) {
        CRM_Contact_BAO_Group::discard( $this->_id );
        CRM_Core_Session::setStatus( ts('The Group "%1" has been deleted.', array(1 => $this->_title)) );        
    }
}

?>
