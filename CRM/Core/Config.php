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
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'Log.php';
require_once 'Mail.php';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Recent.php';
require_once 'CRM/Utils/Rule.php';
require_once 'CRM/Utils/File.php';
require_once 'CRM/Contact/DAO/Factory.php';
require_once 'CRM/Core/Session.php';

class CRM_Core_Config 
{

    /**
     * the dsn of the database connection
     * @var string
     */
    public $dsn;

    /** 
     * the debug level for civicrm
     * @var int 
     */ 
    public $debug             = 0; 
    public $backtrace         = 0;

    /**
     * the debug level for DB_DataObject
     * @var int
     */
    public $daoDebug		  = 0;

    /**
     * the directory where Smarty and plugins are installed
     * @var string
     */
    public $smartyDir           = '/opt/local/lib/php/Smarty/';
    public $pluginsDir          = '/opt/local/lib/php/Smarty/plugins/';

    /**
     * the root directory of our template tree
     * @var string
     */
    public $templateDir		  = './templates/';

    /**
     * The root directory where Smarty should store
     * compiled files
     * @var string
     */
    public $templateCompileDir  = './templates_c/en_US/';

    /**
     * The root url of our application. Used when we don't
     * know where to redirect the application flow
     * @var string
     */
    public $mainMenu            = null;

    /**
     * The resourceBase of our application. Used when we want to compose
     * url's for things like js/images/css
     * @var string
     */
    public $resourceBase        = null;

    /**
     * the factory class used to instantiate our DB objects
     * @var string
     */
    public $DAOFactoryClass	  = 'CRM_Contact_DAO_Factory';

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
     * The format of the address fields.
     * @var string
     */
    public $addressFormat = "{street_address}\n{supplemental_address_1}\n{supplemental_address_2}\n{city}{, }{county}{ }{state_province}{ }{postal_code}\n{country}";
        
    /**
     * Address Standardization Provider.
     * @var string
     */
    public $AddressStdProvider = null;
        
    /**
     * Address Standardization User ID.
     * @var string
     */
    public $AddressStdUserID = null;
    
    /**
     * Address Standardization URL.
     * @var string
     */
    public $AddressStdURL = null;
    
    /**
     * The sequence of the address fields.
     * @var string
     */
    public $addressSequence = array('street_address', 'supplemental_address_1', 'supplemental_address_2', 'city', 'county', 'state_province', 'postal_code', 'country');

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
     * String format for date QuickForm drop-downs
     * @var string
     */
    public $dateformatQfDate = '%b %d %Y';

    /**
     * String format for date and time QuickForm drop-downs
     * @var string
     */
    public $dateformatQfDatetime = '%b %d %Y, %I : %M %P';

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
    public $defaultCurrencySymbol = '$';
    
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
    public $userFrameworkVersion        = 4.6;
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
     * The default mysql version that we are using
     */
    public $mysqlVersion = 4.1;

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
    public $versionCheck = false;

    /**
     * How long should we wait before checking for new outgoing mailings?
     *
     * @var int
     */
    public $mailerPeriod    = 180;

    /**
     * What should be the verp separator we use
     *
     * @var char
     */
    public $verpSeparator = '.';

    /**
     * How many emails should CiviMail deliver on a given run
     *
     * @var int
     */
    public $mailerBatchLimit = 0;

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
     * the domainID for this instance. 
     *
     * @var int
     */
    private static $_domainID = 1;

    /**
     * The handle to the log that we are using
     * @var object
     */
    private static $_log = null;

    /**
     * the handle on the mail handler that we are using
     * @var object
     */
    private static $_mail = null;
    
    /**
     * include county in address block
     * @var object
     */
    private static $_includeCounty = null;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     * @var object
     * @static
     */
    private static $_singleton = null;

    /**
     * Optimization related variables
     */
    public $includeEmailInSearch     = 1;
    public $includeAlphabeticalPager = 1;
    public $includeOrderByClause     = 1;
    public $includeDomainID          = 1;
    public $oldInputStyle            = 1;

