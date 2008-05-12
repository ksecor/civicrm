<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

class CRM_Utils_CAPTCHA {

    protected $_captcha = null;

    protected $_name    = null;

    protected $_url     = null;

    protected $_phrase  = null;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * singleton function used to manage this object
     *
     * @param string the key to permit session scope's
     *
     * @return object
     * @static
     *
     */
    static function &singleton( ) {
        if (self::$_singleton === null ) {
            self::$_singleton =& new CRM_Utils_CAPTCHA( );
        }
        return self::$_singleton;
    }

    function __construct( ) {
    }

    /**
     * class constructor
     *
     */
    function init( $phrase, $size, $path, $file, $width, $height ) {
        $options = array( 'font_size' => $size,
                          'font_path' => $path,
                          'font_file' => $file );
        require_once 'Text/CAPTCHA.php';
        $this->_captcha =& Text_CAPTCHA::factory( 'Image' );
        $retval = $this->_captcha->init( $width, $height, $phrase, $options );
        if ( PEAR::isError( $retval ) ) {
            CRM_Core_Error::statusBounce( ts( 'Error initializing CAPTCHA library' ) );
        }

        $config =& CRM_Core_Config::singleton( );
        $this->_name = $config->imageUploadDir . 'captcha_' . md5( session_id( ) ) . '.png';
        $this->_url  = $config->imageUploadURL . 'captcha_' . md5( session_id( ) ) . '.png';
    }


    /**
     * Add element to form
     *
     */
    function add( &$form ) {
        $config =& CRM_Core_Config::singleton( );

        require_once 'packages/recaptcha/recaptchalib.php';
        $html = recaptcha_get_html($config->recaptchaPublicKey, $error);

        $form->assign( 'recaptchaHTML', $html );
        $form->add( 'text',
                    'recaptcha_challenge_field',
                    null,
                    null,
                    true );
        $form->add( 'hidden',
                    'recaptcha_response_field',
                    'manual_challenge' );

        $form->registerRule( 'recaptcha', 'callback', 'validate', 'CRM_Utils_CAPTCHA' );
        $form->addRule( 'recaptcha_challenge_field',
                        ts( 'Input text must match the phrase in the image. Please review the image and re-enter matching text.' ), 
                        'recaptcha',
                        $form );

    }

    function validate( $value, &$form ) {
        $config =& CRM_Core_Config::singleton( );

        $resp = recaptcha_check_answer( $config->recaptchaPrivateKey,
                                        $_SERVER['REMOTE_ADDR'],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"] );
        return $resp->is_valid;
    }

}


