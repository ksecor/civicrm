<?php

function _crm_error( $message, $code = 8000, $level = 'Fatal' ) {
    $error = CRM_Error::singleton( );
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

function _crm_resolve_value( &$params, $name, &$dest, &$values ) {
    if ( ! array_key_exists( $name, $params ) ) {
        return;
    }

    $flip = array_flip( $values );
    if ( ! array_key_exists( $params[$name], $flip ) ) {
        return;
    }

    $dest[ $name . '_id' ] = $flip[$params[$name]];
}

?>