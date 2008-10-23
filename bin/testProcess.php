<?php

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton( );

require_once 'CRM/Case/XMLProcessor/Process.php';
$xmlProcessor = new CRM_Case_XMLProcessor_Process( );
$result = $xmlProcessor->get( 'Substance Abuse', 'CaseRoles' );
CRM_Core_Error::debug( 'Case Roles', $result );
$result = $xmlProcessor->get( 'Substance Abuse', 'ActivityTypes' );
CRM_Core_Error::debug( 'Activity Types', $result );
$result = $xmlProcessor->get( 'Substance Abuse', 'ActivitySets' );
CRM_Core_Error::debug( 'Activity Sets', $result );
exit( );

$params = array( 'clientID'         => 104,
                 'creatorID'        => 108,
                 'standardTimeline' => 1,
                 'cleanupDatabase'  => 1,
                 'dueDateTime'      => time( ),
                 'caseID'           => 1,
                 );

$xmlProcessor->run( 'Substance Abuse',
                    $params );
