<?php
/**
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class is used for listing the members of the group
 * 
 */
class CRM_Group_Form_GroupMember extends CRM_Core_Form
{
    
    /**
     * The group id, used for listing members
     *
     * @var int
     */
    protected $_id;

    function preProcess( ) {
        $this->_id    = $this->get( 'id' );
    }

    /**
     * This function sets the default values for the form. Group that in view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {

        if ( isset( $this->_id ) ) {
            $aMembers = CRM_Core_BAO_Group::getMember( $this->_id );
        }
        $this->assign('members', $aMembers );
    }

}

?>
