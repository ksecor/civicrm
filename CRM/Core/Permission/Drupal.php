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
     * the current set of permissioned groups for the user
     *
     * @var array
     */
    static protected $_viewPermissionedGroups;
    static protected $_editPermissionedGroups;

    /**
     * Get all groups from database, filtered by permissions
     * for this user
     *
     * @access public
     * @static
     *
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

            foreach ( $groups as $id => $title ) {
                if ( CRM_Utils_System::checkPermission( 'edit ' . $title ) ) {
                    self::$_editPermissionedGroups[$id] = $title;
                    self::$_viewPermissionedGroups[$id] = $title;
                    self::$_editPermission      = true;
                } else if ( CRM_Utils_System::checkPermission( 'view ' . $title ) ) {
                    self::$_viewPermissionedGroups[$id] = $title;
                    self::$_viewPermission      = true;
                } 
            }
        }

        return self::$_viewPermissionedGroups;
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
                $clauses = array( );
                $groups = implode( ', ', self::$_editPermissionedGroups );
                $clauses[] = ' ( civicrm_group_contact.group_id IN (' . implode( ', ', array_keys( self::$_editPermissionedGroups ) ) . ') ) ';
                $tables['civicrm_group_contact'] = 1;
                
                // foreach group that is potentially a saved search, add the saved search clause
                foreach ( array_keys( self::$_editPermissionedGroups ) as $id ) {
                    $group     =& new CRM_Contact_DAO_Group( );
                    $group->id = $id;
                    if ( $group->find( true ) && $group->saved_search_id ) {
                        $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $group->saved_search_id, $tables );
                    }
                }
                $clause = ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
            }
        } else {
            if ( self::$_viewAdminUser ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( self::$_viewPermissionedGroups ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $clauses = array( );
                $groups = implode( ', ', self::$_viewPermissionedGroups );
                $clauses[] = ' ( civicrm_group_contact.group_id IN (' . implode( ', ', array_keys( self::$_viewPermissionedGroups ) ) . ') ) ';
                $tables['civicrm_group_contact'] = 1;

                // foreach group that is potentially a saved search, add the saved search clause
                foreach ( array_keys( self::$_viewPermissionedGroups ) as $id ) {
                    $group     =& new CRM_Contact_DAO_Group( );
                    $group->id = $id;
                    if ( $group->find( true ) && $group->saved_search_id ) {
                        $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $group->saved_search_id, $tables );
                    }
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

        /***
        CRM_Core_Error::debug( self::$_editAdminUser, self::$_viewAdminUser );
        CRM_Core_Error::debug( self::$_editPermission, self::$_viewPermission );
        CRM_Core_Error::debug( 'EG', self::$_editPermissionedGroups );
        CRM_Core_Error::debug( 'VG', self::$_viewPermissionedGroups );
        **/
        return self::groupClause( $type, $tables );
    }


}

?>
