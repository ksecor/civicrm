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
 * Variables class contains definitions of all the core config settings that are allowed on 
 * CRM_Core_Config. If you want a config variable to be present in run time config object,
 * it need to be defined here first.
 * 
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Config_Variables
{


    /** 
     * the debug level for civicrm
     * @var int 
     */ 
    public $debug             = 0; 
    public $backtrace         = 0;

    /**
     * the directory where Smarty and plugins are installed
     * @var string
     */
    public $smartyDir           = null;
    public $pluginsDir          = null;

    /**
     * the root directory of our template tree
     * @var string
     */
    public $templateDir		  = null;

    /**
     * The resourceBase of our application. Used when we want to compose
     * url's for things like js/images/css
     * @var string
     */
    public $resourceBase        = null;

    /**
     * The directory to store uploaded files
     */
    public $uploadDir         = null;
    
    /**
     * The directory to store uploaded image files
     */
    public $imageUploadDir   = null;
    
    /**
     * The directory to store uploaded  files in custom data 
     */
    public $customFileUploadDir   = null;
    
    /**
     * The url that we can use to display the uploaded images
     */
    public $imageUploadURL   = null;

    /**
     * Are we generating clean url's and using mod_rewrite
     * @var string
     */
    public $cleanURL = false;

    /**
     * List of country codes limiting the country list.
     * @var string
     */
    public $countryLimit = array();

    /**
     * List of country codes limiting the province list.
     * @var string
     */
    public $provinceLimit = array( 'US' );

    /**
     * ISO code of default country for contact.
     * @var int
     */
    public $defaultContactCountry = 'US';

    /**
     * ISO code of default currency.
     * @var int
     */
    public $defaultCurrency = 'USD';

    /**
     * Locale for the application to run with.
     * @var string
     */
    public $lcMessages = 'en_US';

    /**
     * String format for date+time
     * @var string
     */
    public $dateformatDatetime = '%B %E%f, %Y %l:%M %P';

    /**
     * String format for a full date (one with day, month and year)
     * @var string
     */
    public $dateformatFull = '%B %E%f, %Y';

    /**
     * String format for a partial date (one with month and year)
     * @var string
     */
    public $dateformatPartial = '%B %Y';

    /**
     * String format for a year-only date
     * @var string
     */
    public $dateformatYear = '%Y';

    /**
     * String format for a time only date
     * @var string
     */
    public $dateformatTime = '%l:%M %P';

    /**
     * String format for date QuickForm drop-downs
     * @var string
     */
    public $dateformatQfDate = '%b %d %Y';

    /**
     * String format for date and time QuickForm drop-downs
     * @var string
     */
    public $dateformatQfDatetime = '%b %d %Y, %I : %M %P';

    public $fiscalYearStart = array(
                                    'M' => 01,
                                    'd' => 01
                                    );

    /**
     * String format for monetary values
     * @var string
     */
    public $moneyformat = '%c %a';

    /**
     * Format for monetary amounts
     * @var string
     */
    public $lcMonetary = 'en_US';

    /**
     * Format for monetary amounts
     * @var string
     */
    public $currencySymbols = '';
    
    /**
        * Format for monetary amounts
     * @var string
     */
    public $defaultCurrencySymbol = null;
    
    /**
     * Default encoding of strings returned by gettext
     * @var string
     */
    public $gettextCodeset = 'utf-8';


    /**
     * Default name for gettext domain.
     * @var string
     */
    public $gettextDomain = 'civicrm';

    /**
     * Default location of gettext resource files.
     */
    public $gettextResourceDir = './l10n/';

    /**
     * Default smtp server and port
     */
    public $smtpServer         = null;
    public $smtpPort           = 25;
    public $smtpAuth           = false;
    public $smtpUsername       = null;
    public $smtpPassword       = null;

    /**
     * Default user framework
     */
    public $userFramework               = 'Drupal';
    public $userFrameworkVersion        = 5.3;
    public $userFrameworkClass          = 'CRM_Utils_System_Drupal';
    public $userHookClass               = 'CRM_Utils_Hook_Drupal';
    public $userPermissionClass         = 'CRM_Core_Permission_Drupal';
    public $userFrameworkURLVar         = 'q';
    public $userFrameworkDSN            = null;
    public $userFrameworkUsersTableName = 'users';
    public $userFrameworkBaseURL        = null;
    public $userFrameworkResourceURL    = null;
    public $userFrameworkFrontend       = false;

    /**
     * Mysql path
     */
    public $mysqlPath = '/usr/bin/';

    /**
     * the handle for import file size 
     * @var int
     */
    public $maxImportFileSize = 1048576;

    /**
     * Map Provider 
     *
     * @var boolean
     */
    public $mapProvider = null;

    /**
     * Map API Key 
     *
     * @var boolean
     */
    public $mapAPIKey = null;
    
    /**
     * How should we get geo code information if google map support needed
     *
     * @var boolean
     */
    public $geocodeMethod    = '';

    /**
     * Whether CiviCRM should check for newer versions
     *
     * @var boolean
     */
    public $versionCheck = true;

    /**
     * Array of enabled add-on components (e.g. CiviContribute, CiviMail...)
     *
     * @var array
     */
    public $enableComponents = array();     

    /**
     * Should payments be accepted only via SSL?
     *
     * @var boolean
     */
    public $enableSSL = false;

    /**
     * error template to use for fatal errors
     *
     * @var string
     */
    public $fatalErrorTemplate = 'CRM/error.tpl';

    /**
     * fatal error handler
     *
     * @var string
     */
    public $fatalErrorHandler = null;

    /**
     * legacy encoding for file encoding conversion
     *
     * @var string
     */
    public $legacyEncoding = 'Windows-1252';

    /**
     * max location blocks in address
     *
     * @var integer
     */
    public $maxLocationBlocks        = 2;

    /**
     * the font path where captcha fonts are stored
     *
     * @var string
     */
    public $captchaFontPath = null;

    /**
     * the font to use for captcha
     *
     * @var string
     */
    public $captchaFont = null;
    
    /**
     * Optimization related variables
     */
    public $includeAlphabeticalPager = 1;
    public $includeOrderByClause     = 1;
    public $includeDomainID          = 1;
    public $oldInputStyle            = 1;

    /**
     * Should we include dojo?
     */
    public $includeDojo              = 1;

    /**
     * should we disbable key generation for forms
     *
     * @var boolean
     */
    public $formKeyDisable = false;

    /**
     * to determine wether the call is from cms or civicrm 
     */
    public $inCiviCRM  = false;

    /**
     * component registry object (of CRM_Core_Component type)
     */
    public $componentRegistry  = null;


    function addCoreVariables( ) {
        global $civicrm_root;

        $this->smartyDir  =
            $civicrm_root . DIRECTORY_SEPARATOR .
            'packages'    . DIRECTORY_SEPARATOR .
            'Smarty'      . DIRECTORY_SEPARATOR ;

        $this->pluginsDir =
            $civicrm_root . DIRECTORY_SEPARATOR .
            'CRM'         . DIRECTORY_SEPARATOR . 
            'Core'        . DIRECTORY_SEPARATOR .
            'Smarty'      . DIRECTORY_SEPARATOR .
            'plugins'     . DIRECTORY_SEPARATOR ;

        $this->templateDir =
            $civicrm_root . DIRECTORY_SEPARATOR .
            'templates'   . DIRECTORY_SEPARATOR ;

        $this->gettextResourceDir =
            $civicrm_root . DIRECTORY_SEPARATOR .
            'l10n'        . DIRECTORY_SEPARATOR ;

        $this->gettextCodeset = 'utf-8';
        $this->gettextDomain  = 'civicrm';

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = CRM_Utils_File::addTrailingSlash(CIVICRM_TEMPLATE_COMPILEDIR);

            if ( ! empty( $this->lcMessages ) ) {
                $this->templateCompileDir .= CRM_Utils_File::addTrailingSlash($this->lcMessages);
            }
                
            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }

        if ( defined( 'CIVICRM_CLEANURL' ) ) {        
            $this->cleanURL = CIVICRM_CLEANURL;
        } else {
            $this->cleanURL = 0;
        }
      
        if ( defined( 'CIVICRM_UF' ) ) {
            $this->userFramework       = CIVICRM_UF;
            $this->userFrameworkClass  = 'CRM_Utils_System_'    . $this->userFramework;
            $this->userHookClass       = 'CRM_Utils_Hook_'      . $this->userFramework;
            $this->userPermissionClass = 'CRM_Core_Permission_' . $this->userFramework;
        }

        if ( defined( 'CIVICRM_UF_VERSION' ) ) {
            $this->userFrameworkVersion = (float ) CIVICRM_UF_VERSION;
        }

        if ( defined( 'CIVICRM_UF_URLVAR' ) ) {
            $this->userFrameworkURLVar = CIVICRM_UF_URLVAR;
        }

        if ( defined( 'CIVICRM_UF_DSN' ) ) { 
            $this->userFrameworkDSN = CIVICRM_UF_DSN;
        }

        if ( defined( 'CIVICRM_UF_USERSTABLENAME' ) ) {
            $this->userFrameworkUsersTableName = CIVICRM_UF_USERSTABLENAME;
        }

        if ( defined( 'CIVICRM_UF_BASEURL' ) ) {
            $this->userFrameworkBaseURL = CRM_Utils_File::addTrailingSlash( CIVICRM_UF_BASEURL, '/' );
            
            if ( isset( $_SERVER['HTTPS'] ) &&
                 strtolower( $_SERVER['HTTPS'] ) != 'off' ) {
                $this->userFrameworkBaseURL     = str_replace( 'http://', 'https://', 
                                                               $this->userFrameworkBaseURL );
            }
        }
        
        if ( defined( 'CIVICRM_UF_FRONTEND' ) ) {
            $this->userFrameworkFrontend = CIVICRM_UF_FRONTEND;
        }

        if ( defined( 'CIVICRM_SUNLIGHT' ) ) {
            $this->sunlight = true;
        } else {
            $this->sunlight = false;
        }

        $size = trim( ini_get( 'upload_max_filesize' ) );
        if ( $size ) {
            $last = strtolower($size{strlen($size)-1});
            switch($last) {
                // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
            }
            $this->maxImportFileSize = $size;
        }
    }


    /**
     * retrieve a mailer to send any mail from the applciation
     *
     * @param
     * @access private
     * @return object
     */
    static function &getMailer() 
    {
        if ( ! isset( self::$_mail ) ) {
            $config =& CRM_Core_Config::singleton();
            if ( defined( 'CIVICRM_MAILER_SPOOL' ) &&
                 CIVICRM_MAILER_SPOOL ) {
                require_once 'CRM/Mailing/BAO/Spool.php';
                self::$_mail = & new CRM_Mailing_BAO_Spool();
            } else {
                if ( self::$_singleton->smtpServer == '' ||
                     ! self::$_singleton->smtpServer ) {
                    CRM_Core_Error::fatal( ts( 'There is no valid smtp server setting. Click <a href="%1">Administer CiviCRM >> Global Settings</a> to set the SMTP Server.', array( 1 => CRM_Utils_System::url('civicrm/admin/setting', 'reset=1')))); 
                }
                
                $params['host'] = self::$_singleton->smtpServer;
                $params['port'] = self::$_singleton->smtpPort ? self::$_singleton->smtpPort : 25;
                
                if (self::$_singleton->smtpAuth) {
                    $params['username'] = self::$_singleton->smtpUsername;
                    $params['password'] = self::$_singleton->smtpPassword;
                    $params['auth']     = true;
                } else {
                    $params['auth']     = false;
                }
                
                self::$_mail =& Mail::factory( 'smtp', $params );
            }
        }
        return self::$_mail;
    }
    
} // end CRM_Core_Config

?>
