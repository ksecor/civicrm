<?php
/**
 * A PHP cron script to run the outstanding and
 * scheduled CiviMail jobs, by Owen Barton.
 *
 * From the cron file, call the script in the following manner:
 * /usr/bin/php -n civimail.cronjob.php /var/www/drupal
 * (adjusting the paths to your PHP CLI binary and the Drupal work dir)
 */

/**
 * Fake function to force access (avoids trying to include Drupal code)
 */
function user_access()
{
    return true;
}

ini_set('memory_limit', '32M');
ini_set('include_path', $_SERVER['argv'][1]);

require_once 'sites/default/civicrm.settings.php';

require_once 'modules/civicrm/CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton();

require_once 'modules/civicrm/CRM/Mailing/BAO/Job.php';
CRM_Mailing_BAO_Job::runJobs();

?>
