<?

require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';

require_once 'api/utils.php';
require_once 'api/Contact.php';

$config =& CRM_Core_Config::singleton();

$server =& new SoapServer(null, array('uri' => 'http://localhost',
'soap_version' => SOAP_1_2));
$server->addFunction('crm_get_contact');
$server->handle();

?>
