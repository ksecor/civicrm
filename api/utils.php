<?php

function _crm_error( $message, $code = 8000, $level = 'Fatal' ) {
    $error = CRM_Core_Error::singleton( );
    $error->push( $code, $level, array( ), $message );
    return $error;
}

function _crm_store_values( &$fields, &$params, &$values ) {
    $valueFound = false;
    foreach ( $fields as $name => &$field ) {
        // ignore all ids for now
        if ( $name === 'id' || substr( $name, -1, 3 ) === '_id' ) {
            continue;
        }

        if ( array_key_exists( $name, $params ) ) {
            $values[$name] = $params[$name];
            $valueFound = true;
        }
    }
    return $valueFound;
}

function _crm_update_object( &$object, &$values ) {
    $fields =& $object->fields( );

    $valueFound = false;
    foreach ( $fields as $name => &$field ) {
        // ignore all ids for now
        if ( $name === 'id' ) {
            continue;
        }

        if ( array_key_exists( $name, $values ) ) {
            $object->$name = $values[$name];
            if ( substr( $name, -1, 3 ) !== '_id' ) {
                $valueFound = true;
            }
        }
    }

    if ( $valueFound ) {
        $object->save( );
    }
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
function _crm_check_params( &$params, $contact_type = 'Individual' ) {
    static $required = array(
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
        return _crm_error( 'Input Parameters empty' );
    }

    // contact_type has a limited number of valid values
    $fields = CRM_Utils_Array::value( $contact_type, $required );
    if ( $fields == null ) {
        return _crm_error( "Invalid Contact Type: $contact_type" );
    }

    $valid = false;
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
        return _crm_error( "Required fields not found for $contact_type $error" );
    }
    
    return true;
}

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *                            '
 * @return array|CRM_Error
 * @access public
 */
function _crm_format_params( &$params, &$values ) {
    // copy all the contact and contact_type fields as is
    $fields =& CRM_Contact_DAO_Contact::fields( );
    _crm_store_values( $fields, $params, $values );

    eval( '$fields =& CRM_Contact_DAO_' . $values['contact_type'] . '::fields( );' );
    _crm_store_values( $fields, $params, $values );

    $locationTypeNeeded = false;

    $values['location']               = array( );
    $values['location'][1]            = array( );
    $fields =& CRM_Contact_DAO_Location::fields( );
    if ( _crm_store_values( $fields, $params, $values['location'][1] ) ) {
        $locationTypeNeeded = true;
    }
    if ( array_key_exists( 'location_type', $params ) ) {
        $values['location'][1]['location_type'] = $params['location_type'];
        $locationTypes = array_flip( CRM_Core_PseudoConstant::locationType( ) );
        $values['location'][1]['location_type_id'] = $locationTypes[$params['location_type']];
    }

    $values['location'][1]['address'] = array( );
    $fields =& CRM_Contact_DAO_Address::fields( );
    if ( _crm_store_values( $fields, $params, $values['location'][1]['address'] ) ) {
        $locationTypeNeeded = true;
    }
    $ids = array( 'county', 'country', 'state_province' );
    foreach ( $ids as $id ) {
        if ( array_key_exists( $id, $params ) ) {
            $values['location'][1]['address'][$id] = $params[$id];
            $locationTypeNeeded = true;
        }
    }

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);
        $values['location'][1][$name]    = array( );
        $values['location'][1][$name][1] = array( );
        eval( '$fields =& CRM_Contact_DAO_' . $block . '::fields( );' );
        if ( _crm_store_values( $fields, $params, $values['location'][1][$name][1] ) ) {
            $locationTypeNeeded = true;
            $values['location'][1][$name][1]['is_primary'] = 1;
        }
    }

    // make sure phone and email are valid strings
    if ( array_key_exists( 'email', $params ) &&
         ! CRM_Utils_Rule::email( $params['email'] ) ) {
        return _crm_error( "Email not valid " . $params['email'] );
    }

    if ( array_key_exists( 'phone', $params ) &&
         ! CRM_Utils_Rule::phone( $params['phone'] ) ) {
        return _crm_error( "Phone not valid " . $params['phone'] );
    }
    
    if ( array_key_exists( 'im_name', $params ) ) {
        $values['location'][1]['im'][1]['name'] = $params['im_name'];
        $locationTypeNeeded = true;
    }

    if ( array_key_exists( 'im_provider', $params ) ) {
        $values['location'][1]['im'][1]['provider'] = $params['im_provider'];
        $locationTypeNeeded = true;
    }

    if ( $locationTypeNeeded &&
         ! array_key_exists( 'location_type_id', $values['location'][1] ) ) {
        return _crm_error( "Location Type not defined" );
    }

    if ( $locationTypeNeeded ) {
        $values['location'][1]['is_primary'] = true;
    }

    CRM_Contact_BAO_Contact::resolveDefaults( $values, true );
    return null;
}

