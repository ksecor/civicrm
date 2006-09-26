/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://www.civicrm.org/licensing/                                  |
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

class CRM_ACL_Base {

    /**
     * given a permission string, check for access requirements
     *
     * @param string $str       the permission to check
     * @param int    $contactID the contactID for whom the check is made
     *
     * @return boolean true if yes, else false
     * @static
     * @access public
     */
    static function check( $str, $contactID = null ) {
        if ( $contactID == null ) {
            $session   =& CRM_Core_Session::singleton( );
            $contactID =  $session->get( 'userID' );
        }

        if ( ! $contactID ) {
            $contactID = 0; // anonymous user
        }

        require_once 'CRM/ACL/BAO/ACL.php';
        CRM_ACL_BAO_ACL::check( $str, $contactID );
    }

    /**
     * Get the permissioned where clause for the user
     *
     * @param int $type the type of permission needed
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     * @param  array $whereTables (reference ) add the tables that are needed for the where clause
     * @param int    $contactID the contactID for whom the check is made
     *
     * @return string the group where clause for this user
     * @access public
     */
    public static function whereClause( $type, &$tables, &$whereTables, $contactID = null ) {
        if ( $contactID == null ) {
            $session   =& CRM_Core_Session::singleton( );
            $contactID =  $session->get( 'userID' );
        }

        if ( ! $contactID ) {
            $contactID = 0; // anonymous user
        }

        require_once 'CRM/ACL/BAO/ACL.php';
        CRM_ACL_BAO_ACL::whereClause( $type, $tables, $whereTables, $contactID );
    }

}

?>