    /**
     * singleton function used to manage this object
     *
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
    static function &singleton($key = 'crm', $loadFromDB = true ) 
    {
        if (self::$_singleton === null ) {
            self::$_singleton =& new CRM_Core_Config($key);

            self::$_singleton->initialize( );

            //initialize variable. for gencode we cannot load from the
            //db since the db might not be initialized
            if ( $loadFromDB ) {
                self::$_singleton->initVariables();
                
                // retrieve and overwrite stuff from the settings file
                self::$_singleton->addCoreVariables( );
            }
        }

        return self::$_singleton;
    }

    /**
     * The constructor. Basically redefines the class variables if
     * it finds a constant definition for that class variable
     *
     * @return object
     * @access private
     */
    function __construct() 
    {
        require_once 'CRM/Core/Session.php';
        $session =& CRM_Core_Session::singleton( );
        if ( defined( 'CIVICRM_DOMAIN_ID' ) ) {
            self::$_domainID = CIVICRM_DOMAIN_ID;
        } else {
            self::$_domainID = 1;
        }
        $session->set( 'domainID', self::$_domainID );
    }

    function addCoreVariables( ) {
        if (defined('CIVICRM_DSN')) {
            $this->dsn = CIVICRM_DSN;
        }

        if (defined('CIVICRM_DAO_DEBUG') ) {
            $this->daoDebug = CIVICRM_DAO_DEBUG;
        }

        if (defined('CIVICRM_DAO_FACTORY_CLASS') ) {
            $this->DAOFactoryClass = CIVICRM_DAO_FACTORY_CLASS;
        }

        if (defined('CIVICRM_SMARTYDIR')) {
            $this->smartyDir = self::addTrailingSlash(CIVICRM_SMARTYDIR);
        }

        if (defined('CIVICRM_PLUGINSDIR')) {
            $this->pluginsDir = self::addTrailingSlash(CIVICRM_PLUGINSDIR);
        }

        if (defined('CIVICRM_TEMPLATEDIR')) {
            $this->templateDir = self::addTrailingSlash(CIVICRM_TEMPLATEDIR);
        }

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = self::addTrailingSlash(CIVICRM_TEMPLATE_COMPILEDIR);

            if ( ! empty( $this->lcMessages ) ) {
                $this->templateCompileDir .= self::addTrailingSlash($this->lcMessages);
            }
                
            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }

        if ( defined( 'CIVICRM_UPLOADDIR' ) ) {
            $this->uploadDir = self::addTrailingSlash( CIVICRM_UPLOADDIR );

            CRM_Utils_File::createDir( $this->uploadDir );
        }

        if ( defined( 'CIVICRM_CLEANURL' ) ) {
            $this->cleanURL = CIVICRM_CLEANURL;
        }       
      
        if ( defined( 'CIVICRM_LC_MONETARY' ) ) {
            $this->lcMonetary = CIVICRM_LC_MONETARY;
            setlocale(LC_MONETARY, $this->lcMonetary . '.UTF-8', $this->lcMonetary, 'C');
        }
        
        $this->currencySymbols = array('EUR' => '€', 'GBP' => '£', 'ILS' => '₪', 'JPY' => '¥', 'KRW' => '₩', 'LAK' => '₭',
                                       'MNT' => '₮', 'NGN' => '₦', 'PLN' => 'zł', 'THB' => '฿', 'USD' => '$', 'VND' => '₫');
        
        if ( defined( 'CIVICONTRIBUTE_DEFAULT_CURRENCY' ) and CRM_Utils_Rule::currencyCode( CIVICONTRIBUTE_DEFAULT_CURRENCY ) ) {
            $this->defaultCurrency       = CIVICONTRIBUTE_DEFAULT_CURRENCY;
            $this->defaultCurrencySymbol = CRM_Utils_Array::value($this->defaultCurrency, $this->currencySymbols, '');
        }        
        
        if ( defined( 'CIVICRM_GETTEXT_CODESET' ) ) {
            $this->gettextCodeset = CIVICRM_GETTEXT_CODESET;
        }
        
        if ( defined( 'CIVICRM_GETTEXT_DOMAIN' ) ) {
            $this->gettextDomain = CIVICRM_GETTEXT_DOMAIN;
        }
        
        if ( defined( 'CIVICRM_GETTEXT_RESOURCEDIR' ) ) {
            $this->gettextResourceDir = self::addTrailingSlash( CIVICRM_GETTEXT_RESOURCEDIR );
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
            $this->userFrameworkBaseURL = self::addTrailingSlash( CIVICRM_UF_BASEURL, '/' );
        }
        
