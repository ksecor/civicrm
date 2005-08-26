<?
require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

global $ufClass;

$server =& new SoapServer(null, 
            array('uri' => 'urn:civicrm', 'soap_version' => SOAP_1_2));

// $server->setPersistence(SOAP_PERSISTENCE_SESSION);

/* Cache the real UF, override it with the SOAP environment */
$config =& CRM_Core_Config::singleton();
$ufClass = $config->userFrameworkClass;
$config->userFramework          = 'Soap';
$config->userFrameworkClass     = 'CRM_Utils_System_Soap';
$config->userPermissionClass    = 'CRM_Core_Permission_Soap';

$session =& CRM_Core_Session::singleton();


function authenticate($name, $password) {
    global $ufClass;

    eval('$result = ' . $ufClass . '::authenticate($name, $password);');

    if (empty($result)) {
        return null;
    }

    return $result[2];
}


function ping($var) {
    return "PONG: $var";
}

$server->addFunction('authenticate');
$server->addFunction('ping');

/* Contact functions */
// $contact_api = array('crm_get_contact');

// $server->addFunction($contact_api);

/* Mailer functions */
$mailer_api = array(
            'crm_mailer_event_bounce', 
            'crm_mailer_event_confirm', 
            'crm_mailer_event_domain_unsubscribe',
            'crm_mailer_event_unsubscribe',
            'crm_mailer_event_subscribe',
            'crm_mailer_event_reply'
        );

$server->addFunction($mailer_api);

$server->handle();

?>
