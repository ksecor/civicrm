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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/


/**
 * Definition of the CRM API. For more detailed documentation, please check:
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

require_once 'PEAR.php';
require_once 'CRM/Core/Error.php';
require_once 'api/utils.php';

require_once 'api/Contact.php';
require_once 'api/Group.php';
require_once 'api/History.php';
require_once 'api/CustomGroup.php';
require_once 'api/UFGroup.php';
require_once 'api/Search.php';
require_once 'api/Relationship.php';

require_once 'CRM/Contact/BAO/Group.php';



/**
 * Create an additional location for an existing contact
 *
 * @param CRM_Contact $contact     Contact object to be deleted
 * @param array       $params      input properties
 * @param enum        context_name Name of a valid Context
 *  
 * @return CRM_Contact or CRM_Error (db error or contact was not valid)
 *
 * @access public
 */
function crm_create_location(&$contact, $params) {
    _crm_initialize( );

    $values = array(
        'contact_id'    => $contact->id,
        'location'      => array(1 => array()),
    );

    $loc =& $values['location'][1];

    $loc['address'] = array( );
    $fields =& CRM_Contact_DAO_Address::fields( );
    _crm_store_values($fields, $params, $loc['address']);
    $ids = array( 'county', 'country', 'state_province', 'supplemental_address_1', 'supplemental_address_2', 'StateProvince.name' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $loc['address'][$id] = $params[$id];
        }
    }

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $loc[$name]    = array( );
        $loc[$name][1] = array( );
        require_once(str_replace('_', DIRECTORY_SEPARATOR, "CRM_Contact_DAO_" . $block) . ".php");
        eval( '$fields =& CRM_Contact_DAO_' . $block . '::fields( );' );
        _crm_store_values( $fields, $params, $loc[$name][1] );
    }
    $loc['location_type_id'] = $params['location_type_id'];
 
    $ids = array();
    CRM_Core_BAO_Location::add($values, $ids, null);
    return $contact;
}

function crm_update_location(&$contact, $context_name, $params) {
}

function crm_delete_location(&$contact, $context_name) {
}

function crm_get_locations(&$contact) {
}

function crm_create_extended_property_group($class_name, $params) {
}

function crm_create_extended_property(&$property_group, $params) {
}

?>