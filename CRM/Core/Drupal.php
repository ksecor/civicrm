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
     * am in in view mode or edit mode?
     * @var boolean
     */
    static protected $_viewMode;
    static protected $_editMode;

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
            self::$_editMode = self::$_viewMode = false;

            if ( user_access( 'edit all contacts' ) ) {
                self::$_editAdminUser = true;
                self::$_editMode      = true;
                self::$_editPermissionedGroups = $groups;
                self::$_viewPermissionedGroups = $groups;
            } else if ( user_access( 'view all contacts' ) ) {
                self::$_viewAdminUser = true;
                self::$_viewMode      = true;
                self::$_viewPermissionedGroups = $groups;
            }

            self::$_editPermissionedGroups = array( );
            self::$_viewPermissionedGroups = array( );
            foreach ( $groups as $id => $name ) {
                if ( user_access( 'edit ' . $name ) ) {
                    self::$_editPermissionedGroups[$id] = $name;
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_editMode      = true;
                } else if ( user_access( 'view ' . $name ) ) {
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_viewMode      = true;
                } 
            }

            // if we have view permissions only for any list, then we downgrade to view
            if ( self::$_viewMode && self::$_editMode ) {
                self::$_editMode = false;
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
     * get the current mode of this user
     *
     * @return string the mode of the user (edit or view or null)
     */
    public static function getMode( ) {
        if (! isset( self::$_viewPermissionedGroups ) ) {
            self::group( );
        }

        if ( self::$_editMode ) {
            return 'edit';
        } else if ( self::$_viewMode ) {
            return 'view';
        }
        return null;
    }
}

?>
