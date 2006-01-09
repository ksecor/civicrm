<?php

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

global $ufClass;

$server =& new SoapServer(null, 
			  array('uri' => 'urn:civicrm',
				'soap_version' => SOAP_1_2));


require_once 'CRM/Utils/SoapServer.php';
$crm_soap =& new CRM_Utils_SoapServer();

/* Cache the real UF, override it with the SOAP environment */
$config =& CRM_Core_Config::singleton();

$server->setClass('CRM_Utils_SoapServer', $config->userFrameworkClass);

$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
// $config->userPermissionClass    = 'CRM_Core_Permission_Soap';

$server->setPersistence(SOAP_PERSISTENCE_SESSION);

$server->handle();

?>
