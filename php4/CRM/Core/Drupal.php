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
$GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] = null;
$GLOBALS['_CRM_CORE_DRUPAL']['_editPermission'] = null;

require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';
class CRM_Core_Drupal {

    /**
     * is this user someone with access for the entire system
     *
     * @var boolean
     */
    
    

    /**
     * the current set of permissioned groups for the user
     *
     * @var array
     */
    
    

    /**
     * am in in view permission or edit permission?
     * @var boolean
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
          //if ( ! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) {
            $session =& CRM_Core_Session::singleton( );
        
            $groups =& CRM_Core_PseudoConstant::allGroup( );

            $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] = $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] = false;
            $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission'] = $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] = false;

            $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = array( );
            $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = array( );

            if ( user_access( 'edit all contacts' ) ) {
                // this is the most powerful permission, so we return
                // immediately rather than dilute it further
                $GLOBALS['_CRM_CORE_DRUPAL']['_editAdminUser'] = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission']      = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']      = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_editPermissionedGroups'] = $groups;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] = $groups;
                return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'];
            } else if ( user_access( 'view all contacts' ) ) {
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewAdminUser'] = true;
                $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission']      = true;
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
            // }

        return $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'];
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
     * get the current permission of this user
     *
     * @return string the permission of the user (edit or view or null)
     */
      function getPermission( ) {
        if (! isset( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermissionedGroups'] ) ) {
            CRM_Core_Drupal::group( );
        }

        if ( $GLOBALS['_CRM_CORE_DRUPAL']['_editPermission'] ) {
            return 'edit';
        } else if ( $GLOBALS['_CRM_CORE_DRUPAL']['_viewPermission'] ) {
            return 'view';
        }
        return null;
    }
}

?>
