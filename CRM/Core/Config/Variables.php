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
    public $smartyDir           = '/opt/local/lib/php/Smarty/';
    public $pluginsDir          = '/opt/local/lib/php/Smarty/plugins/';

    /**
     * the root directory of our template tree
     * @var string
     */
    public $templateDir		  = './templates/';



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
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     * @var object
     * @static
     */
    private static $_singleton = null;

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


    /**
     * The constructor. Sets domain id if defined, otherwise assumes
     * single instance installation.
     *
     * @return void
     * @access private
     */
    private function __construct() 
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

    /**
     * singleton function used to manage this object
     *
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
    static public function &singleton($key = 'crm', $loadFromDB = true ) 
    {
        if (self::$_singleton === null ) {

            // first, attempt to get configuration object from cache
            require_once 'CRM/Utils/Cache.php';
            $cache =& CRM_Utils_Cache::singleton( );
            self::$_singleton = $cache->get( 'CRM_Core_Config' );

            // if not in cache, fire off config construction
            if ( ! self::$_singleton ) {
                self::$_singleton =& new CRM_Core_Config($key);
                self::$_singleton->_initialize( );
                
                //initialize variable. for gencode we cannot load from the
                //db since the db might not be initialized
                if ( $loadFromDB ) {
                    self::$_singleton->initVariables( );
                    
                    // retrieve and overwrite stuff from the settings file
                    self::$_singleton->addCoreVariables( );
                }
                $cache->set( 'CRM_Core_Config', self::$_singleton );
            } else {
                // we retrieve the object from memcache, so we now initialize the objects
                self::$_singleton->_initialize( );
            }
            self::$_singleton->initialized = 1;
        }

        return self::$_singleton;
    }


    /**
     * Initializes the entire application.
     *
     * @return void
     * @access public
     */
    private function _initialize() 
    {
        if (defined( 'CIVICRM_DSN' )) {
            $this->dsn = CIVICRM_DSN;
        }

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = CRM_Utils_File::addTrailingSlash(CIVICRM_TEMPLATE_COMPILEDIR);

            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }

        $this->_initDAO();

        // also initialize the logger
        self::$_log =& Log::singleton( 'display' );

        if ( defined( 'CIVICRM_UF' ) ) {
            $this->userFramework       = CIVICRM_UF;
        }

        if ( defined( 'CIVICRM_UF_BASEURL' ) ) {
            $this->userFrameworkBaseURL = CRM_Utils_File::addTrailingSlash( CIVICRM_UF_BASEURL, '/' );
        }

        if ( defined( 'CIVICRM_GETTEXT_RESOURCEDIR' ) ) {
            $this->gettextResourceDir = CRM_Utils_File::addTrailingSlash( CIVICRM_GETTEXT_RESOURCEDIR );
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
    private function _initDAO() 
    {
        CRM_Core_DAO::init(
                      $this->dsn, 
                      $this->daoDebug
                      );

        $factoryClass = $this->DAOFactoryClass;
        CRM_Core_DAO::setFactory(new $factoryClass());
    }

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

        if ( defined( 'CIVICRM_MYSQL_PATH' ) ) {
            $this->mysqlPath = CRM_Utils_File::addTrailingSlash( CIVICRM_MYSQL_PATH );
        }

        if ( defined( 'CIVICRM_SMTP_PASSWORD' ) ) {
            $this->smtpPassword = CIVICRM_SMTP_PASSWORD;
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

    function retrieveFromSettings( ) {
         if (defined('CIVICRM_DEBUG') ) {
             $this->debug = CIVICRM_DEBUG;
            
             // check for backtrace only if debug is enabled
             if ( defined( 'CIVICRM_BACKTRACE' ) ) {
                 $this->backtrace = CIVICRM_BACKTRACE;
             }
         }

         if ( defined( 'CIVICRM_SMTP_PASSWORD' ) ) {
             $this->smtpPassword = CIVICRM_SMTP_PASSWORD;
         }

         if ( defined( 'CIVICRM_UF_RESOURCEURL' ) ) {
             $this->userFrameworkResourceURL = CRM_Utils_File::addTrailingSlash( CIVICRM_UF_RESOURCEURL, '/' );
             $this->resourceBase             = $this->userFrameworkResourceURL;
         }

        require_once 'CRM/Core/Component.php';
        $this->componentRegistry =& new CRM_Core_Component();
        $this->componentRegistry->addConfig( $this, true );        

    }




    /**
     * returns the singleton logger for the application
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
            CRM_Utils_File::cleanDir ( $this->templateCompileDir );
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }
        if ( $value & 2 ) {
            // clean upload dir
            CRM_Utils_File::cleanDir ( $this->uploadDir );
            CRM_Utils_File::createDir( $this->uploadDir );
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
            require_once 'CRM/Core/Config/Defaults.php';
            CRM_Core_Config_Defaults::setValues( $variables );

            CRM_Core_BAO_Setting::add($variables);
        }
        
        $urlArray     = array('userFrameworkResourceURL', 'imageUploadURL');
        $dirArray     = array('uploadDir','customFileUploadDir');
        
        foreach($variables as $key => $value) {
            if ( in_array($key, $urlArray) ) {
                $value = CRM_Utils_File::addTrailingSlash( $value, '/' );
            } else if ( in_array($key, $dirArray) ) {
                $value = CRM_Utils_File::addTrailingSlash( $value );
                CRM_Utils_File::createDir( $value );
            } else if ( $key == 'lcMessages' ) {
                // reset the templateCompileDir to locale-specific and make sure it exists
                $this->templateCompileDir .= CRM_Utils_File::addTrailingSlash($value);
                CRM_Utils_File::createDir( $this->templateCompileDir );
            }
            
            $this->$key = $value;       
        }
        
        if ( $this->userFrameworkResourceURL ) {
            // we need to do this here so all blocks also load from an ssl server
            if ( isset( $_SERVER['HTTPS'] ) &&
                 strtolower( $_SERVER['HTTPS'] ) != 'off' ) {
                CRM_Utils_System::mapConfigToSSL( );
            }
            $this->resourceBase = $this->userFrameworkResourceURL;
        } 
            
        if ( !$this->customFileUploadDir ) {
            $this->customFileUploadDir = $this->uploadDir;
        }
        
        if ( $this->mapProvider ) {
            $this->geocodeMethod = 'CRM_Utils_Geocode_'. $this->mapProvider ;
        }
        
        require_once 'CRM/Core/Component.php';
        $this->componentRegistry =& new CRM_Core_Component();
        $this->componentRegistry->addConfig( $this );
    }

    function addressSequence( ) {
        require_once 'CRM/Core/BAO/Preferences.php';
        return CRM_Core_BAO_Preferences::value( 'address_sequence' );
    }


    function defaultCurrencySymbol( ) {
        static $cachedSymbol = null;
        if ( ! $cachedSymbol ) {
            if ( $this->defaultCurrency ) {
                require_once "CRM/Core/PseudoConstant.php";
                $currencySymbolName = CRM_Core_PseudoConstant::currencySymbols( 'name' );
                $currencySymbol     = CRM_Core_PseudoConstant::currencySymbols( );
                
                $this->currencySymbols = CRM_Utils_Array::combine( $currencySymbolName, $currencySymbol );
                
                $cachedSymbol = CRM_Utils_Array::value($this->defaultCurrency, $this->currencySymbols, '');
            } else {
                $cachedSymbol = '$';
            }
        }
        return $cachedSymbol;
    }

    function defaultContactCountry( ) {
        static $cachedContactCountry = null;
        if ( ! $cachedContactCountry ) {
            $countryIsoCodes = CRM_Core_PseudoConstant::countryIsoCode( );
            $cachedContactCountry = $countryIsoCodes[$this->defaultContactCountry];
        }
        return $cachedContactCountry;
    }

    function defaultContactCountryName( ) {
        static $cachedContactCountryName = null;
        if ( ! $cachedContactCountryName ) {
            $countryCodes = CRM_Core_PseudoConstant::country( );
            $cachedContactCountryName = $countryCodes[$this->defaultContactCountry];
        }
        return $cachedContactCountryName;
    }

    function countryLimit( ) {
        static $cachedCountryLimit = null;
        if ( ! $cachedCountryLimit ) {
            $countryIsoCodes = CRM_Core_PseudoConstant::countryIsoCode( );
            $country = array();
            if ( is_array( $this->countryLimit ) ) {
                foreach( $this->countryLimit as $val ) {
                    $country[] = $countryIsoCodes[$val]; 
                }
            } else {
                $country[] = $countryIsoCodes[$this->countryLimit];
            }
            $cachedCountryLimit = $country;
        }
        return $cachedCountryLimit;
    }

    function provinceLimit( ) {
        static $cachedProvinceLimit = null;
        if ( ! $cachedProvinceLimit ) {
            $countryIsoCodes = CRM_Core_PseudoConstant::countryIsoCode( );
            $country = array();
            if ( is_array( $this->provinceLimit ) ) {
                foreach( $this->provinceLimit as $val ) {
                    $country[] = $countryIsoCodes[$val]; 
                }
            } else {
                $country[] = $countryIsoCodes[$this->provinceLimit];
            }
            $cachedProvinceLimit = $country;
        }
        return $cachedProvinceLimit;
    }


} // end CRM_Core_Config

?>
