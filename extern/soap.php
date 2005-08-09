<?

require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

$config =& CRM_Core_Config::singleton();

$server =& new SoapServer(null, array('uri' => 'http://localhost',
'soap_version' => SOAP_1_2));

/* Contact functions */
$server->addFunction('crm_get_contact');

/* Mailer functions */
$server->addFunction('crm_mailer_event_bounce');
$server->addFunction('crm_mailer_event_unsubscribe');

$server->handle();

?>