        if ( defined( 'CIVICRM_IMAGE_UPLOADURL' ) ) {
            $this->imageUploadURL = self::addTrailingSlash( CIVICRM_IMAGE_UPLOADURL, '/' );
        }

        if ( defined( 'CIVICRM_UF_FRONTEND' ) ) {
            $this->userFrameworkFrontend = CIVICRM_UF_FRONTEND;
        }

        if ( defined( 'CIVICRM_MYSQL_VERSION' ) ) {
            $this->mysqlVersion = CIVICRM_MYSQL_VERSION;
        }

        if ( defined( 'CIVICRM_MYSQL_PATH' ) ) {
            $this->mysqlPath = self::addTrailingSlash( CIVICRM_MYSQL_PATH );
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

        //$this->retrieveFromSettings( );
    }

    function retrieveFromSettings( ) {
        // we figure this out early, since some config parameters are loaded
        // based on what components are enabled
         if ( defined( 'ENABLE_COMPONENTS' ) ) {
             $this->enableComponents = explode(',', ENABLE_COMPONENTS);
             for ( $i=0; $i < count($this->enableComponents); $i++) {
                 $this->enableComponents[$i] = trim($this->enableComponents[$i]);
             }
        }

         if (defined('CIVICRM_DEBUG') ) {
             $this->debug = CIVICRM_DEBUG;
            
             // check for backtrace only if debug is enabled
             if ( defined( 'CIVICRM_BACKTRACE' ) ) {
                 $this->backtrace = CIVICRM_BACKTRACE;
             }
         }

         if ( defined( 'CIVICRM_COUNTRY_LIMIT' ) ) {
             $isoCodes = preg_split('/[^a-zA-Z]/', CIVICRM_COUNTRY_LIMIT);
             $this->countryLimit = array_filter($isoCodes);
         }
        
         if ( defined( 'CIVICRM_PROVINCE_LIMIT' ) ) {
             $isoCodes = preg_split('/[^a-zA-Z]/', CIVICRM_PROVINCE_LIMIT);
             $provinceLimitList = array_filter($isoCodes);
             if ( !empty($provinceLimitList)) {
                 $this->provinceLimit = array_filter($isoCodes);
             }
         } 

         // Note: we can't change the ISO code to country_id
         // here, as we can't access the database yet...
         if ( defined( 'CIVICRM_DEFAULT_CONTACT_COUNTRY' ) ) {
             $this->defaultContactCountry = CIVICRM_DEFAULT_CONTACT_COUNTRY;
         }
        
         if ( defined( 'CIVICRM_LC_MESSAGES' ) ) {
             $this->lcMessages = CIVICRM_LC_MESSAGES;

             // reset the templateCompileDir to locale-specific and make sure it exists
             $this->templateCompileDir .= self::addTrailingSlash($this->lcMessages);
             CRM_Utils_File::createDir( $this->templateCompileDir );
         }
        
         if ( $this->addressFormat ) {
            
             // trim the format and unify line endings to LF
             $format = trim($this->addressFormat);
             $format = str_replace(array("\r\n", "\r"), "\n", $format);

             // get the field sequence from the format
             $newSequence = array();
             foreach($this->addressSequence as $field) {
                 if (substr_count($format, $field)) {
                     $newSequence[strpos($format, $field)] = $field;
                 }
             }
             ksort($newSequence);

             // add the addressSequence fields that are missing in the addressFormat
             // to the end of the list, so that (for example) if state_province is not
             // specified in the addressFormat it's still in the address-editing form
             $newSequence = array_merge($newSequence, $this->addressSequence);
             $newSequence = array_unique($newSequence);

             $this->addressSequence = $newSequence;
             $this->addressFormat   = $format;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_DATETIME' ) ) {
             $this->dateformatDatetime = CIVICRM_DATEFORMAT_DATETIME;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_FULL' ) ) {
             $this->dateformatFull = CIVICRM_DATEFORMAT_FULL;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_PARTIAL' ) ) {
             $this->dateformatPartial = CIVICRM_DATEFORMAT_PARTIAL;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_YEAR' ) ) {
             $this->dateformatYear = CIVICRM_DATEFORMAT_YEAR;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_QF_DATE' ) ) {
             $this->dateformatQfDate = CIVICRM_DATEFORMAT_QF_DATE;
         }
        
         if ( defined( 'CIVICRM_DATEFORMAT_QF_DATETIME' ) ) {
             $this->dateformatQfDatetime = CIVICRM_DATEFORMAT_QF_DATETIME;
         }

         if ( defined( 'CIVICRM_MONEYFORMAT' ) ) {
             $this->moneyformat = CIVICRM_MONEYFORMAT;
         }

         if ( defined( 'CIVICRM_SMTP_SERVER' ) ) {
             $this->smtpServer = CIVICRM_SMTP_SERVER;
         }

         if ( defined( 'CIVICRM_SMTP_PORT' ) ) {
             $this->smtpPort = CIVICRM_SMTP_PORT;
         }

         if ( defined( 'CIVICRM_SMTP_AUTH' )) {
             if (CIVICRM_SMTP_AUTH === true) {
                 $this->smtpAuth = true;
             } // else it stays false
         }

         if ( defined( 'CIVICRM_SMTP_USERNAME' ) ) {
             $this->smtpUsername = CIVICRM_SMTP_USERNAME;
         }

         if ( defined( 'CIVICRM_SMTP_PASSWORD' ) ) {
             $this->smtpPassword = CIVICRM_SMTP_PASSWORD;
         }

         if ( defined( 'CIVICRM_UF_RESOURCEURL' ) ) {
             $this->userFrameworkResourceURL = self::addTrailingSlash( CIVICRM_UF_RESOURCEURL, '/' );
             $this->resourceBase             = $this->userFrameworkResourceURL;
         }

         if ( defined( 'CIVICRM_MAP_PROVIDER' ) ) {
             $this->mapProvider = CIVICRM_MAP_PROVIDER;
         }

         if ( defined( 'CIVICRM_MAP_API_KEY' ) ) {
             $this->mapAPIKey = CIVICRM_MAP_API_KEY;
         }

         if ( defined( 'CIVICRM_GEOCODE_METHOD' ) ) {
             if ( CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_ZipTable' ||
                  CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_RPC'      ||
                  CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_Yahoo'    ||
                  CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_Google') {
                 $this->geocodeMethod = CIVICRM_GEOCODE_METHOD;
             }
         }

         if (defined('CIVICRM_VERSION_CHECK') and CIVICRM_VERSION_CHECK) {
             $this->versionCheck = true;
         }
         if ( defined( 'CIVICRM_ENABLE_SSL' ) ) {
             $this->enableSSL = CIVICRM_ENABLE_SSL;
         }

         if ( defined( 'CIVICRM_FATAL_ERROR_TEMPLATE' ) ) {
             $this->fatalErrorTemplate = CIVICRM_FATAL_ERROR_TEMPLATE;
         }

         if ( defined( 'CIVICRM_FATAL_ERROR_HANDLER' ) ) {
             $this->fatalErrorHandler = CIVICRM_FATAL_ERROR_HANDLER;
         }

         if ( defined( 'CIVICRM_LEGACY_ENCODING' ) ) {
             $this->legacyEncoding = CIVICRM_LEGACY_ENCODING;
         }

         if ( defined( 'CIVICRM_MAX_LOCATION_BLOCKS' ) ) {
             $this->maxLocationBlocks = CIVICRM_MAX_LOCATION_BLOCKS;
         }

         if ( defined( 'CIVICRM_CAPTCHA_FONT_PATH' ) ) {
             $this->captchaFontPath = self::addTrailingSlash( CIVICRM_CAPTCHA_FONT_PATH );
         }

         if ( defined( 'CIVICRM_CAPTCHA_FONT' ) ) {
             $this->captchaFont = CIVICRM_CAPTCHA_FONT;
         }

        if ( defined( 'CIVICRM_INCLUDE_COUNTY' ) ) {
            $this->includeCounty = CIVICRM_INCLUDE_COUNTY;
        }
        
        if ( defined( 'CIVICRM_ADDRESS_STANDARDIZATION_PROVIDER' ) ) {
            $this->AddressStdProvider = CIVICRM_ADDRESS_STANDARDIZATION_PROVIDER;
        }
         
        if ( defined( 'CIVICRM_ADDRESS_STANDARDIZATION_USERID' ) ) {
            $this->AddressStdUserID = CIVICRM_ADDRESS_STANDARDIZATION_USERID;
        }
        
        if ( defined( 'CIVICRM_ADDRESS_STANDARDIZATION_URL' ) ) {
            $this->AddressStdURL = CIVICRM_ADDRESS_STANDARDIZATION_URL;
        }

        if ( defined( 'CIVICRM_MAILER_SPOOL_PERIOD' ) ) {
            $this->mailerPeriod = CIVICRM_MAILER_SPOOL_PERIOD;
        }

        if ( defined( 'CIVICRM_VERP_SEPARATOR' ) ) {
            $this->verpSeparator = CIVICRM_VERP_SEPARATOR;
        }

        if ( defined( 'CIVICRM_MAILER_BATCH_LIMIT' ) ) {
            $this->mailerBatchLimit = (int) CIVICRM_MAILER_BATCH_LIMIT;
        }

        require_once 'CRM/Core/Component.php';
        CRM_Core_Component::addConfig( $this, true );   
    }