function _crm_update_contact( $contact, $values ) {
    // fix sort_name and display_name
    if ( $contact->contact_type == 'Individual' ) {
        $firstName = CRM_Utils_Array::value( 'first_name', $values );
        if ( ! $firstName ) {
            $firstName = isset( $contact->contact_type_object->first_name ) ? $contact->contact_type_object->first_name : '';
        }
        
        $middleName = CRM_Utils_Array::value( 'middle_name', $values );
        if ( ! $middleName ) {
            $middleName = isset( $contact->contact_type_object->middle_name ) ? $contact->contact_type_object->middle_name : '';
        }
        
        $lastName = CRM_Utils_Array::value( 'last_name', $values );
        if ( ! $lastName ) {
            $lastName = isset( $contact->contact_type_object->last_name ) ? $contact->contact_type_object->last_name : '';
        }

        $values['sort_name'] = "$lastName, $firstName";
        $values['display_name'] = "$firstName $middleName $lastName";
    } else if ( $contact->contact_type == 'Household' ) {
        $householdName = CRM_Utils_Array::value( 'household_name', $values );
        if ( ! $householdName ) {
            $householdName = isset( $contact->contact_type_object->household_name ) ? $contact->contact_type_object->household_name : '';
        }
        $values['sort_name'] = $householdName;
    } else {
        $organizationName = CRM_Utils_Array::value( 'organization_name', $values );
        if ( ! $organizationName ) {
            $organizationName = isset( $contact->contact_type_object->organization_name ) ? $contact->contact_type_object->organization_name : '';
        }
        $values['sort_name'] = $organizationName;
    }

    _crm_update_object( $contact, $values );

    // fix display_name
    _crm_update_object( $contact->contact_type_object, $values );

    if ( ! isset( $contact->location ) ) {
        $contact->location    = array( );
    }
    if ( ! array_key_exists( 0, $contact->location ) || empty( $contact->location[1] ) ) {
        $contact->location[1] = new CRM_Contact_BAO_Location( );
    }

    $values['location'][1]['contact_id'] = $contact->id;
    _crm_update_object( $contact->location[1], $values['location'][1] );

    if ( ! isset( $contact->location[1]->address ) ) {
        $contact->location[1]->address = new CRM_Contact_BAO_Address( );
    }
    $values['location'][1]['address']['location_id'] = $contact->location[1]->id;
    _crm_update_object( $contact->location[1]->address, $values['location'][1]['address'] );

    $blocks = array( 'Email', 'Phone', 'IM' );
    foreach ( $blocks as $block ) {
        $name = strtolower($block);

        if ( ! isset( $contact->location[1]->$name ) ) {
            $contact->location[1]->$name = array( );
            eval( '$contact->location[1]->{$name}[1] = new CRM_Contact_BAO_' . $block . '( );' );
        }
        $values['location'][1][$name][1]['location_id'] = $contact->location[1]->id;
        _crm_update_object( $contact->location[1]->{$name}[1], $values['location'][1][$name][1] );
    }
    return $contact;
}

/**
 * This function ensures that we have the right input parameters
 *
 * We also need to make sure we run all the form rules on the params list
 * to ensure that the params are valid
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new history.
 *
 * @return bool|CRM_Utils_Error
 * @access public
 */
function _crm_check_history_params(&$params)
{
    static $required = array('entity_id', 'activity_id');
    
    // cannot create a contact with empty params
    if (empty($params)) {
        return _crm_error('Input Parameters empty');
    }

    $valid = false;
    foreach ($required as $requiredField) {
        if (!CRM_Utils_Array::value($requiredField, $params)) {
            $valid = false;
            $error .= "$requiredField "; 
        }
    }

    if (!$valid) {
        return _crm_error("Required fields $error not found for history");
    }
    return true;
}

?>