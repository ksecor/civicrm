<?

require_once '../civicrm.settings.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';

$config =& CRM_Core_Config::singleton();


$queue_id = $_GET['q'];
CRM_Mailing_Event_BAO_Opened::open($queue_id);

$filename = "../i/tracker.gif";

header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Content-type: image/gif');
header('Content-Length: ' . filesize($filename));

header('Content-Disposition: inline; filename=tracker.gif');

readfile($filename);

exit();

?>
