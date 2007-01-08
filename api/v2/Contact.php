<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | http://civicrm.org/licensing/                                      |
 +--------------------------------------------------------------------+
*/

/**
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Add a contact to the db. If a dupe is found, check for
 * ignoreDupe flag to ignore or return error
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contact_add( &$params ) {
    $ids = array( );
    
    require_once 'CRM/Contact/BAO/Contact.php';
    $contact =& CRM_Contact_BAO_Contact::createFlat( $params, $ids );

    $values = array( );
    if ( is_a( $contact, 'CRM_Core_Error' ) ) {
        $values['error_message'] = $contact->_errors[0]['message'];
        $values['is_error'] = 1;
    } else {
        $values['contact_id'] = $contact->contact_id;
    }
    return $values;
}

/**
 * Retrieve a specific contact, given a set of input params
 * If more than one contact exists, return an error, unless
 * the client has requested to return the first found contact
 *
 * @param  array   $params           (reference ) input parameters
 * @param array    $returnProperties Which properties should be included in the
 *                                   returned Contact object. If NULL, the default
 *                                   set of properties will be included.

 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contact_get( &$params, $returnProperties = null ) {
    require_once 'api/v2/utils.php';

    _civicrm_initialize( );

    $values = array( );
    if ( empty( $params ) ) {
        $values['error_message'] = ts( 'No input parameters present' );
        $values['is_error'     ] = 1;
        return $values;
    }

    if ( ! is_array( $params ) ) {
        $values['error_message'] = ts( 'Input parameters is not an array' );
        $values['is_error'     ] = 1;
        return $values;
    }

    $contacts =& civicrm_contact_search( $params, $returnProperties );
    if ( civicrm_error( $contacts ) ) {
        return $contacts;
    }

    if ( count( $contacts ) != 1 &&
         ! $params['returnFirst'] ) {
        $values['error_message'] = ts( '%1 contacts matching input params', array( 1 => count( $contacts ) ) );
        $values['is_error'     ] = 1;
        return $values;
    }

    $contacts = array_values( $contacts );
    return $contacts[0];
}

/**
 * Retrieve a set of contacts, given a set of input params
 *
 * @param  array   $params           (reference ) input parameters
 * @param array    $returnProperties Which properties should be included in the
 *                                   returned Contact object. If NULL, the default
 *                                   set of properties will be included.
 * @param string   $sort             sort order for sql query
 * @param int      $offset           the row number to start from
 * @param int      $rowCount         the number of rows to return
 *
 * @return array (reference )        array of contacts, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contact_search( &$params,
                                  $returnProperties = null,
                                  $sort = null,
                                  $offset = 0,
                                  $rowCount = 25 ) {
    require_once 'api/v2/utils.php';

    _civicrm_initialize( );

    require_once 'CRM/Contact/BAO/Query.php';
    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $params );
    list( $contacts, $options ) = CRM_Contact_BAO_Query::apiQuery( $newParams,
                                                                   $returnProperties,
                                                                   null,
                                                                   $sort,
                                                                   $offset,
                                                                   $rowCount );
    return $contacts;
}


?>