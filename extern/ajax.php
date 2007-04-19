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

    case 'state':
        return state( $config );

    case 'country':
        return country( $config );

    case 'status':
        return status( $config );

    case 'event':
        return event( $config );

    case 'message':
        return message( $config );

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
SELECT sort_name, id
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
        $elements[] = array( $dao->sort_name, $dao->id );
        $count++;
    }

    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

function event( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    $query = "
SELECT title
  FROM civicrm_event
 WHERE domain_id = $domainID
   AND title LIKE '$name%'
ORDER BY title
LIMIT 6";

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->title, $dao->title );
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

function message( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );

    $query = "
SELECT id, msg_title,msg_text,msg_subject
  FROM civicrm_msg_template
 WHERE domain_id = $domainID
 AND is_active =1 
ORDER BY msg_title
LIMIT 6";

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->msg_title,
                             $dao->msg_text . "^A" . $dao->msg_subject,
                             $dao->id );
        $count++;
    }
    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

function state( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    $query = "
SELECT name
  FROM civicrm_state_province
 WHERE name LIKE '$name%'
ORDER BY name";

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->name, $dao->name );
        $count++;
    }

    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

function country( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['node'], 'String'  ) );

    $query = "
SELECT civicrm_country.name name
  FROM civicrm_state_province, civicrm_country
 WHERE civicrm_country.id = civicrm_state_province.country_id 
   AND civicrm_state_province.name LIKE '$name%'
ORDER BY name";

    $nullArray = array( );
    $dao = CRM_Core_DAO::executeQuery( $query, $nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $elements[] = array( $dao->name, $dao->name );
        $count++;
    }

    require_once 'Services/JSON.php';
    $json =& new Services_JSON( );
    echo $json->encode( $elements );
}

invoke( );

exit( );
?>