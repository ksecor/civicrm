<?
require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

$config =& CRM_Core_Config::singleton();
$server =& new SoapServer(null, 
            array('uri' => 'http://localhost', 'soap_version' => SOAP_1_2));

$api = array();

/* Contact functions */
$api += array('crm_get_contact');

/* Mailer functions */
$api += array(
            'crm_mailer_event_bounce', 
            'crm_mailer_event_unsubscribe',
            'crm_mailer_event_subscribe'
        );

foreach ($api as $function) {
    $server->addFunction($function);
}

$server->handle();

?>
