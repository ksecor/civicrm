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
 * Add or update a contact. If a dupe is found, check for
 * ignoreDupe flag to ignore or return error
 *
 * @param  array   $params           (reference ) input parameters
 * @param  int     $contactID        if present the contact with that ID is updated
 * @param  boolean $dupeCheck        should we do a dupeCheck before attempting to insert?
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_contact_add( &$params, $contactID = null, $dupeCheck = true ) {
    require_once 'api/v2/utils.php';

    _civicrm_initialize( );

    if ( ! $contactID ) {
        $values = civicrm_contact_check_params( $params, $dupeCheck );
        if ( $values ) {
            return $values;
        }
            
    }

    require_once 'CRM/Contact/BAO/Contact.php';
    $contact =& _civicrm_contact_add( $params, $contactID );
    $values = array( );
    if ( is_a( $contact, 'CRM_Core_Error' ) ) {
        $values['error_message'] = $contact->_errors[0]['message'];
        $values['is_error'] = 1;
    } else {
        $values['contact_id'] = $contact->id;
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
 * Delete a contact
 *
 * @param int $contactID  id of contact to be deleted
 *
 * @return boolean        true if success, else false
 * @static void
 * @access public
 */
function civicrm_contact_delete( $contactID ) {
    require_once 'CRM/Contact/BAO/Contact.php';
    return CRM_Contact_BAO_Contact::deleteContact( $contactID );
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

/**
 * This function ensures that we have the right input parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param string $contact_type Which class of contact is being created.
 *            Valid values = 'Individual', 'Household', 'Organization'.
 *                            '
 * @return bool|CRM_Utils_Error
 * @access public
 */
function civicrm_contact_check_params( &$params, $dupeCheck = true ) {
    $required = array(
                      'Individual'   => array(
                                              array( 'first_name', 'last_name' ),
                                              'email',
                                              ),
                      'Household'    => array(
                                              'household_name',
                                              ),
                      'Organization' => array(
                                              'organization_name',
                                              ),
                      );

    // cannot create a contact with empty params
    if ( empty( $params ) ) {
        return _civicrm_create_error( 'Input Parameters empty' );
    }

    if ( ! array_key_exists( 'contact_type', $params ) ) {
        return _civicrm_create_error( 'Contact Type not specified' );
    }

    // contact_type has a limited number of valid values
    $fields = CRM_Utils_Array::value( $params['contact_type'], $required );
    if ( $fields == null ) {
        return _civicrm_create_error( "Invalid Contact Type: {$params['contact_type']}" );
    }

    $valid = false;
    $error = '';
    foreach ( $fields as $field ) {
        if ( is_array( $field ) ) {
            $valid = true;
            foreach ( $field as $element ) {
                if ( ! CRM_Utils_Array::value( $element, $params ) ) {
                    $valid = false;
                    $error .= $element; 
                    break;
                }
            }
        } else {
            if ( CRM_Utils_Array::value( $field, $params ) ) {
                $valid = true;
            }
        }
        if ( $valid ) {
            break;
        }
    }

    if ( ! $valid ) {
        return _civicrm_create_error( "Required fields not found for {$params['contact_type']} $error" );
    }

    if ( $dupeCheck ) {
        // check for record already existing
        require_once 'CRM/Core/BAO/UFGroup.php';
        if ( ( $ids = CRM_Core_BAO_UFGroup::findContact( $params ) ) != null ) {
            return _civicrm_create_error( "Found matching contacts: $ids", 8000, 'Fatal',
                                          $ids );
        }
    }

    return null;
}

/** 
 * takes an associative array and creates a contact object and all the associated 
 * derived objects (i.e. individual, location, email, phone etc) 
 * 
 * @param array $params (reference ) an assoc array of name/value pairs 
 * @param  int     $contactID        if present the contact with that ID is updated
 * 
 * @return object CRM_Contact_BAO_Contact object  
 * @access public 
 * @static 
 */ 
function &_civicrm_contact_add( &$params, $contactID = null ) {
    require_once 'CRM/Utils/Hook.php';

    if ( $contactID ) {
        CRM_Utils_Hook::pre( 'edit', 'Individual', $contactID, $params );
    } else {
        CRM_Utils_Hook::pre( 'create', 'Individual', null, $params ); 
    }

    CRM_Core_DAO::transaction( 'BEGIN' ); 

    $ids = array( );
    if ( $contactID ) {
        $ids['contact'] = $contactID;
    }
    $contact = CRM_Contact_BAO_Contact::add   ( $params, $ids );

    $params['contact_id'] = $contact->id;
    if ( $contactID ) {
        $ids[strtolower( $params['contact_type'] ) ] =
            CRM_Core_DAO::getFieldValue( 'CRM_Contact_BAO_' . $params['contact_type'],
                                         $contactID,
                                         'id',
                                         'contact_id' );
    }
                                     
    require_once "CRM/Contact/BAO/{$params['contact_type']}.php";
    eval( 'CRM_Contact_BAO_' . $params['contact_type'] . '::add( $params, $ids );' );

    $locationTypeId = CRM_Utils_Array::value( 'location_type_id', $params );
    if ( ! $locationTypeId ) {
        require_once 'CRM/Core/BAO/LocationType.php';
        $locationType   =& CRM_Core_BAO_LocationType::getDefault( ); 
        $locationTypeId =  $locationType->id;
    }

    $location =& new CRM_Core_DAO_Location( );
    $location->location_type_id = $locationTypeId;
    $location->entity_table     = 'civicrm_contact';
    $location->entity_id        = $contact->id;
    $location->is_primary       = true;

    $location->find( true );

    $location->save( );
        
    $address =& new CRM_Core_BAO_Address();
    $address->location_id = $location->id;
    $address->find( true );

    CRM_Core_BAO_Address::fixAddress( $params );
    if ( ! $address->copyValues( $params ) ) {
        $address->save( );
    }

    $phone =& new CRM_Core_BAO_Phone();
    $phone->location_id = $location->id;
    $phone->is_primary = true;
    $phone->find( true );
    if ( ! $phone->copyValues( $params ) ) {
        $phone->save( );
    }
        
    $email =& new CRM_Core_BAO_Email();
    $email->location_id = $location->id;
    $email->is_primary = true;
    $email->find( true );
    if ( ! $email->copyValues( $params ) ) {
        $email->save( );
    }

    /* Process custom field values and other values */
    foreach ($params as $key => $value) {
        if ( $key == 'group' ) {
            CRM_Contact_BAO_GroupContact::create( $params['group'], $contact->id );
        } else if ( $key == 'tag' ) {
            require_once 'CRM/Core/BAO/EntityTag.php';
            CRM_Core_BAO_EntityTag::create( $params['tag'], $contact->id );
        } else if ($cfID = CRM_Core_BAO_CustomField::getKeyID($key) ) {
            $custom_field_id = $cfID;
            $cf =& new CRM_Core_BAO_CustomField();
            $cf->id = $custom_field_id;
            if ( $cf->find( true ) ) {
                switch($cf->html_type) {

                case 'Select Date':
                    $date = CRM_Utils_Date::format( $value );
                    if ( ! $date ) {
                        $date = '';
                    }
                    $customValue = $date;
                    break;

                case 'CheckBox':
                    $customValue =
                        CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
                        implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value)) .
                        CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
                    break;

                    //added a case for Multi-Select
                case 'Multi-Select':
                    $customValue = 
                        CRM_Core_BAO_CustomOption::VALUE_SEPERATOR . 
                        implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, array_keys($value)) . 
                        CRM_Core_BAO_CustomOption::VALUE_SEPERATOR;
                    break;

                default:
                    $customValue = $value;
                }
            }
            
            CRM_Core_BAO_CustomValue::updateValue($contact->id, $custom_field_id, $customValue);
        }
    }

    CRM_Core_DAO::transaction( 'COMMIT' ); 

    if ( $contactID ) {
        CRM_Utils_Hook::post( 'edit', 'Individual', $contact->id, $contact );
    } else {
        CRM_Utils_Hook::post( 'create', 'Individual', $contact->id, $contact );
    }

    return $contact;
}

?>