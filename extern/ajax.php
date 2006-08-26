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
        return help( );

    case 'search':
        return search( );

    default:
        return;
    }

}

function help( ) {
    $id   = urldecode( $_GET['id'] );
    $file = urldecode( $_GET['file'] );
    echo "<div class=\"crm-help\">You need help for $id in $file</div>";
}

function search( ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    $query = "
SELECT sort_name
  FROM civicrm_contact
 WHERE domain_id = $domainID
   AND LOWER( sort_name ) LIKE '$name%'";
    $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $n = '"' . $dao->sort_name . '"';
        $elements[] = "[ $n, $n ]";
        $count++;
    }

    echo '[' . implode( ',', $elements ) . ']';
}

invoke( );

exit( );
?>