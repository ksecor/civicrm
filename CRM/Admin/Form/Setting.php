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
        self::setValues(&$defaults);
        
        return $defaults;
    }


    public function setValues(&$defaults) {
        // should actually call CRM_Utils_System::baseURL( );
        global $base_url;

        $config =& CRM_Core_Config::singleton( );
        if ( $config->templateCompileDir ) {
            $path = dirname( $config->templateCompileDir );
            $path = CRM_Core_Config::addTrailingSlash( $path );
        }

        //set defaults if not set in db
        if ( !$defaults['userFrameworkResourceURL'] ) {
            $defaults['userFrameworkResourceURL'] = $base_url. "/modules/civicrm/"; 
        }
        if ( !$defaults['imageUploadDir'] ) {
            $defaults['imageUploadDir'] = $path . "persist/contribute/";
        }
        if ( !$defaults['customFileUploadDir'] ) {
            $defaults['customFileUploadDir'] = $path . "upload/custom/";
        }
        if ( !$defaults['uploadDir'] ) {
            $defaults['uploadDir'] = $path . "upload/";
        }
        if ( !$defaults['smtpPort'] ) {
            $defaults['smtpPort'] = 25;
        }
        if ( !$defaults['smtpAuthsmtp'] ) {
            $defaults['smtpAuth'] = 0;
        }
        if ( !$defaults['countryLimit'][0] ) {
            $defaults['countryLimit'] = 1228;
        }
        if ( !$defaults['provinceLimit'][0] ) {
            $defaults['provinceLimit'] = 1228;
        }
        if ( !$defaults['defaultContactCountry'] ) {
            $defaults['defaultContactCountry'] = 1228;
        }
        if ( !$defaults['defaultCurrency'] ) {
            $defaults['defaultCurrency'] = 'USD';
        }
        if ( !$defaults['lcMonetary'] ) {
            $defaults['lcMonetary'] = 'en_US';
        }
        if ( !$defaults['mapGeoCoding'] ) {
            $defaults['mapGeoCoding'] = 1;
        }
        if ( !$defaults['versionCheck'] ) {
            $defaults['versionCheck'] = 1;
        }
        if ( !$defaults['enableSSL'] ) {
            $defaults['enableSSL'] = 0;
        }
        if ( !$defaults['paymentExpressButton'] ) {
            $defaults['paymentExpressButton'] = 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif';
        }
        if ( !$defaults['paymentPayPalExpressTestUrl'] ) {
            $defaults['paymentPayPalExpressTestUrl'] = 'www.sandbox.paypal.com';
        }
        if ( !$defaults['paymentPayPalExpressUrl'] ) {
            $defaults['paymentPayPalExpressUrl'] = 'www.paypal.com';
        }
        if ( !$defaults['maxLocationBlocks'] ) {
            $defaults['maxLocationBlocks'] = 2;
        }
        if ( !$defaults['captchaFontPath'] ) {
            $defaults['captchaFontPath'] = '/usr/X11R6/lib/X11/fonts/';
        }
        if ( !$defaults['captchaFont'] ) {
            $defaults['captchaFont'] = 'HelveticaBold.ttf';
        }
        if ( !$defaults['debug'] ) {
            $defaults['debug'] = 0;
        }
        if ( !$defaults['backtrace'] ) {
            $defaults['backtrace'] = 0;
        }
        if ( !$defaults['fatalErrorTemplate'] ) {
            $defaults['fatalErrorTemplate'] = 'CRM/error.tpl';
        }
        if ( !$defaults['mailerPeriod'] ) {
            $defaults['mailerPeriod'] = 180;
        }
        if ( !$defaults['mailerBatchLimit'] ) {
            $defaults['mailerBatchLimit'] = 0;
        }
        if ( !$defaults['legacyEncoding'] ) {
            $defaults['legacyEncoding'] = 'Windows-1252';
        }
        if ( !$defaults['enableComponents'] ) {
            $defaults['enableComponents'] = array(
                                                  0 => 'CiviContribute',
                                                  1 => 'CiviMember'
                                                  );
        }
        if ( !$defaults['addressFormat'] ) {
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

        CRM_Core_Session::setStatus( ts('Global settings has been saved.') );
    }
}

?>