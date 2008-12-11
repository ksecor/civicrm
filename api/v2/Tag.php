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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id: Contribute.php 9526 2007-05-12 19:36:28Z deepak $
 *
 */

require_once 'api/v2/utils.php';

/**
 *  Add a Tag. Tags are used to classify CRM entities (including Contacts, Groups and Actions).
 *
 * @param   array   $params          an associative array used in
 *                                   construction / retrieval of the
 *                                   object
 * 
 * @return array of newly created tag property values.
 * @access public
 */
function civicrm_tag_create( &$params ) 
{
    _civicrm_initialize( );
    
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }
    
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Input parameters is not an array' ) );
    }
    
    $error = _civicrm_check_required_fields($params, 'CRM_Core_DAO_Tag');
    
    if ( $error['is_error'] ) {
        return civicrm_create_error( $error['error_message'] );
    }
    
    require_once 'CRM/Core/BAO/Tag.php';
    $ids    = array();
    $tagBAO = CRM_Core_BAO_Tag::add($params, $ids);
    
    if ( is_a( $tagBAO, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Tag is not created" );
    } else {
        $values = array( );
        _civicrm_object_to_array($tagBAO, $values);
        $tag = array( );
        $tag['tag_id']   = $values['id'];
        $tag['is_error'] = 0;
    }
    return $tag;
}

/**
 * Deletes an existing Tag
 *
 * @param  array  $params
 * 
 * @return boolean | error  true if successfull, error otherwise
 * @access public
 */
function civicrm_tag_delete( &$params ) 
{
    $tagID = CRM_Utils_Array::value( 'tag_id', $params );
    if ( ! $tagID ) {
        return civicrm_create_error( ts( 'Could not find tag_id in input parameters' ) );
    }
    
    require_once 'CRM/Core/BAO/Tag.php';
    return CRM_Core_BAO_Tag::del( $tagID ) ? civicrm_create_success( ) : civicrm_create_error(  ts( 'Could not delete tag' )  );
}

