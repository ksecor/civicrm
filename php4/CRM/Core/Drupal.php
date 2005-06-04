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
$GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] =  false;
$GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] =  false;
$GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] =  false;
$GLOBALS['_CRM_CORE_DRUPAL']['_editPermission'] =  false;
$GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSavedSearches'] = null;


require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Contact/BAO/SavedSearch.php';
class CRM_Core_Drupal {

    /**
     * is this user someone with access for the entire system
     *
     * @var boolean
     */
    
    

    /**
     * am in in view permission or edit permission?
     * @var boolean
     */
    
    

    /**
     * the current set of permissioned groups and saved searches for the user
     *
     * @var array
     */
    
    

    
    

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
      function &group( ) {
        if ( ! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) {
            $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = array( );

            $groups =& CRM_Core_PseudoConstant::allGroup( );

            if ( user_access( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser']          = $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser']  = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission']         = $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = $groups;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = $groups;
                return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'];
            } else if ( user_access( 'view all contacts' ) ) {
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser']          = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']         = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = $groups;
            }

            foreach ( $groups as $id => $name ) {
                if ( user_access( 'edit ' . $name ) ) {
                    $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission']      = true;
                } else if ( user_access( 'view ' . $name ) ) {
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']      = true;
                } 
            }
        }

        return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'];
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
      function &savedSearch( ) {
        if ( ! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] ) ) {
            $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] = $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSavedSearches'] = array( );

            $savedSearches =& CRM_Core_PseudoConstant::allSavedSearch( );

            if ( user_access( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser']          = $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser']  = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission']         = $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSavedSearches'] = $savedSearches;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] = $savedSearches;
                return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'];
            } else if ( user_access( 'view all contacts' ) ) {
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser']                 = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']                = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] = $savedSearches;
            }

            foreach ( $savedSearches as $id => $name ) {
                if ( user_access( 'edit ' . $name ) ) {
                    $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSavedSearches'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission']                     = true;
                } else if ( user_access( 'view ' . $name ) ) {
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'][$id] = $name;
                    $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']                     = true;
                } 
            }
        }

        return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'];
    }

    /**
     * Get group clause for this user
     *
     * @param none
     * @return string the group where clause for this user
     * @access public
     */
      function groupClause( $type = 'view' ) {
        if (! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) {
            CRM_Core_Drupal::group( );
        }

        if ( $type == 'edit' ) {
            if ( $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $groups = implode( ', ', $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] );
                $clause = ' ( group_id IN (' . implode( ', ', array_keys( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] ) ) . ') ) ';
            }
        } else {
            if ( $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $groups = implode( ', ', $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] );
                $clause = ' ( group_id IN (' . implode( ', ', array_keys( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) . ') ) ';
            }
        }
        return $clause;
    }

    /**
     * Get savedSearch clause for this user
     *
     * @param none
     * @return string the savedSearch where clause for this user
     * @access public
     */
      function savedSearchClause( $type = 'view' ) {
        if (! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] ) ) {
            CRM_Core_Drupal::savedSearch( );
        }

        if ( $type == 'edit' ) {
            if ( $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSearches'] ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $clauses = array( );
                foreach ( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedSavedSearches'] as $savedSearchId => $dontCare ) {
                    $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $savedSearchId );
                }
                $clause = ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
            }
        } else {
            if ( $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] ) {
                $clause = ' ( 1 ) ';
            } else if ( empty( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] ) ) {
                $clause = ' ( 0 ) ';
            } else {
                $clauses = array( );
                foreach ( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedSavedSearches'] as $savedSearchId => $dontCare ) {
                    $clauses[] = CRM_Contact_BAO_SavedSearch::whereClause( $savedSearchId );
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
      function getPermission( ) {
        CRM_Core_Drupal::group( );
        CRM_Core_Drupal::savedSearch( );

        if ( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission'] ) {
            return 'edit';
        } else if ( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] ) {
            return 'view';
        }
        return null;
    }

    /**
     * Get the permissioned where clause for the user
     *
     * @param none
     * @return string the group where clause for this user
     * @access public
     */
      function whereClause( $type = 'view' ) {
        CRM_Core_Drupal::group( );
        CRM_Core_Drupal::savedSearch( );

        /***
        CRM_Core_Error::debug( self::$_editAdminUser, self::$_viewAdminUser );
        CRM_Core_Error::debug( self::$_editPermission, self::$_viewPermission );
        CRM_Core_Error::debug( 'EG', self::$_editPermissionedGroups );
        CRM_Core_Error::debug( 'VG', self::$_viewPermissionedGroups );
        **/
        $clauses = array( );
        $clauses[] = CRM_Core_Drupal::groupClause( $type );
        $clauses[] = CRM_Core_Drupal::savedSearchClause( $type );
        return ' ( ' . implode( ' OR ', $clauses ) . ' ) ';
    }


}

?>
