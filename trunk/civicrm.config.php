<?php

/**
 * This function has been copied from DRUPAL_ROOT/includes/bootstrap.inc
 */

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
  $currentDir = dirname( __FILE__ ) . '/';
  if ( file_exists( $currentDir . 'settings_location.php' ) ) {
    include $currentDir . 'settings_location.php';
  }
  
  if ( defined( 'CIVICRM_CONFDIR' ) ) {
    $confdir = CIVICRM_CONFDIR;
  } else {
    // make it relative to civicrm.config.php, else php makes it relative
    // to the script that invokes it
    $confdir = $currentDir . '../../sites';
  }
  
  $phpSelf  = array_key_exists( 'PHP_SELF' , $_SERVER ) ? $_SERVER['PHP_SELF' ] : '';
  $httpHost = array_key_exists( 'HTTP_HOST', $_SERVER ) ? $_SERVER['HTTP_HOST'] : '';

  $uri    = explode('/', $phpSelf );
  $server = explode('.', implode('.', array_reverse(explode(':', rtrim($httpHost, '.')))));
  for ($i = count($uri) - 1; $i > 0; $i--) {
      for ($j = count($server); $j > 0; $j--) {
          $dir = implode('.', array_slice($server, -$j)) . implode('.', array_slice($uri, 0, $i));
          if (file_exists("$confdir/$dir/civicrm.settings.php")) {
              $conf = "$confdir/$dir";
              return $conf;
          }
      }
  }

  $conf = "$confdir/default";
  return $conf;
}

include_once conf_init( ) . '/civicrm.settings.php';

?>
