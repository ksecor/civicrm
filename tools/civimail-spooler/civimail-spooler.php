<?
// $config_file = "/home/bmcfee/svn/civicrm/modules/config.inc.php";

$options = getopt('bcht:');

if (isset($options['h'])) {
print("\nUsage: php civimail-spooler.php [-bh] [-c <config>] [-t <period>]\n");
print(" -b      Run this process continuously\n");
print(" -c      Path to CiviCRM config.inc.php\n");
print(" -h      Print this help message\n");
print(" -t      In continuous mode, the period to wait between queue events\n\n");
exit();
}

if (isset($options['c'])) {
    $config_file = $options['c'];
}

eval('
require_once $config_file;
require_once \'CRM/Core/Config.php\';
');

$config =& CRM_Core_Config::singleton();

if (is_int($options['t'])) {
    $config->mailerPeriod = $options['t'];
}

if (isset($options['b'])) {
    while (true) {
        CRM_Mailing_BAO_Job::runJobs();
        sleep($config->mailerPeriod);
    }
} else {
    CRM_Mailing_BAO_Job::runJobs();
}

?>
