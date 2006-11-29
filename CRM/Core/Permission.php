<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Permission/Drupal.php';
require_once 'CRM/Core/Permission/Joomla.php';
require_once 'CRM/Core/Permission/Soap.php';

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
    public static function &group( ) {
        $config   =& CRM_Core_Config::singleton( );
        return eval( 'return ' . $config->userPermissionClass . '::group( );' );
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

}

?>
