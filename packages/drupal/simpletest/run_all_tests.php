<?php

// $Id: run_all_tests.php,v 1.4 2008/02/26 19:13:57 rokZlender Exp $

/**
 * @file
 * This script can be run with browser or from command line.
 * You can provide class names of the tests you wish to run.
 * When this script is run from browser you can select which reporter to use html or xml.
 * For command line: php run_all_tests.php SearchMatchTest,ProfileModuleTestSingle
 * For browser: http://yoursite.com/sites/all/modules/simpletest/run_all_tests.php?include=SearchMatchTest,ProfileModuleTestSingle&reporter=html
 * If none of these two options are provided all tests will be run.
 */
chdir(realpath(dirname(__FILE__) . '/../../'));
if (file_exists('./includes/bootstrap.inc')) {
  include_once './includes/bootstrap.inc';
}
else {
  chdir(getcwd() . '/../../');
  if (file_exists('./includes/bootstrap.inc')) {
    include_once './includes/bootstrap.inc';
  }
  else {
  	exit("bootstrap.inc could not be loaded\n");
  }
}
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

//load simpletest files
simpletest_load();

// If not in 'safe mode', increase the maximum execution time:
if (!ini_get('safe_mode')) {
  set_time_limit(360);
}


$tests = NULL;
$reporter = 'html';
if (SimpleReporter::inCli()) {
  $reporter = 'text';
  if ($argc == 2) {
    $tests = explode(',', $argv[1]);
  }
}
else {
  if ($_GET['include']) {
    $tests = explode(',', $_GET['include']);
  }
  if ($_GET['reporter'] && ($_GET['reporter'] == 'xml' || $_GET['reporter'] == 'html')) {
    $reporter = $_GET['reporter'];
  }
  else {
    $reporter = 'html';
  }
}

simpletest_run_tests($tests, $reporter);
?>