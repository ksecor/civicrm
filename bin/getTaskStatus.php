<?php

ini_set( 'include_path', ".:../packages:.." );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

$config =& CRM_Core_Config::singleton( );

function user_access( $str ) {
    return true;
}

function module_list( ) {
    return array( );
}

// get all the task status
require_once 'CRM/Project/DAO/TaskStatus.php';
$dao =& new CRM_Project_DAO_TaskStatus( );

$dao->find( );
while ( $dao->fetch( ) ) {
    // only interested in in-progress
    if ( $dao->status_id == 327 ) {
        $completed = unserialize( $dao->status_detail );
        $total = $filled = 0;
        foreach ( $completed as $key => $value ) {
            if ( substr( $key, 0, 7 ) == 'Income-' ) {
                continue;
            }

            $total++;
            if ( $value ) {
                $filled++;
            }
        }

        $percent = number_format( (float ) $filled * 100.0 / (float ) $total, 2 );
        if ( $percent > 95.00 ) {
            echo "{$dao->status_id}, {$dao->target_entity_id}: $percent\n";
        }
    }
 }

?>
