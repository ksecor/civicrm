<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * Class to check for updated versions of CiviCRM
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Config.php';

class CRM_Utils_VersionCheck
{

    const
        LATEST_VERSION_AT = 'http://downloads.openngo.org/civicrm/latest-version.txt',
        LOCALFILE_NAME    = 'civicrm-version.txt',      // relative to $civicrm_root
        CACHEFILE_NAME    = 'latest-version-cache.txt', // relative to $config->uploadDir
        CACHEFILE_EXPIRE  = 86400;                      // cachefile expiry time (in seconds)

    /**
     * We only need one instance of this object, so we use the
     * singleton pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * The version of the current (local) installation
     *
     * @var string
     */
    var $localVersion = null;

    /**
     * The latest version of CiviCRM
     *
     * @var string
     */
    var $latestVersion = null;

    /**
     * Class constructor
     *
     * @access private
     */
    function __construct()
    {
        global $civicrm_root;
        $config =& CRM_Core_Config::singleton();

        $localfile = $civicrm_root . DIRECTORY_SEPARATOR . CRM_Utils_VersionCheck::LOCALFILE_NAME;
        $cachefile = $config->uploadDir . CRM_Utils_VersionCheck::CACHEFILE_NAME;

        if ($config->versionCheck and file_exists($localfile)) {

            $this->localVersion = file_get_contents($localfile);
            $expiryTime         = time() - CRM_Utils_VersionCheck::CACHEFILE_EXPIRE;

            // if there's a cachefile and it's not stale use it to
            // read the latestVersion, else read it from the Internet
            if (file_exists($cachefile) and (filemtime($cachefile) > $expiryTime)) {
                $this->latestVersion = file_get_contents($cachefile);
            } else {
                $this->latestVersion = file_get_contents(CRM_Utils_VersionCheck::LATEST_VERSION_AT);
                $fp = fopen($cachefile, 'w');
                fwrite($fp, $this->latestVersion);
                fclose($fp);
            }
        }
    }

    /**
     * Static instance provider
     *
     * Method providing static instance of CRM_Utils_VersionCheck,
     * as in Singleton pattern
     *
     * @return CRM_Utils_VersionCheck
     */
    static function &singleton()
    {
        if (!isset(self::$_singleton)) {
            self::$_singleton =& new CRM_Utils_VersionCheck();
        }
        return self::$_singleton;
    }

    /**
     * Get the latest version number if it's newer than the local one
     *
     * @return string
     */
    function newerVersion()
    {
        $local  = explode('.', $this->localVersion);
        $latest = explode('.', $this->latestVersion);
        // compare by version part; this allows us to use trunk.$rev
        // for trunk versions ('trunk' is greater than '1')
        for ($i = 0; $i < max(count($local), count($latest)); $i++) {
            if ($local[$i] > $latest[$i]) {
                return null;
            } elseif ($local[$i] < $latest[$i]) {
                return $this->latestVersion;
            }
        }
        return null;
    }

}

?>
