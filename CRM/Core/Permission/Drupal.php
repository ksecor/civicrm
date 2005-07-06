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
class CRM_Core_Permission_Drupal {

    /**
     * is this user someone with access for the entire system
     *
     * @var boolean
     */
    static protected $_viewAdminUser = false;
    static protected $_editAdminUser = false;

    /**
     * am in in view permission or edit permission?
     * @var boolean
     */
    static protected $_viewPermission = false;
    static protected $_editPermission = false;

    /**
     * the current set of permissioned groups and saved searches for the user
     *
     * @var array
     */
    static protected $_viewPermissionedGroups;
    static protected $_editPermissionedGroups;

    static protected $_viewPermissionedSavedSearches;
    static protected $_editPermissionedSavedSearches;

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
            self::$_viewPermissionedGroups = self::$_editPermissionedGroups = array( );

            $groups =& CRM_Core_PseudoConstant::allGroup( );

            if ( CRM_Utils_System::checkPermission( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                self::$_editAdminUser          = self::$_viewAdminUser  = true;
                self::$_editPermission         = self::$_viewPermission = true;
                self::$_editPermissionedGroups = $groups;
                self::$_viewPermissionedGroups = $groups;
                return self::$_viewPermissionedGroups;
            } else if ( CRM_Utils_System::checkPermission( 'view all contacts' ) ) {
                self::$_viewAdminUser          = true;
                self::$_viewPermission         = true;
                self::$_viewPermissionedGroups = $groups;
            }

            foreach ( $groups as $id => $name ) {
                if ( CRM_Utils_System::checkPermission( 'edit ' . $name ) ) {
                    self::$_editPermissionedGroups[$id] = $name;
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_editPermission      = true;
                } else if ( CRM_Utils_System::checkPermission( 'view ' . $name ) ) {
                    self::$_viewPermissionedGroups[$id] = $name;
                    self::$_viewPermission      = true;
                } 
            }
        }

        return self::$_viewPermissionedGroups;
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
        if ( ! isset( self::$_viewPermissionedSavedSearches ) ) {
            self::$_viewPermissionedSavedSearches = self::$_editPermissionedSavedSearches = array( );

            $savedSearches =& CRM_Core_PseudoConstant::allSavedSearch( );

            if ( CRM_Utils_System::checkPermission( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                self::$_editAdminUser          = self::$_viewAdminUser  = true;
                self::$_editPermission         = self::$_viewPermission = true;
                self::$_editPermissionedSavedSearches = $savedSearches;
                self::$_viewPermissionedSavedSearches = $savedSearches;
                return self::$_viewPermissionedSavedSearches;
            } else if ( CRM_Utils_System::checkPermission( 'view all contacts' ) ) {
                self::$_viewAdminUser                 = true;
                self::$_viewPermission                = true;
                self::$_viewPermissionedSavedSearches = $savedSearches;
            }

            foreach ( $savedSearches as $id => $name ) {
                if ( CRM_Utils_System::checkPermission( 'edit ' . $name ) ) {
                    self::$_editPermissionedSavedSearches[$id] = $name;
                    self::$_viewPermissionedSavedSearches[$id] = $name;
                    self::$_editPermission                     = true;
                } else if ( CRM_Utils_System::checkPermission( 'view ' . $name ) ) {
                    self::$_viewPermissionedSavedSearches[$id] = $name;
                    self::$_viewPermission                     = true;
                } 
            }
        }

        return self::$_viewPermissionedSavedSearches;
    }

    /**
     * Get group clause for this user
     *
     * @param int $type the type of permission needed
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the group where clause for this user
     * @access public
     */
    public static function groupClause( $type = CRM_Core_Permission::VIEW, &$tables ) {
        if (! isset( self::$_viewPermissionedGroups ) ) {
            self::group( );
        }

        if ( $type == CRM_Core_Permission::EDIT ) {
            if ( self::$_editAdminUser ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( self::$_editPermissionedGroups ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $groups = implode( ', ', self::$_editPermissionedGroups );
                $clause = ' ( crm_group_contact.group_id IN (' . implode( ', ', array_keys( self::$_editPermissionedGroups ) ) . ') ) ';
                $tables['crm_group_contact'] = 1;
            }
        } else {
            if ( self::$_viewAdminUser ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( self::$_viewPermissionedGroups ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $groups = implode( ', ', self::$_viewPermissionedGroups );
                $clause = ' ( crm_group_contact.group_id IN (' . implode( ', ', array_keys( self::$_viewPermissionedGroups ) ) . ') ) ';
                $tables['crm_group_contact'] = 1;
            }
        }
        return $clause;
    }

    /**
     * Get savedSearch clause for this user
     *
     * @param int $type the type of permission needed
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the savedSearch where clause for this user
     * @access public
     */
    public static function savedSearchClause( $type = CRM_Core_Permission::VIEW ) {
        if (! isset( self::$_viewPermissionedSavedSearches ) ) {
            self::savedSearch( );
        }

        if ( $type == CRM_Core_Permission::EDIT ) {
            if ( self::$_editAdminUser ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( self::$_editPermissionedSearches ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $clauses = array( );
                foreach ( self::$_editPermissionedSavedSearches as $savedSearchId => $dontCare ) {
                    $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $savedSearchId, $tables );
                }
                $clause = ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
            }
        } else {
            if ( self::$_viewAdminUser ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( self::$_viewPermissionedSavedSearches ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $clauses = array( );
                foreach ( self::$_viewPermissionedSavedSearches as $savedSearchId => $dontCare ) {
                    $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $savedSearchId, $tables );
                }
                $clause = ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
            }
        }
        return $clause;
    }

    /**
     * get the current permission of this user
     *
     * @return string the permission of the user (edit or view or null)
     */
    public static function getPermission( ) {
        self::group( );
        self::savedSearch( );

        if ( self::$_editPermission ) {
            return CRM_Core_Permission::EDIT;
        } else if ( self::$_viewPermission ) {
            return CRM_Core_Permission::VIEW;
        }
        return null;
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
        self::group( );
        self::savedSearch( );

        /***
        CRM_Core_Error::debug( self::$_editAdminUser, self::$_viewAdminUser );
        CRM_Core_Error::debug( self::$_editPermission, self::$_viewPermission );
        CRM_Core_Error::debug( 'EG', self::$_editPermissionedGroups );
        CRM_Core_Error::debug( 'VG', self::$_viewPermissionedGroups );
        **/
        $clauses = array( );
        $clauses[] = self::groupClause( $type, $tables );
        $clauses[] = self::savedSearchClause( $type, $tables );
        return ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
    }


}

?>
