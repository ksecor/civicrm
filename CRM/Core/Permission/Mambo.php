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

/**
 *
 */
class CRM_Core_Permission_Mambo {
    /**
     * get the current permission of this user
     *
     * @return string the permission of the user (edit or view or null)
     */
    public static function getPermission( ) {
        return CRM_Core_Permission::EDIT;
    }

    /**
     * Get the permissioned where clause for the user
     *
     * @param int $type the type of permission needed
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the group where clause for this user
     * @access public
     */
    public static function whereClause( $type, &$tables ) {
        return '( 1 )';
    }

    /**
     * Get all groups from database, filtered by permissions
     * for this user
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all groups.
     *
     */
    public static function &group( ) {
        return CRM_Core_PseudoConstant::allGroup( );
    }

    /**
     * Get all saved searches from database, filtered by permissions
     * for this user
     *
     * @access public
     * @static
     *
     * @param none
     * @return array - array reference of all filtered saved searches
     *
     */
    public static function &savedSearch( ) {
        return CRM_Core_PseudoConstant::allSavedSearch( );
    }
    
}

?>
