<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
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
        LOCAL_VERSION_AT  = 'civicrm-version.txt',
        CACHEFILE_NAME    = 'civicrm-version-cache.txt',
        STALE_TIME        = 30;

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
        $config =& CRM_Core_Config::singleton();

        if ($config->versionCheck) {

            $this->localVersion = file_get_contents(CRM_Utils_VersionCheck::LOCAL_VERSION_AT, true);

            $cachefile   = $config->uploadDir . CRM_Utils_VersionCheck::CACHEFILE_NAME;
            $staleBefore = time() - CRM_Utils_VersionCheck::STALE_TIME;

            // if there's no cachefile or it's stale - fetch the latestVersion from the Internet
            // else read the one in cachefile
            if (!file_exists($cachfile) or filemtime($cachefile) < $staleBefore) {
                $this->latestVersion = file_get_contents(CRM_Utils_VersionCheck::LATEST_VERSION_AT);
                $fp = fopen($cachefile, 'w');
                fwrite($fp, $this->latestVersion);
                fclose($fp);
            } else {
                $this->latestVersion = file_get_contents($cachefile);
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
        if ($this->localVersion != $this->latestVersion) {
            return $this->latestVersion;
        } else {
            return null;
        }
    }

}

?>
