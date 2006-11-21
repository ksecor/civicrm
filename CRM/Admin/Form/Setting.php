<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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
    function setDefaultValues( ) {
        $defaults = array( );
        
        require_once "CRM/Core/BAO/Setting.php";
        CRM_Core_BAO_Setting::retrieve($defaults);
        self::setValues($defaults);
        return $defaults;
    }


    public function setValues(&$defaults) {
        $config =& CRM_Core_Config::singleton( );

        $baseURL = $config->userFrameworkBaseURL;

        if ( $config->templateCompileDir ) {
            $path = dirname( $config->templateCompileDir );
            $path = CRM_Core_Config::addTrailingSlash( $path );
        }

        //set defaults if not set in db
        if ( ! isset( $defaults['userFrameworkResourceURL'] ) ) {
            if ( $config->userFramework == 'Joomla' ) {
                $defaults['userFrameworkResourceURL'] = $baseURL . "components/com_civicrm/civicrm/";
            } else {
                $defaults['userFrameworkResourceURL'] = $baseURL . "/modules/civicrm/"; 
            }
        }

        if ( ! isset( $defaults['imageUploadDir'] ) ) {
            $defaults['imageUploadDir'] = $path . "persist/contribute/";
        }

        if ( ! isset( $defaults['imageUploadURL'] ) ) {
            $defaults['imageUploadURL'] = $baseURL . "files/civicrm/persist/contribute/";
        }

        if ( ! isset( $defaults['customFileUploadDir'] ) ) {
            $defaults['customFileUploadDir'] = $path . "upload/custom/";
        }

        if ( ! isset( $defaults['uploadDir'] ) ) {
            $defaults['uploadDir'] = $path . "upload/";
        }

        if ( ! isset( $defaults['smtpPort'] ) ) {
            $defaults['smtpPort'] = 25;
        }

        if ( ! isset( $defaults['smtpAuth'] ) ) {
            $defaults['smtpAuth'] = 0;
        }

        if ( ! isset( $defaults['countryLimit'][0] ) ) {
            $defaults['countryLimit'] = 1228;
        }

        if ( ! isset( $defaults['provinceLimit'][0] ) ) {
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

        if ( empty ( $defaults['enableComponents'] ) ) {
            $defaults['enableComponents'] = array('CiviContribute','CiviMember');
        }

        if ( ! isset( $defaults['addressFormat'] ) ) {
            $defaults['addressFormat']= '{street_address}
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
    public function buildQuickForm( ) {
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

        CRM_Core_Session::setStatus( ts('Your settings changes have been saved.') );
    }
}

?>