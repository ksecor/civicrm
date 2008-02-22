<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id: GroupContact.php 12458 2007-11-30 17:00:08Z shot $
 *
 */
require_once 'CRM/Contact/BAO/Group.php';
require_once 'api/v2/utils.php';

function &civicrm_group_add( &$params )
{
    if ( is_null( $params ) || !is_array( $params ) ||  empty ( $params ) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
   
    if ( ( ! CRM_Utils_Array::value('id', $params ) ) &&
         ( ! CRM_Utils_Array::value('name', $params ) ) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
        
    if ( $groupType = CRM_Utils_Array::value( 'group_type', $params ) ) {
        $groupType = explode( ',', $groupType );
        require_once 'CRM/Core/BAO/CustomOption.php';
        $groupType = CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $groupType ) . CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
    }
    
    $group = CRM_Contact_BAO_Group::create( $params );
    
    if ( is_null( $group ) ) {
        return civicrm_create_error( 'Group not created' );
    } else {
        return civicrm_create_success( $group->id );
    }
}

function &civicrm_groups_get( &$params )
{
    if ( !is_null( $params ) && !is_array( $params ) ) {
        return civicrm_create_error( 'Params should be array' );
    }
    
    $groupObjects = CRM_Contact_BAO_Group::getGroups( $params );
    
    if ( count( $groupObjects ) == 0 ) {
        return civicrm_create_error( 'No such group exists' );
    }
    
    $groups       = array( );
    foreach( $groupObjects as $group ) {
        _civicrm_object_to_array( $group, $groups[$group->id] );
    }
    
    return $groups;
}

function &civicrm_group_delete( &$params )
{
    if ( is_null( $params ) || !is_array( $params ) || !CRM_Utils_Array::value( 'id', $params ) ) {
        return civicrm_create_error( 'Required parameter missing' );
    }
    
    CRM_Contact_BAO_Group::discard( $params['id'] );
    return civicrm_create_success( true );
}
?>