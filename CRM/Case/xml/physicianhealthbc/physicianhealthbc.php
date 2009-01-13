<?php
/*
 * This is a workaround for setting config_backend in the civicrm_domain table. See the note in the .mysql file for details.
 * 
 * You need to run this AFTER visiting the home page for the first time.
 */

if (! (isset($argv[1]) && isset($argv[2]) && isset($argv[3]))) {
	echo "\nUsage: $argv[0] <username> <password> <databasename>\n";
	exit;
}

$u = $argv[1];
$p = $argv[2];
$dbname = $argv[3];

$h = mysql_connect('localhost', $u, $p);
mysql_select_db($dbname);
$r = mysql_query('SELECT config_backend FROM civicrm_domain where id=1');
$row = mysql_fetch_assoc($r);
$cb = $row['config_backend'];
mysql_free_result($r);

$cbarr = unserialize($cb);

$cbarr['includeWildCardInName'] = '1';
$cbarr['includeEmailInName'] = '1';
$cbarr['includeNickNameInName'] = '1';
$cbarr['dateformatDatetime'] = '%Y-%m-%d %H:%M';
$cbarr['dateformatFull'] = '%Y-%m-%d';
$cbarr['dateformatPartial'] = '%B %Y';
$cbarr['dateformatYear'] = '%Y';
$cbarr['dateformatTime'] = '%H:%M';
$cbarr['dateformatQfDate'] = '%b %d %Y';
$cbarr['dateformatQfDatetime'] = '%b %d %Y, %I : %M %P';
$cbarr['countryLimit'] = 
  array (
    0 => '1228',
    1 => '1039',
  );
$cbarr['provinceLimit'] = 
  array (
    0 => '1228',
    1 => '1039',
  );
$cbarr['defaultContactCountry'] = '1039';
$cbarr['enableComponents'] = 
  array (
    0 => 'CiviCase',
  );
$cbarr['enableComponentIDs'] = 
  array (
    0 => '7',
  );
$cbarr['dateformatMonthVar'] = 'M';
$cbarr['datetimeformatMonthVar'] = 'M';
$cbarr['datetimeformatHourVar'] = 'h';

$newcb = mysql_real_escape_string(serialize($cbarr));

mysql_query('UPDATE civicrm_domain SET config_backend = \'' . $newcb . '\' WHERE id=1');
mysql_close($h);
?>
