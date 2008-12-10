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
 * A PHP cron script to format all the addresses in the database. Currently
 * it only does geocoding if the geocode values are not set. At a later
 * stage we will also handle USPS address cleanup and other formatting
 * issues
 *
 */

function run( ) {
    session_start( );                               

    require_once '../civicrm.config.php'; 
    require_once 'CRM/Core/Config.php'; 
    
    $config =& CRM_Core_Config::singleton(); 

    require_once 'Console/Getopt.php';
    $shortOptions = "n:p:s:e:";
    $longOptions  = array( 'name=', 'pass=', 'start=', 'end=' );

    $getopt  = new Console_Getopt( );
    $args = $getopt->readPHPArgv( );
    array_shift( $args );
    list( $valid, $dontCare ) = $getopt->getopt2( $args, $shortOptions, $longOptions );

    $vars = array(
                  'start' => 's',
                  'end'   => 'e',
                  'name'  => 'n',
                  'pass'  => 'p' );

    foreach ( $vars as $var => $short ) {
        $$var = null;
        foreach ( $valid as $v ) {
            if ( $v[0] == $short || $v[0] == "--$var" ) {
                $$var = $v[1];
                break;
            }
        }
        if ( ! $$var ) {
            $$var = CRM_Utils_Array::value( $var, $_REQUEST );
        }
    }

    // this does not return on failure
    // require_once 'CRM/Utils/System.php';
    CRM_Utils_System::authenticateScript( true, $name, $pass );

    // check that we have a geocodeMethod
    if ( empty( $config->geocodeMethod ) ) {
        echo ts( 'Error: You need to set a mapping provider under Global Settings' );
        exit( );
    }

    $config->userFramework      = 'Soap'; 
    $config->userFrameworkClass = 'CRM_Utils_System_Soap'; 
    $config->userHookClass      = 'CRM_Utils_Hook_Soap';


    // we have an exclusive lock - run the mail queue
    processContacts( $config, $start, $end );
}

function processContacts( &$config, $start = null, $end = null ) {
    $contactClause = array( );
    if ( $start ) {
        $contactClause[] = "c.id >= $start";
    }
    if ( $end ) {
        $contactClause[] = "c.id <= $end";
    }
    if ( ! empty( $contactClause ) ) {
        $contactClause = ' AND ' . implode( ' AND ', $contactClause );
    } else {
        $contactClause = null;
    }

    $query = "
SELECT     c.id,
           a.id as address_id,
           a.street_address,
           a.city,
           a.postal_code,
           s.name as state,
           o.name as country
FROM       civicrm_contact  c
INNER JOIN civicrm_address        a ON a.contact_id = c.id
INNER JOIN civicrm_country        o ON a.country_id = o.id
LEFT  JOIN civicrm_state_province s ON a.state_province_id = s.id
WHERE      c.id           = a.contact_id
  AND      ( a.geo_code_1 is null OR a.geo_code_1 = 0 )
  AND      ( a.geo_code_2 is null OR a.geo_code_2 = 0 )
  AND      a.country_id is not null
  AND      a.state_province_id is not null
  AND      a.state_province_id = s.id
  AND      a.country_id = o.id
  $contactClause
ORDER BY a.id
";
    
    $totalGeocoded = $totalAddresses = 0;

    $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
    
    require_once( str_replace('_', DIRECTORY_SEPARATOR, $config->geocodeMethod ) . '.php' );
    require_once 'CRM/Core/DAO/Address.php';
    while ( $dao->fetch( ) ) {
        $totalAddresses++;
        $params = array( 'street_address'    => $dao->street_address,
                         'postal_code'       => $dao->postal_code,
                         'city'              => $dao->city,
                         'state_province'    => $dao->state,
                         'country'           => $dao->country );

        // loop through the address removing more information
        // so we can get some geocode for a partial address
        // i.e. city -> state -> country

        $maxTries = 5;
        do {
            eval( $config->geocodeMethod . '::format( $params, true );' );
            array_shift( $params );
            $maxTries--;
        } while ( ( ! isset( $params['geo_code_1'] ) ) &&
                  ( $maxTries > 1 ) );
            
        if ( isset( $params['geo_code_1'] ) ) {
            $address = new CRM_Core_DAO_Address( );
            $address->id = $dao->address_id;
            $address->geo_code_1 = $params['geo_code_1'];
            $address->geo_code_2 = $params['geo_code_2'];
            $address->save( );
            $totalGeocoded++;
        }
    }

    echo ts( "Addresses Evaluated: $totalAddresses\n" );
    echo ts( "Addresses Geocoded : $totalGeocoded\n" );
    return;
}

run( );


