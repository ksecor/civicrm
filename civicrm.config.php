<?php

/**
 * This function has been copied from DRUPAL_ROOT/includes/bootstrap.inc
 */

/**
 *  If civicrm has been installed using a symlink, you will need 
 * to set this variable to your drupal root directory
 */
// define( 'CIVICRM_DRUPAL_ROOT', '/home/lobo/public_html/drupal' );

/**
 * Locate the appropriate configuration file.
 *
 * Try finding a matching configuration directory by stripping the
 * website's hostname from left to right and pathname from right to
 * left.  The first configuration file found will be used, the
 * remaining will ignored.  If no configuration file is found,
 * return a default value '$confdir/default'.
 *
 * Example for a fictitious site installed at
 * http://www.drupal.org/mysite/test/ the 'settings.php' is
 * searched in the following directories:
 *
 *  1. $confdir/www.drupal.org.mysite.test
 *  2. $confdir/drupal.org.mysite.test
 *  3. $confdir/org.mysite.test
 *
 *  4. $confdir/www.drupal.org.mysite
 *  5. $confdir/drupal.org.mysite
 *  6. $confdir/org.mysite
 *
 *  7. $confdir/www.drupal.org
 *  8. $confdir/drupal.org
 *  9. $confdir/org
 *
 * 10. $confdir/default
 */
function conf_init() {
  static $conf = '';

  if ($conf) {
    return $conf;
  }

  /**
   * We are within the civicrm module, the drupal root is 2 links
   * above us, so use that
   */
  if ( defined( 'CIVICRM_DRUPAL_ROOT' ) ) {
      $drupalRoot = CIVICRM_DRUPAL_ROOT;
  } else {
      $drupalRoot = '../..';
  }

  $confdir = 'sites';
  $uri = explode('/', $_SERVER['PHP_SELF']);
  $server = explode('.', rtrim($_SERVER['HTTP_HOST'], '.'));
  for ($i = count($uri) - 1; $i > 0; $i--) {
      for ($j = count($server); $j > 0; $j--) {
          $dir = implode('.', array_slice($server, -$j)) . implode('.', array_slice($uri, 0, $i));
          if (file_exists("$drupalRoot/$confdir/$dir/civicrm.settings.php")) {
              $conf = "$confdir/$dir";
              return $conf;
          }
      }
  }

  $conf = "$drupalRoot/$confdir/default";
  return $conf;
}

include_once conf_init( ) . '/civicrm.settings.php';

?>