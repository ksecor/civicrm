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
class CRM_Core_Drupal {

    /**
     * is this user someone with access for the entire system
     *
     * @var boolean
     */
    static protected $_viewAdminUser;
    static protected $_editAdminUser;

    /**
     * the current set of permissioned groups for the user
     *
     * @var array
     */
    static protected $_viewPermissionedGroups;
    static protected $_editPermissionedGroups;

    /**
     * am in in view permission or edit permission?
     * @var boolean
     */
    static protected $_viewPermission;
    static protected $_editPermission;

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
        if ( ! isset( self::$_viewPermissionedGroups ) ) {
            $session =& CRM_Core_Session::singleton( );
        
            $groups =& CRM_Core_PseudoConstant::allGroup( );

            self::$_editAdminUser = self::$_viewAdminUser = false;
            self::$_editPermission = self::$_viewPermission = false;

            self::$_editPermissionedGroups = array( );
            self::$_viewPermissionedGroups = array( );

            if ( user_access( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                self::$_editAdminUser = true;
                self::$_editPermission      = true;
                self::$_editPermissionedGroups = $groups;
                self::$_viewPermissionedGroups = $groups;
                return self::$_viewPermissionedGroups;
            } else if ( user_access( 'view all contacts' ) ) {
                self::$_viewAdminUser = true;
                self::$_viewPermission      = true;
                self::$_viewPermissionedGroups = $groups;
            }

            foreach ( $groups as $id => $name ) {
                if ( user_access( 'edit ' . $name ) ) {
                    self::$_editPermissionedGroups[$id] = $name;
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_editPermission      = true;
                } else if ( user_access( 'view ' . $name ) ) {
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_viewPermission      = true;
                } 
            }

            // if we have view permissions only for any list, then we downgrade to view
            if ( self::$_viewPermission && self::$_editPermission ) {
                self::$_editPermission = false;
            }
        }

        return self::$_viewPermissionedGroups;
    }

    /**
     * Get group clause for this user
     *
     * @param none
     * @return string the group where clause for this user
     * @access public
     */
    public static function groupClause( ) {
        if (! isset( self::$_viewPermissionedGroups ) ) {
            self::group( );
        }

        if ( self::$_editAdminUser || self::$_viewAdminUser ) {
            $clause = ' ( 1 ) ';
        } else if ( empty( self::$_viewPermissionedGroups ) ) {
            $clause = ' ( 0 ) ';
        } else {
            $groups = implode( ', ', self::$_viewPermissionedGroups );
            $clause = ' ( group_id IN (' . implode( ', ', array_keys( self::$_viewPermissionedGroups ) ) . ') ) ';
        }
        return $clause;
    }

    /**
     * get the current permission of this user
     *
     * @return string the permission of the user (edit or view or null)
     */
    public static function getPermission( ) {
        if (! isset( self::$_viewPermissionedGroups ) ) {
            self::group( );
        }

        if ( self::$_editPermission ) {
            return 'edit';
        } else if ( self::$_viewPermission ) {
            return 'view';
        }
        return null;
    }
}

?>
