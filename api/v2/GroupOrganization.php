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
 * File for the CiviCRM APIv2 group contact functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Group
 *
 * @copyright CiviCRM LLC (c) 2004-2009
 * @version $Id: GroupContact.php 21624 2009-06-04 22:02:55Z mover $
 *
 */

/**
 * Include utility functions
 */
require_once 'api/v2/utils.php';

/**
 * This API will give list of the groups for particular contact 
 * Particualr status can be sent in params array
 * If no status mentioned in params, by default 'added' will be used
 * to fetch the records
 * 
 * @param  array $params  name value pair of contact information
 *
 * @return  array  list of groups, given contact subsribed to
 */
function civicrm_group_organization_get( &$params ) 
{
    if ( ! array_key_exists( 'contact_id', $params ) &&
         ! array_key_exists( 'group_id', $params ) ) {
        return civicrm_create_error( ts( 'at least one of contact_id or group_id is a required field' ) );
    }

    require_once 'CRM/Contact/DAO/GroupOrganization.php';
    $dao = new CRM_Contact_DAO_GroupOrganization();
    if ( array_key_exists( 'contact_id', $params ) ) {
        $dao->contact_id = $params['contact_id'];
    }
    if ( array_key_exists( 'group_id', $params ) ) {
        $dao->group_id = $params['group_id'];
    }
    $values = _civicrm_object_to_array( $dao->find() );
    return $values;
}

/**
 *
 * @param <type> $params
 * @return <type>
 */
function civicrm_group_organization_create( &$params ) 
{
    require_once 'CRM/Contact/BAO/GroupOrganization.php';
    return CRM_Contact_BAO_GroupOrganization::add( $params );
}

/**
 *
 * @param <type> $params
 * @return <type>
 */
function civicrm_group_organization_remove( &$params ) 
{
    require_once 'CRM/Contact/DAO/GroupOrganization.php';
    $dao = new CRM_Contact_DAO_GroupOrganization();
    $dao->copyValues( $params );
    return $dao->delete( );
}