    /**
     * initializes the entire application. Currently we only need to initialize
     * the dataobject framework
     *
     * @return void
     * @access public
     */
    function initialize() 
    {
        if (defined('CIVICRM_DSN')) {
            $this->dsn = CIVICRM_DSN;
        }

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = self::addTrailingSlash(CIVICRM_TEMPLATE_COMPILEDIR);

            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }

        $this->initDAO();

        // also initialize the logger
        self::$_log =& Log::singleton( 'display' );

        if ( defined( 'CIVICRM_UF' ) ) {
            $this->userFramework       = CIVICRM_UF;
        }

        if ( defined( 'CIVICRM_UF_BASEURL' ) ) {
            $this->userFrameworkBaseURL = self::addTrailingSlash( CIVICRM_UF_BASEURL, '/' );
        }

        // set the error callback
        CRM_Core_Error::setCallback();

    }

    /**
     * initialize the DataObject framework
     *
     * @return void
     * @access private
     */
    function initDAO() 
    {
        CRM_Core_DAO::init(
                      $this->dsn, 
                      $this->daoDebug
                      );

        $factoryClass = $this->DAOFactoryClass;
        CRM_Core_DAO::setFactory(new $factoryClass());
    }

    /**
     * returns the singleton logger for the applicationthe singleton logger for the application
     *
     * @param
     * @access private
     * @return object
     */
    static function &getLog() 
    {
        if ( ! isset( self::$_log ) ) {
            self::$_log =& Log::singleton( 'display' );
        }

        return self::$_log;
    }

    /**
     * retrieve a mailer to send any mail from the applciation
     *
     * @param
     * @access private
     * @return object
     */
    static function &getMailer( ) 
    {
        if ( ! isset( self::$_mail ) ) {
            if ( self::$_singleton->smtpServer == '' ||
                 ! self::$_singleton->smtpServer ) {
                CRM_Core_Error::fatal( ts( 'CIVICRM_SMTP_SERVER is not set in the config file' ) ); 
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
        return self::$_mail;
    }

    /**
     * get the domain Id of the current user
     *
     * @param
     * @access private
     * @return int
     */
    static function domainID( ) 
    {
        return self::$_domainID;
    }

    /**
     * delete the web server writable directories
     *
     * @param int $value 1 - clean templates_c, 2 - clean upload, 3 - clean both
     *
     * @access public
     * @return void
     */
    public function cleanup( $value ) 
    {
        $value = (int ) $value;

        if ( $value & 1 ) {
            // clean templates_c
            CRM_Utils_File::cleanDir( $this->templateCompileDir );
        }
        if ( $value & 2 ) {
            // clean upload dir
            CRM_Utils_File::cleanDir( $this->uploadDir );
        }
    }


    /**
     * verify that the needed parameters are not null in the config
     *
     * @param CRM_Core_Config (reference ) the system config object
     * @param array           (reference ) the parameters that need a value
     *
     * @return boolean
     * @static
     * @access public
     */
    static function check( &$config, &$required ) 
    {
        foreach ( $required as $name ) {
            if ( CRM_Utils_System::isNull( $config->$name ) ) {
                return false;
            }
        }
        return true;
    }

    static function addTrailingSlash( $name, $separator = null ) 
    {
        if ( ! $separator ) {
            $separator = DIRECTORY_SEPARATOR;
        }
            
        if ( substr( $name, -1, 1 ) != $separator ) {
            $name .= $separator;
        }
        return $name;
    }

    /**
     * initialize the config variables
     *
     * @return void
     * @access private
     */
    function initVariables() 
    {
        require_once "CRM/Core/BAO/Setting.php";
        $variables = array();
        CRM_Core_BAO_Setting::retrieve($variables);

        if ( empty( $variables ) ) {
            $this->retrieveFromSettings( );

            $variables = get_object_vars($this);

            // if we dont get stuff from the sttings file, apply appropriate defaults
            require_once 'CRM/Admin/Form/Setting.php';
            CRM_Admin_Form_Setting::setValues( $variables );

            CRM_Core_BAO_Setting::add($variables);
        }
        
        $countryIsoCodes = CRM_Core_PseudoConstant::countryIsoCode( );

        $specialArray = array('countryLimit', 'provinceLimit');
        $paymentArray = array('paymentCertPath', 'paymentUsername');
        $urlArray     = array('userFrameworkResourceURL', 'imageUploadURL');
        $dirArray     = array('uploadDir','customFileUploadDir');
        
        foreach($variables as $key => $value) {
            if ( in_array($key, $specialArray) && is_array($value) ) {
                $country = array();
                foreach( $value as $val ) {
                    $country[] = $countryIsoCodes[$val]; 
                }

                $value = $country;
            } else if ( $key == 'defaultContactCountry' ) {
                $value =  $countryIsoCodes[$value];
            } else if (strpos($key, '_')) {
                list($v, $p) = explode("_", $key);
                if (in_array($v, $paymentArray)) {
                    $key = $v;
                    
                    if ( isset( $this->$key ) &&
                         is_array( $this->$key ) ) {
                        $d = $this->$key;
                    } else {
                        $d = array( );
                    }
                    $d[$p] = $value;
                    $value = $d;
                }
            } else if ( in_array($key, $urlArray) ) {
                $value = self::addTrailingSlash( $value, '/' );
            } else if ( in_array($key, $dirArray) ) {
                $value = self::addTrailingSlash( $value );
                CRM_Utils_File::createDir( $value );
            } else if ( $key == 'lcMessages' ) {
                // reset the templateCompileDir to locale-specific and make sure it exists
                $this->templateCompileDir .= self::addTrailingSlash($value);
                CRM_Utils_File::createDir( $this->templateCompileDir );
            }
            
            $this->$key = $value;       
        }
        
        if ( $this->userFrameworkResourceURL ) {
            $this->resourceBase = $this->userFrameworkResourceURL;
        } 
            
        if ( !$this->customFileUploadDir ) {
            $this->customFileUploadDir = $this->uploadDir;
        }
        
        if ( $this->addressFormat ) {
            
            // trim the format and unify line endings to LF
            $format = trim($this->addressFormat);
            $format = str_replace(array("\r\n", "\r"), "\n", $format);
            
            // get the field sequence from the format
            $newSequence = array();
            foreach($this->addressSequence as $field) {
                if (substr_count($format, $field)) {
                    $newSequence[strpos($format, $field)] = $field;
                }
            }
            ksort($newSequence);

            // add the addressSequence fields that are missing in the addressFormat
            // to the end of the list, so that (for example) if state_province is not
            // specified in the addressFormat it's still in the address-editing form
            $newSequence = array_merge($newSequence, $this->addressSequence);
            $newSequence = array_unique($newSequence);

            $this->addressSequence = $newSequence;
            $this->addressFormat   = $format;
        }
        
        if ( $this->defaultCurrency ) {
            $this->defaultCurrencySymbol = CRM_Utils_Array::value($this->defaultCurrency, $this->currencySymbols, '');
        }    
        
        if ( $this->mapProvider ) {
            $this->geocodeMethod = 'CRM_Utils_Geocode_'. $this->mapProvider ;
        }
        
        require_once 'CRM/Core/Component.php';
        CRM_Core_Component::addConfig( $this );   
        
        //CRM_Core_Error::debug('this', $this );
    }

} // end CRM_Core_Config

?>
