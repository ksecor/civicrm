<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class CRM_Contact_BAO_Contact_Permission {

    /**
     * check if the logged in user has permissions for the operation type
     *
     * @param int    $id   contact id
     * @param string $type the type of operation (view|edit)
     *
     * @return boolean true if the user has permission, false otherwise
     * @access public
     * @static
     */
    static function allow( $id, $type = CRM_Core_Permission::VIEW ) 
    {
        $tables     = array( );
        $temp       = array( );
       
        //check permission based on relationship, CRM-2963
        if ( self::relationship( $id ) ) {
            return true;
        } else {
            require_once 'CRM/ACL/API.php';
            $permission = CRM_ACL_API::whereClause( $type, $tables, $temp );
        }
        require_once "CRM/Contact/BAO/Query.php";
        $from       = CRM_Contact_BAO_Query::fromClause( $tables );

        $query = "
SELECT count(DISTINCT contact_a.id) 
       $from
WHERE contact_a.id = %1 AND $permission";
        $params = array( 1 => array( $id, 'Integer' ) );

        return ( CRM_Core_DAO::singleValueQuery( $query, $params ) > 0 ) ? true : false;
    }

    /**
      * Function to get the permission base on its relationship
      * 
      * @param int $selectedContactId contact id of selected contact
      * @param int $contactId contact id of the current contact 
      *
      * @return booleab true if logged in user has permission to view
      * selected contact record else false
      * @static
      */
    static function relationship ( $selectedContactID, $contactID = null ) 
    {
        $session   =& CRM_Core_Session::singleton( );
        if ( ! $contactID ) {
            $contactID =  $session->get( 'userID' );
            if ( ! $contactID ) {
                return false;
            }
        }
        if (  $contactID == $selectedContactID ) {
            return true;
        } else {
            $query = "
SELECT id
FROM   civicrm_relationship
WHERE  ( contact_id_a = %1 AND contact_id_b = %2 AND is_permission_a_b = 1 ) OR
       ( contact_id_a = %2 AND contact_id_b = %1 AND is_permission_b_a = 1 )
";
            $params = array( 1 => array( $contactID        , 'Integer' ),
                             2 => array( $selectedContactID, 'Integer' ) );
            return CRM_Core_DAO::singleValueQuery( $query, $params );
        }
    }

    static function validateChecksumContact( $contactID ) {
        if ( ! self::allow( $contactID, CRM_Core_Permission::EDIT ) ) {
            // check if this is of the format cs=XXX
            require_once 'CRM/Contact/BAO/Contact/Utils.php';
            $cs = CRM_Utils_Request::retrieve( 'cs', 'String' , $this, false );
            if ( ! CRM_Contact_BAO_Contact_Utils::validChecksum( $contactID, $cs ) ) {
                $config =& CRM_Core_Config::singleton( );
                CRM_Core_Error::statusBounce( ts( 'You do not have permission to edit this contact record. Contact the site administrator if you need assistance.' ),
                                              $config->userFrameworkBaseURL );
            }
            return true;
        }
        return false;
    }

}