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
     * function to get the display name, primary email, location type and location id of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array  of display_name, email, location type and location id if found, or (null,null,null, null)
     * @static
     * @access public
     */
    static function getEmailDetails( $id, $locationTypeID = null ) 
    {
        $locationClause = null;
        if ( $locationTypeID ) {
            $locationClause = " AND civicrm_email.location_type_id = $locationTypeID";
        }

        $sql = "
SELECT    civicrm_contact.display_name,
          civicrm_email.email,
          civicrm_email.location_type_id,
          civicrm_email.id
FROM      civicrm_contact
LEFT JOIN civicrm_email ON ( civicrm_contact.id = civicrm_email.contact_id )
    WHERE civicrm_email.is_primary = 1
      AND civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row    = $result->fetchRow();
            if ( $row ) {
                return array( $row[0], $row[1], $row[2], $row[3] );
            }
        }
        return array( null, null, null, null );
    }
    
    
    /**
     * function to get the sms number and display name of a contact
     *
     * @param  int    $id id of the contact
     *
     * @return array    tuple of display_name and sms if found, or (null,null)
     * @static
     * @access public
     */
    static function getPhoneDetails( $id, $type = null ) 
    {
        if ( ! $id ) {
            return array( null, null );
        }

        $cond = null;
        if ( $type ) {
            $cond = " AND civicrm_phone.phone_type = '$type'";
        }


        $sql = "
   SELECT civicrm_contact.display_name, civicrm_phone.phone
     FROM civicrm_contact
LEFT JOIN civicrm_phone ON ( civicrm_phone.contact_id = civicrm_contact.id )
    WHERE civicrm_phone.is_primary = 1
          $cond
      AND civicrm_contact.id = " . CRM_Utils_Type::escape($id, 'Integer');

        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        $result = $dao->getDatabaseResult();
        if ( $result ) {
            $row    = $result->fetchRow();
            if ( $row ) {
                return array( $row[0], $row[1] );
            }
        }
        return array( null, null );
    }    