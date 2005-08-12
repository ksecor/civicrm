<?
define('CIVICRM_USERFRAMEWORK', 'Soap');

require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

$config =& CRM_Core_Config::singleton();
$server =& new SoapServer(null, 
            array('uri' => 'http://localhost', 'soap_version' => SOAP_1_2));

/* Contact functions */
$contact_api = array('crm_get_contact');

$server->addFunction($contact_api);

/* Mailer functions */
$mailer_api = array(
            'crm_mailer_event_bounce', 
            'crm_mailer_event_confirm', 
            'crm_mailer_event_unsubscribe',
            'crm_mailer_event_subscribe'
        );

$server->addFunction($mailer_api);

$server->handle();

?>
