<?php

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';


// build the query
function invoke( ) {
    // intialize the system
    $config =& CRM_Core_Config::singleton( );

    $q = $_GET['q'];
    $args = explode( '/', $q );
    if ( $args[0] != 'civicrm' ) {
        exit( );
    }

    switch ( $args[1] ) {

    case 'help':
        return help( $config );

    case 'search':
        return search( $config );

    case 'status':
        return status( $config );

    case 'zandigo':
        return zandigo( $config );

    default:
        return;
    }

}

function help( &$config ) {
    $id   = urldecode( $_GET['id'] );
    $file = urldecode( $_GET['file'] );

    $template =& CRM_Core_Smarty::singleton( );
    $file = str_replace( '.tpl', '.hlp', $file );

    $template->assign( 'id', $id );
    echo $template->fetch( $file );
}

function search( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    $query = "
SELECT sort_name
  FROM civicrm_contact
 WHERE domain_id = $domainID
   AND sort_name LIKE '$name%'
ORDER BY sort_name
LIMIT 6";

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->sort_name, $dao->sort_name );
        $count++;
    }

    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

function zandigo( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
    $type     = strtolower( CRM_Utils_Type::escape( $_GET['t'], 'String'  ) );
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    if ( $type == 'f' ) {
        $var   = 'first_name';
        $table = 'civicrm_individual';
    } else if ( $type == 'l' ) {
        $var   = 'last_name';
        $table = 'civicrm_individual';
    } else if ( $type == 'n' ) {
        $var   = 'organization_name';
        $table = 'civicrm_organization';
    } else if ( $type == 'e' ) {
        $var   = 'email';
        $table = 'civicrm_email';
    }

    if ( $type == 'e' ) {
        $query = "
SELECT $var
  FROM civicrm_email    e,
       civicrm_contact  c,
       civicrm_location l
 WHERE domain_id      = $domainID
   AND c.id           = l.entity_id
   AND l.entity_table = 'civicrm_contact'
   AND e.location_id  = l.id 
   AND $var LIKE '$name%'
ORDER BY $var
LIMIT 6";
    } else {
        $query = "
SELECT $var
  FROM $table t, civicrm_contact c
 WHERE domain_id = $domainID
   AND c.id = t.contact_id
   AND $var LIKE '$name%'
ORDER BY $var
LIMIT 6";
    }

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->$var, $dao->$var );
        $count++;
    }

    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

function status( &$config ) {
    // make sure we get an id
    if ( ! isset( $_GET['id'] ) ) {
        return;
    }

    $file = "{$config->uploadDir}status_{$_GET['id']}.txt";
    if ( file_exists( $file ) ) {
        $str = file_get_contents( $file );
        echo $str;
    } else {
        require_once 'Services/JSON.php';
        $json =& new Services_JSON( );
        $status = "<div class='description'>&nbsp; " . ts('No processing status reported yet.') . "</div>";
        echo $json->encode( array( 0, $status ) );
    }
}

invoke( );

exit( );
?>