<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Payment_Config {

    /**
     * Name of the payment processor
     *
     * @var string
     */
    public $paymentProcessor = null;

    /** 
     * Name of the payment class that implement
     * the payment processor directive
     * 
     * @var string 
     */ 
    public $paymentFile = null;

    /**
     * Type of billing mode
     *
     * 1 - billing information collected
     * 2 - button displayed, billing information on processor side
     * 3 - both
     * @var int
     */
    public $paymentBillingMode = null;

    /**
     * Where are the payment processor secret files stored
     *
     * @var string
     */
    public $paymentCertPath = array( );

    /** 
     * What is the payment User name
     * 
     * @var string                
     */ 
    public $paymentUsername = array( );

    /** 
     * What is the payment file key or api signature
     * 
     * @var string                
     */ 
    public $paymentKey = array( );

    /** 
     * What is the payment password
     * 
     * @var string                
     */ 
    public $paymentPassword = array( );

    /** 
     * What is the payment subject
     * 
     * @var string                
     */ 
    public $paymentSubject = null;

    /** 
     * What is the payment response email address
     * 
     * @var string                
     */ 
    public $paymentResponseEmail = null;

    /** 
     * URL to payment processor submit button for "express" processors / button mode
     * 
     * @var string                
     */ 
    public $paymentProcessorButton = null;

    /**
     * Function to add additional config paramters to the core Config class
     * if CiviContribute is enabled
     *
     * Note that this config class prevent code bloat in the Core Config class,
     * however we declare all the variables assigned here, more for documentation
     * than anything else, at some point, we'll figure out how to extend a class
     * and properties dynamically in PHP (like Ruby)
     *
     * @param CRM_Core_Config (reference ) the system config object
     *
     * @return void
     * @static
     * @access public
     */
    static function add( &$config, $oldMode = false ) {
        $config->paymentFile             = null;
        $config->paymentBillingMode      = null;
        $config->paymentPassword         = null;
        $config->paymentSubject          = null;
        $config->paymentKey              = null;
        $config->enableRecurContribution = false;

        if ( $oldMode ) {
            if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR' ) ) {
                $config->paymentProcessor       = CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR;
            } else {
                $config->paymentProcessor       = null;
            }
            $config->paymentCertPath        = array( 'test' => null, 'live' => null );
            $config->paymentUsername        = array( 'test' => null, 'live' => null );
            $config->paymentProcessorButton = "https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif";
            $config->paymentPayPalExpressUrl = "www.paypal.com";
            $config->paymentPayPalExpressTestUrl = "www.sandbox.paypal.com";
        }

        if ( isset( $config->paymentProcessor ) ) {
            require_once 'CRM/Core/Payment.php';
            switch ( $config->paymentProcessor ) {
            case 'PayPal':
                $config->paymentFile = 'Payment_PayPalImpl';
                $config->paymentBillingMode =
                    CRM_Core_Payment::BILLING_MODE_FORM |
                    CRM_Core_Payment::BILLING_MODE_BUTTON;
                if ( $oldMode ) {
                    $config->paymentExpressButton = CIVICRM_CONTRIBUTE_PAYMENT_EXPRESS_BUTTON;
                    $config->paymentPayPalExpressUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_URL;
                    $config->paymentPayPalExpressTestUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_TEST_URL;
                }
                break;

            case 'PayPal_Express':
                $config->paymentFile = 'Payment_PayPalImpl';
                $config->paymentBillingMode = CRM_Core_Payment::BILLING_MODE_BUTTON;
                if ( $oldMode ) {
                    $config->paymentExpressButton = CIVICRM_CONTRIBUTE_PAYMENT_EXPRESS_BUTTON;
                    $config->paymentPayPalExpressUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_URL;
                    $config->paymentPayPalExpressTestUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_TEST_URL;
                }
                break;

            case 'PayPal_Standard':
                $config->enableRecurContribution = true;
                $config->paymentFile = 'Payment_PayPalImpl';
                $config->paymentBillingMode = CRM_Core_Payment::BILLING_MODE_NOTIFY;
                if ( $oldMode ) {
                    $config->paymentPayPalExpressUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_URL;
                    $config->paymentPayPalExpressTestUrl = CIVICRM_CONTRIBUTE_PAYMENT_PAYPAL_EXPRESS_TEST_URL;
                }
                break;

            case 'Moneris':
                $config->enableRecurContribution = defined('CIVICRM_CONTRIBUTE_PAYMENT_ENABLE_RECUR') ? CIVICRM_CONTRIBUTE_PAYMENT_ENABLE_RECUR : 0;
                $config->paymentFile = 'Payment_Moneris';
                $config->paymentBillingMode = CRM_Core_Payment::BILLING_MODE_FORM;
                break;

            case 'Google_Checkout':
                $config->paymentFile = 'Payment_Google';
                $config->paymentBillingMode = CRM_Core_Payment::BILLING_MODE_NOTIFY;
                break;
 
            case 'AuthNet_AIM':
                $config->paymentFile = 'Payment_AuthorizeNet';
                $config->paymentType = 'AIM';
                $config->paymentBillingMode = CRM_Core_Payment::BILLING_MODE_FORM;
                break;
           }

        }

        if ( $oldMode ) {
            if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH' ) ) {
                $config->paymentCertPath['live'] = CRM_Core_Config::addTrailingSlash( CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH );
            }

            if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH' ) ) {
                $config->paymentCertPath['test'] = CRM_Core_Config::addTrailingSlash( CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH );
            }

            if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_USERNAME' ) ) {
                $config->paymentUsername['live'] = CIVICRM_CONTRIBUTE_PAYMENT_USERNAME;
            }

            if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_USERNAME' ) ) {
                $config->paymentUsername['test'] = CIVICRM_CONTRIBUTE_PAYMENT_TEST_USERNAME;
            }
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_KEY' ) ) {
            $config->paymentKey['live'] = CIVICRM_CONTRIBUTE_PAYMENT_KEY;
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY' ) ) {
            $config->paymentKey['test'] = CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY;
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD' ) ) {
            $config->paymentPassword['live'] = CIVICRM_CONTRIBUTE_PAYMENT_PASSWORD;
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_PASSWORD' ) ) {
            $config->paymentPassword['test'] = CIVICRM_CONTRIBUTE_PAYMENT_TEST_PASSWORD;
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_SUBJECT' ) ) {
            $config->paymentSubject['live'] = CIVICRM_CONTRIBUTE_PAYMENT_SUBJECT;
        }

        if ( defined( 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_SUBJECT' ) ) {
            $config->paymentSubject['test'] = CIVICRM_CONTRIBUTE_PAYMENT_TEST_SUBJECT;
        }
    }

}


