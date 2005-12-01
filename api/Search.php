<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.3                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright (c) 2005 Donald A. Lobo                                  | 
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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * Definition of the Contact part of the CRM API.  
 * More detailed documentation can be found  
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs 
 * here} 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo 01/15/2005 
 * $Id$ 
 * 
 */

  /** 
   * Files required for this package 
   */ 
 
require_once 'api/utils.php'; 

require_once 'CRM/Contact/BAO/Query.php';

/** 
 * Most API functions take in associative arrays ( name => value pairs 
 * as parameters. Some of the most commonly used parameters are 
 * described below 
 * 
 * @param array $params           an associative array used in construction 
                                  / retrieval of the object 
 * @param array $returnProperties the limited set of object properties that 
 *                                need to be returned to the caller 
 * 
 */ 


/** 
 * Returns the number of Contact objects which match the search criteria specified in $params.
 *
 * @param array  $params
 *
 * @return int
 * @access public
 */
function crm_contact_search_count( &$params ) {
    $query =& new CRM_Contact_BAO_Query( $params );
    return $query->searchQuery( 0, 0, null, true );
}

/**  
 * returns a number of contacts from the offset that match the criteria
 * specified in $params. return_properties are the values that are returned
 * to the calling function
 * 
 * @param array  $params
 * @param array  $returnProperties
 * @param object|array  $sort      object or array describing sort order for sql query.
 * @param int    $offset   the row number to start from
 * @param int    $rowCount the number of rows to return
 * 
 * @return int 
 * @access public 
 */ 
function crm_contact_search( &$params, $return_properties = null, $sort = null, $offset = 0, $row_count = 25) {
    $sortString = CRM_Core_DAO::getSortString( $sort );
    return CRM_Contact_BAO_Query::apiQuery( $params, $return_properties, $sortString, $offset, $row_count );
} 
 



