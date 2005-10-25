<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * Definition of the Custom Group of the CRM API. 
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

require_once 'CRM/Core/BAO/UFGroup.php';

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
 * Get all the user framework groups 
 * 
 * @access public                                         
 * @return array - array reference of all groups. 
 * @static 
 */ 
function &crm_uf_get_profile_groups( ) {
    return CRM_Core_PseudoConstant::ufGroup( );
}

/** 
 * Get the form title. 
 * 
 * @param int $id id of uf_form 
 * @return string title 
 * 
 * @access public 
 * @static 
 * 
 */ 
function crm_uf_get_profile_title ( $id ) {
    return CRM_Core_BAO_UFGroup::getTitle( $id );
}

/** 
 * get all the fields that belong to the group with the named title 
 * 
 * @param int $id       the id of the UF group 
 * @param int $register are we interested in registration fields 
 * @param int $action   what action are we doing 
 * @param int $match    are we interested in match fields 
 * @param string $visibility visibility of fields we are interested in 
 * 
 * @return array the fields that belong to this title 
 * @static 
 * @access public 
 */ 
function crm_uf_get_profile_fields ( $id, $register = false, $action = null, $match = false, $visibility = null ) {
    return CRM_Core_BAO_UFGroup::getFields( $id, $register, $action, $match, $visibility );
}

/** 
 * get the html for the form that represents this particular group 
 * 
 * @param int     $userID   the user id that we are actually editing 
 * @param string  $title    the title of the group we are interested in 
 * @param int     $action   the action of the form 
 * @param boolean $register is this the registration form 
 * @param boolean $reset    should we reset the form? 
 * 
 * @return string       the html for the form 
 * @static 
 * @access public 
 */ 
function crm_uf_get_profile_html  ( $userID, $title, $action = null, $register = false, $reset = false ) {
    return CRM_Core_BAO_UFGroup::getEditHTML( $userID, $title, $action, $register, $reset );
}

/** 
 * get the contact_id given a uf_id 
 * 
 * @param int $ufID
 * 
 * @return int contact_id 
 * @access public    
 * @static 
 */ 
function crm_uf_get_match_id ( $ufID ) {
    require_once 'CRM/Core/BAO/UFMatch.php';
    return CRM_Core_BAO_UFMatch::getContactId( $ufID );
}

?>
