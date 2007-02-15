<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components generic to CiviCRM settings
 * 
 */
class CRM_Admin_Form_Setting extends CRM_Core_Form
{

    /**
     * This function sets the default values for the form.
     * default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        //CRM_Core_Error::debug("s", $this->_name);
        $formArray = array('Component', 'Localisation');
        $formMode  = false;
        if ( in_array( $this->_name, $formArray ) ) {
            $formMode = true;
        }

        require_once "CRM/Core/BAO/Setting.php";
        CRM_Core_BAO_Setting::retrieve($defaults);
        self::setValues($defaults, $formMode);
        return $defaults;
    }

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

            $path = CRM_Core_Config::addTrailingSlash( $path );
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
                if ( strpos( $civicrm_root, '/sites/all/modules' ) !== false ) {
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
            $customDir = $path . "upload/custom/";
            
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

        if ( ! isset( $defaults['paymentExpressButton'] ) ) {
            $defaults['paymentExpressButton'] = 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif';
        }

        if ( ! isset( $defaults['paymentPayPalExpressTestUrl'] ) ) {
            $defaults['paymentPayPalExpressTestUrl'] = 'www.sandbox.paypal.com';
        }

        if ( ! isset( $defaults['paymentPayPalExpressUrl'] ) ) {
            $defaults['paymentPayPalExpressUrl'] = 'www.paypal.com';
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

        if ( ! isset( $defaults['mailerPeriod'] ) ) {
            $defaults['mailerPeriod'] = 180;
        }

        if ( ! isset( $defaults['mailerBatchLimit'] ) ) {
            $defaults['mailerBatchLimit'] = 0;
        }

        if ( ! isset( $defaults['legacyEncoding'] ) ) {
            $defaults['legacyEncoding'] = 'Windows-1252';
        }
        
        if ( empty ( $defaults['enableComponents'] ) && !$formMode ) {
            $defaults['enableComponents'] = array('CiviContribute','CiviMember','CiviEvent');
        }

        if ( ! isset( $defaults['addressFormat'] ) ) {
            $defaults['addressFormat']= '{street_address}
                                         {supplemental_address_1}
                                         {supplemental_address_2}
                                         {city}{, }{state_province}{ }{postal_code}
                                         {country}';
        }

        if ( ! isset( $defaults['individualNameFormat'] ) ) {
            $defaults['individualNameFormat']= '{individual_prefix}{ } {first_name}{ }{middle_name}{ }{last_name}{ }{individual_suffix}';
        }

        if ( ! isset( $defaults['mailingLabelFormat'] ) ) {
            $defaults['mailingLabelFormat']= '{contact_name}
{street_address}
{supplemental_address_1}
{supplemental_address_2}
{city}{, }{state_province}{ }{postal_code}
{country}';
        }

    }

  
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        // set breadcrumb to append to 2nd layer pages
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin/setting', 'reset=1' );
        $additionalBreadCrumb = "<a href=\"$breadCrumbPath\">" . ts('Global Settings') . '</a>';
        CRM_Utils_System::appendBreadCrumb( $additionalBreadCrumb );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = array();
        $params = $this->controller->exportValues($this->_name);

        require_once "CRM/Core/BAO/Setting.php";
        CRM_Core_BAO_Setting::add($params);

        // also delete the CRM_Core_Config key from the database
        $cache =& CRM_Utils_Cache::singleton( );
        $cache->delete( 'CRM_Core_Config' );

        CRM_Core_Session::setStatus( ts('Your settings changes have been saved.') );
    }
}

?>