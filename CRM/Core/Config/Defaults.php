<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * This class is a temporary place to store default setting values
 * before they will be distributed in proper places (component configurations
 * and core configuration). The name is intentionally stupid so that it will be fixed
 * ASAP.
 * 
 */
class CRM_Core_Config_Defaults
{

    /**
     * Function to set the default values
     *
     * @param array   $defaults  associated array of form elements
     * @param boolena $formMode  this funtion is called to set default
     *                           values in an empty db, also called when setting component using GUI
     *                           this variable is set true for GUI
     *                           mode (eg: Global setting >> Components)    
     *
     * @access public
     */
    public function setValues(&$defaults, $formMode = false) 
    {
        $config =& CRM_Core_Config::singleton( );

        $baseURL = $config->userFrameworkBaseURL;

        if ( $config->templateCompileDir ) {
            $path = dirname( $config->templateCompileDir );
            
            //this fix is to avoid creation of upload dirs inside templates_c directory
            $checkPath = explode( DIRECTORY_SEPARATOR, $path );
            $cnt = count($checkPath) - 1;
            if ( $checkPath[$cnt] == 'templates_c' ) {
                unset( $checkPath[$cnt] );
                $path = implode( DIRECTORY_SEPARATOR, $checkPath );
            }

            $path = CRM_Utils_File::addTrailingSlash( $path );
        }

        //set defaults if not set in db
        if ( ! isset( $defaults['userFrameworkResourceURL'] ) ) {
            $testIMG = "i/tracker.gif";
            if ( $config->userFramework == 'Joomla' ) {
                if ( CRM_Utils_System::checkURL( "{$baseURL}components/com_civicrm/civicrm/{$testIMG}" ) ) {
                    $defaults['userFrameworkResourceURL'] = $baseURL . "components/com_civicrm/civicrm/";
                }
            } else if ( $config->userFramework == 'Drupal' ) {
                // check and see if we are installed in sites/all (for D5 and above)
                // we dont use checkURL since drupal generates an error page and throws
                // the system for a loop on lobo's macosx box
                // or in modules
                global $civicrm_root;
                if ( strpos( $civicrm_root,
                             DIRECTORY_SEPARATOR . 'sites' .
                             DIRECTORY_SEPARATOR . 'all'   .
                             DIRECTORY_SEPARATOR . 'modules' ) !== false ) {
                    $defaults['userFrameworkResourceURL'] = $baseURL . "sites/all/modules/civicrm/"; 
                } else {
                    $defaults['userFrameworkResourceURL'] = $baseURL . "modules/civicrm/"; 
                }
            }
        }

        if ( ! isset( $defaults['imageUploadURL'] ) ) {
            if ( $config->userFramework == 'Joomla' ) {
                // gross hack
                // we need to remove the administrator/ from the end
                $tempURL = str_replace( "/administrator/", "/", $baseURL );
                $defaults['imageUploadURL'] = $tempURL . "media/civicrm/persist/contribute/";
            } else {
                $defaults['imageUploadURL'] = $baseURL . "files/civicrm/persist/contribute/";
            }
        }

        if ( ! isset( $defaults['imageUploadDir'] ) && is_dir($config->templateCompileDir) ) {
            $imgDir = $path . "persist/contribute/";

            CRM_Utils_File::createDir( $imgDir );
            $defaults['imageUploadDir'] = $imgDir;
        }

        if ( ! isset( $defaults['uploadDir'] ) && is_dir($config->templateCompileDir) ) {
            $uploadDir = $path . "upload/";
            
            CRM_Utils_File::createDir( $uploadDir );
            $defaults['uploadDir'] = $uploadDir;
        }

        if ( ! isset( $defaults['customFileUploadDir'] ) && is_dir($config->templateCompileDir) ) {
            $customDir = $path . "custom/";
            
            CRM_Utils_File::createDir( $customDir );
            $defaults['customFileUploadDir'] = $customDir;
        }

        if ( ! isset( $defaults['smtpPort'] ) ) {
            $defaults['smtpPort'] = 25;
        }

        if ( ! isset( $defaults['smtpAuth'] ) ) {
            $defaults['smtpAuth'] = 0;
        }

        if ( ! isset( $defaults['countryLimit'][0] ) && !$formMode ) {
            $defaults['countryLimit'] = 1228;
        }

        if ( ! isset( $defaults['provinceLimit'][0] ) && !$formMode ) {
            $defaults['provinceLimit'] = 1228;
        }

        if ( ! isset( $defaults['defaultContactCountry'] ) ) {
            $defaults['defaultContactCountry'] = 1228;
        }

        if ( ! isset( $defaults['defaultCurrency'] ) ) {
            $defaults['defaultCurrency'] = 'USD';
        }

        if ( ! isset( $defaults['lcMonetary'] ) ) {
            $defaults['lcMonetary'] = 'en_US';
        }

        if ( ! isset( $defaults['mapGeoCoding'] ) ) {
            $defaults['mapGeoCoding'] = 1;
        }

        if ( ! isset( $defaults['versionCheck'] ) ) {
            $defaults['versionCheck'] = 1;
        }

        if ( ! isset( $defaults['enableSSL'] ) ) {
            $defaults['enableSSL'] = 0;
        }
        if ( empty( $defaults['fiscalYearStart']) ) {
            $defaults['fiscalYearStart'] = array(
                                                 'M' => 01,
                                                 'd' => 01
                                                 );
        }

        if ( ! isset( $defaults['maxLocationBlocks'] ) ) {
            $defaults['maxLocationBlocks'] = 2;
        }

        if ( ! isset( $defaults['captchaFontPath'] ) ) {
            $defaults['captchaFontPath'] = '/usr/X11R6/lib/X11/fonts/';
        }

        if ( ! isset( $defaults['captchaFont'] ) ) {
            $defaults['captchaFont'] = 'HelveticaBold.ttf';
        }

        if ( ! isset( $defaults['debug'] ) ) {
            $defaults['debug'] = 0;
        }

        if ( ! isset( $defaults['backtrace'] ) ) {
            $defaults['backtrace'] = 0;
        }

        if ( ! isset( $defaults['fatalErrorTemplate'] ) ) {
            $defaults['fatalErrorTemplate'] = 'CRM/error.tpl';
        }

        if ( ! isset( $defaults['legacyEncoding'] ) ) {
            $defaults['legacyEncoding'] = 'Windows-1252';
        }
        
        if ( empty ( $defaults['enableComponents'] ) && !$formMode ) {
            $defaults['enableComponents'] = array('CiviContribute','CiviMember','CiviEvent', 'CiviMail');
        }

        // populate defaults for components
        foreach( $defaults['enableComponents'] as $key => $name ) {
            $comp = $config->componentRegistry->get( $name );
            $co = $comp->getConfigObject();
            $co->setDefaults( $defaults );
        }

    }
    
}
?>
