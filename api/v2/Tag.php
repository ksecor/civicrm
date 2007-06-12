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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
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
function crm_tag_create( &$params ) 
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
 * @param  id  $tag    Id of the tag to be deleted
 * 
 * @return NULL | error  null if successfull, error otherwise
 * @access public
 */
function crm_tag_delete( &$tagId ) 
{
    if ( ! $tagId ) {
        return civicrm_create_error( 'Invalid value for tagId' );
    }
    
    require_once 'CRM/Core/BAO/Tag.php';
    return CRM_Core_BAO_Tag::del( $tagId ) ? null : civicrm_create_error('Error while deleting tag');
}

?>