<?php

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'extern/stubs.php';

$config =& CRM_Core_Config::singleton();

// to keep backward compatibility for URLs generated
// by CiviCRM < 1.7, we check for the q variable as well
if (isset($_GET['qid'])) {
    $queue_id = $_GET['qid'];
} else {
    $queue_id = $_GET['q'];
}
$url_id = $_GET['u'];

require_once 'CRM/Mailing/Event/BAO/TrackableURLOpen.php';
$url = CRM_Mailing_Event_BAO_TrackableURLOpen::track($queue_id, $url_id);

CRM_Utils_System::redirect($url);

?>
