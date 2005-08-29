<?
require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

global $ufClass;

$server =& new SoapServer(null, 
            array('uri' => 'urn:civicrm', 'soap_version' => SOAP_1_2));


$crm_soap =& new CRM_Utils_SoapServer();

/* Cache the real UF, override it with the SOAP environment */
$config =& CRM_Core_Config::singleton();

$server->setClass('CRM_Utils_SoapServer', $config->userFrameworkClass);

$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
// $config->userPermissionClass    = 'CRM_Core_Permission_Soap';


$session =& CRM_Core_Session::singleton();

$session->set('key', 'abcde');

$server->setPersistence(SOAP_PERSISTENCE_SESSION);


// /* Contact functions */
// $contact_api = array('crm_get_contact');

// $server->addFunction($contact_api);

// /* Mailer functions */
// $mailer_api = array(
//             'crm_mailer_event_bounce', 
//             'crm_mailer_event_confirm', 
//             'crm_mailer_event_domain_unsubscribe',
//             'crm_mailer_event_unsubscribe',
//             'crm_mailer_event_subscribe',
//             'crm_mailer_event_reply'
//         );

// $server->addFunction($mailer_api);

$server->handle();

?>
