<?php
/**
 * CiviCRM Configuration File - v2.1
 */

define( 'CIVICRM_UF'               , 'Joomla'        );

$joomlaConfig =& JFactory::getConfig( );

/**
 * Joomla Database Settings
 *
 * Database URL (CIVICRM_UF_DSN) for Joomla Data:
 */
define( 'CIVICRM_UF_DSN',
        'mysql://'                                 .
        $joomlaConfig->getValue('config.user')     . ':' .
        $joomlaConfig->getValue('config.password') . '@' .
        $joomlaConfig->getValue('config.host')     . '/' .
        $joomlaConfig->getValue('config.db')       .
        '?new_link=true' );

/**
 * CiviCRM Database Settings
 *
 * Database URL (CIVICRM_DSN) for CiviCRM Data:
 */
define( 'CIVICRM_DSN',
        'mysql://'                                 .
        $joomlaConfig->getValue('config.user')     . ':' .
        $joomlaConfig->getValue('config.password') . '@' .
        $joomlaConfig->getValue('config.host')     . '/' .
        $joomlaConfig->getValue('config.db')       .
        '?new_link=true' );

/**
 * File System Paths:
 *
 * $civicrm_root is the file system path on your server where the civicrm
 * code is installed. Use an ABSOLUTE path (not a RELATIVE path) for this setting.
 *
 * CIVICRM_TEMPLATE_COMPILEDIR is the file system path where compiled templates are stored.
 * These sub-directories and files are temporary caches and will be recreated automatically
 * if deleted.
 *
 * IMPORTANT: The COMPILEDIR directory must exist,
 * and your web server must have read/write access to these directories.
 *
 */

global $civicrm_root;

$civicrm_root =
    JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
    'components'        . DIRECTORY_SEPARATOR .
    'com_civicrm'       . DIRECTORY_SEPARATOR .
    'civicrm';

define( 'CIVICRM_TEMPLATE_COMPILEDIR',
        JPATH_ROOT    . DIRECTORY_SEPARATOR .
        'media'       . DIRECTORY_SEPARATOR .
        'templates_c' . DIRECTORY_SEPARATOR );


/**
 * Site URLs:
 *
 * This section defines absolute and relative URLs to access the host CMS (Drupal or Joomla) resources.
 *
 * CIVICRM_UF_BASEURL - home URL for your site:
 * Administration site:
 *      define( 'CIVICRM_UF_BASEURL' , 'http://www.example.com/joomla/administrator/' );
 * Front-end site:
 *      define( 'CIVICRM_UF_BASEURL' , 'http://www.example.com/joomla/' );
 *
 */
 
define( 'CIVICRM_UF_BASEURL'      , JURI::base( ) );

/*
 * If you are using any CiviCRM script in the bin directory that
 * requires authentication, then you also need to set this key.
 * We recommend using a 16-32 bit alphanumeric/punctuation key. 
 * More info at http://wiki.civicrm.org/confluence/display/CRMDOC/Command-line+Script+Configuration
 */
define( 'CIVICRM_SITE_KEY', null );

/**
 * 
 * Do not change anything below this line. Keep as is
 *
 */

$include_path = '.'           . PATH_SEPARATOR .
                $civicrm_root . PATH_SEPARATOR . 
                $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                get_include_path( );
set_include_path( $include_path );

define( 'CIVICRM_CLEANURL', 0 );

// force PHP to auto-detect Mac line endings
ini_set('auto_detect_line_endings', '1');

// make sure the memory_limit is at least 48 MB
$memLimitString = trim(ini_get('memory_limit'));
$memLimitUnit   = strtolower(substr($memLimitString, -1));
$memLimit       = (int) $memLimitString;
switch ($memLimitUnit) {
    case 'g': $memLimit *= 1024;
    case 'm': $memLimit *= 1024;
    case 'k': $memLimit *= 1024;
}
if ( $memLimit >= 0 and $memLimit < 50331648 ) {
    ini_set('memory_limit', '48M');
}


