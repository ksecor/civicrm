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
 * $Id$
 *
 */

require_once 'api/v2/utils.php';

function civicrm_entity_tag_get( &$params ) {
    if ( ! array_key_exists( 'contact_id', $params ) ) {
        return civicrm_create_error( ts( 'contact_id is a required field' ) );
    }

    require_once 'CRM/Core/BAO/EntityTag.php';
    $values =& CRM_Core_BAO_EntityTag::getTag( $params['contact_id'] );
    $result = array( );
    foreach ( $values as $v ) {
        $result[] = array( 'tag_id' => $v );
    }
    return $result;
}

function civicrm_entity_tag_add( &$params ) {
    return civicrm_entity_tag_common( $params, 'add' );
}

function civicrm_entity_tag_remove( &$params ) {
    return civicrm_entity_tag_common( $params, 'remove' );
}

function civicrm_entity_tag_common( &$params, $op = 'add' ) {
    $contactIDs = array( );
    $tagsIDs    = array( );
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 10 ) == 'contact_id' ) {
            $contactIDs[] = $v;
        } else if ( substr( $n, 0, 6 ) == 'tag_id' ) {
            $tagIDs[] = $v;
        }
    }
    if ( empty( $contactIDs ) ) {
        return civicrm_create_error( ts( 'contact_id is a required field' ) );
    }

    if ( empty( $tagIDs ) ) {
        return civicrm_create_error( ts( 'tag_id is a required field' ) );
    }

    require_once 'CRM/Core/BAO/EntityTag.php';
    $values = array( 'is_error' => 0 );
    if ( $op == 'add' ) {
        $values['total_count'] = $values['added'] = $values['not_added'] = 0;
        foreach ( $tagIDs as $tagID ) {
            list( $tc, $a, $na ) = 
                CRM_Core_BAO_EntityTag::addContactsToTag( $contactIDs, $tagID );
            $values['total_count'] += $tc;
            $values['added']       += $a;
            $values['not_added']   += $na;
        }
    } else {
        $values['total_count'] = $values['removed'] = $values['not_removed'] = 0;
        foreach ( $tagIDs as $tagID ) {
            list( $tc, $r, $nr ) = 
                CRM_Core_BAO_EntityTag::removeContactsFromTag( $contactIDs, $tagID );
            $values['total_count'] += $tc;
            $values['removed']     += $r;
            $values['not_removed'] += $nr;
        }
    }
    return $values;
}
