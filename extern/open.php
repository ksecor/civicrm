<?

require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
// require_once 'CRM/Core/I18n.php';

$config =& CRM_Core_Config::singleton();


CRM_Core_Error::debug('c', $config);

$queue_id = $_GET['q'];
CRM_Mailing_Event_BAO_Opened::open($queue_id);

$filename = "{$config->resourceBase}/i/tracker.gif";

header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Content-type: image/gif');
header('Content-Length: ' . filesize($filename));

header('Content-Disposition: attachment; filename=tracker.gif');

readfile($filename);

exit();

?>
