<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * This is the basic permission class wrapper
 */
class CRM_Core_Permission {
    /**
     * Static strings used to compose permissions
     *
     * @const
     * @var string
     */
    const
        EDIT_GROUPS = 'edit contacts in ',
        VIEW_GROUPS = 'view contacts in ';

    /**
     * The various type of permissions
     * 
     * @var int
     */
    const
        EDIT = 1,
        VIEW = 2;

    /**
     * get the current permission of this user
     *
     * @return string the permission of the user (edit or view or null)
     */
    public static function getPermission( ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userPermissionClass ) . '.php' );
        return eval( 'return ' . $config->userPermissionClass . '::getPermission( );' );
    }

    /**
     * given a permission string, check for access requirements
     *
     * @param string $str the permission to check
     *
     * @return boolean true if yes, else false
     * @static
     * @access public
     */
    static function check( $str ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userPermissionClass ) . '.php' );
        return eval( 'return ' . $config->userPermissionClass . '::check( $str ); ' );
    }
    
    /**
     * Get the permissioned where clause for the user
     *
     * @param int $type the type of permission needed
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     * @param  array $whereTables (reference ) add the tables that are needed for the where clause
     *
     * @return string the group where clause for this user
     * @access public
     */
    public static function whereClause( $type, &$tables, &$whereTables ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userPermissionClass ) . '.php' );
        return eval( 'return ' . $config->userPermissionClass . '::whereClause( $type, $tables, $whereTables );' );
    }

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
    public static function group( $groupType ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userPermissionClass ) . '.php' );
        return eval( 'return ' . $config->userPermissionClass . '::group( $groupType );' );
    }

    public static function customGroup( $type = CRM_Core_Permission::VIEW , $reset = false ) {
        $customGroups = CRM_Core_PseudoConstant::customGroup( $reset );

        // check if user has all powerful permission
        // or administer civicrm permission (CRM-1905)
        if ( self::check( 'access all custom data' ) ||
             self::check( 'administer CiviCRM' ) ) {
            return array_keys( $customGroups );
        }

        require_once 'CRM/ACL/API.php';
        return CRM_ACL_API::group( $type, null, 'civicrm_custom_group', $customGroups );
    }

    static function customGroupClause( $type = CRM_Core_Permission::VIEW, $prefix = null, $reset = false ) {
        $groups = self::customGroup( $type, $reset = false );
        if ( empty( $groups ) ) {
            return ' ( 0 ) ';
        } else {
            return "{$prefix}id IN ( " . implode( ',', $groups ) . ' ) ';
        }
    }

    public static function ufGroup( $type = CRM_Core_Permission::VIEW ) {
        $ufGroups = CRM_Core_PseudoConstant::ufGroup( );

        // check if user has all powerful permission
        if ( self::check( 'profile listings and forms' ) ) {
            return array_keys( $ufGroups );
        }

        require_once 'CRM/ACL/API.php';
        return CRM_ACL_API::group( $type, null, 'civicrm_uf_group', $ufGroups );
    }

    static function ufGroupClause( $type = CRM_Core_Permission::VIEW, $prefix = null, $returnUFGroupIds = false ) {
        $groups = self::ufGroup( $type );
        if ( $returnUFGroupIds ) {
            return $groups;
        } else if ( empty( $groups ) ) {
            return ' ( 0 ) ';
        } else {
            return "{$prefix}id IN ( " . implode( ',', $groups ) . ' ) ';
        }
    }

    public static function event( $type = CRM_Core_Permission::VIEW, $eventID = null ) {
        require_once 'CRM/Event/PseudoConstant.php';
        $events = CRM_Event_PseudoConstant::event( );

        // check if user has all powerful permission
        if ( self::check( 'register for events' ) ) {
            return array_keys( $events );
        }

        if ( $type == CRM_Core_Permission::VIEW &&
             self::check( 'view event info' ) ) {
            return array_keys( $events );
        }

        require_once 'CRM/ACL/API.php';
        $permissionedEvents = CRM_ACL_API::group( $type, null, 'civicrm_event', $events );
        if ( ! $eventID ) {
            return $permissionedEvents;
        }
        return array_search( $eventID, $permissionedEvents ) === false ? null : $eventID;
    }

    static function eventClause( $type = CRM_Core_Permission::VIEW, $prefix = null ) {
        $events = self::event( $type );
        if ( empty( $events ) ) {
            return ' ( 0 ) ';
        } else {
            return "{$prefix}id IN ( " . implode( ',', $events ) . ' ) ';
        }
    }

    static function access( $module, $checkPermission = true ) {
        $config =& CRM_Core_Config::singleton( );

        if ( ! in_array( $module, $config->enableComponents ) ) {
            return false;
        }

        if ( $checkPermission &&
             ! CRM_Core_Permission::check( "access $module" ) ) {
            return false;
        }
        
        return true;
    }

    static function checkMenu( &$args, $op = 'and' ) {
        if ( ! is_array( $args ) ) {
            return $args;
        }
        foreach ( $args as $str ) {
            $res = CRM_Core_Permission::check( $str );
            if ( $op == 'or' && $res ) {
                return true;
            } else if ( $op == 'and' && ! $res ) {
                return false;
            }
        }
        return ( $op == 'or' ) ? false : true;
    }

    static function checkMenuItem( &$item ) {
        if ( ! array_key_exists( 'access_callback', $item ) ) {
            CRM_Core_Error::backtrace( );
            CRM_Core_Error::fatal( );
        }

        // if component_id is present, ensure it is enabled
        if ( isset( $item['component_id'] ) &&
             $item['component_id'] ) {
            $config =& CRM_Core_Config::singleton( );
            if ( is_array( $config->enableComponentIDs ) &&
                 in_array( $item['component_id'],
                           $config->enableComponentIDs ) ) {
                // continue with process
            } else {
                return false;
            }
        }

        // the following is imitating drupal 6 code in includes/menu.inc
        if ( empty( $item['access_callback'] ) ||
             is_numeric( $item['access_callback'] ) ) {
            return (boolean ) $item['access_callback'];
        }

        // check if callback is for checkMenu, if so optimize it
        if ( is_array( $item['access_callback'] ) &&
             $item['access_callback'][0] == 'CRM_Core_Permission' &&
             $item['access_callback'][1] == 'checkMenu' ) {
            $op = CRM_Utils_Array::value( 1, $item['access_arguments'], 'and' );
            return self::checkMenu( $item['access_arguments'][0],
                                    $op );
        } else {
            return call_user_func_array( $item['access_callback'],
                                         $item['access_arguments'] );
        }
    }

    static function &basicPermissions( ) {
        static $permissions = null;

        if ( ! $permissions ) {
            $permissions = 
                array(
                      'add contacts'               => ts( 'add contacts' ),
                      'view all contacts'          => ts( 'view all contacts' ),
                      'edit all contacts'          => ts( 'edit all contacts' ),
                      'import contacts'            => ts( 'import contacts' ),
                      'edit groups'                => ts( 'edit groups' ),
                      'administer CiviCRM'         => ts( 'administer CiviCRM' ),
                      'access uploaded files'      => ts( 'access uploaded files' ),
                      'profile listings and forms' => ts( 'profile listings and forms' ),
                      'access all custom data'     => ts( 'access all custom data' ),
                      'view all activities'        => ts( 'view all activities' ),
                      'access CiviCRM'             => ts( 'access CiviCRM' ),
                      'access Contact Dashboard'   => ts( 'access Contact Dashboard' ),
                      );

            $config = CRM_Core_Config::singleton( );
            require_once 'CRM/Core/Component.php';
            $components = CRM_Core_Component::getEnabledComponents();
            foreach ( $components as $comp ) {
                $perm = $comp->getPermissions( );
                if ( $perm ) {
                    sort( $perm );
                    foreach ( $perm as $p ) {
                        $permissions[$p] = $p;
                    }
                }
            }
            asort( $permissions );
        }

        return $permissions;
    }

}
