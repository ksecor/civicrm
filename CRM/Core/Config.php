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
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'Log.php';
require_once 'Mail.php';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Recent.php';
require_once 'CRM/Utils/File.php';
require_once 'CRM/Contact/DAO/Factory.php';
require_once 'CRM/Core/Session.php';

class CRM_Core_Config {

    /**
     * constants to determine method of geocode resolution
     */
    const
        GEOCODE_RPC = 1,
        GEOCODE_ZIP = 2;

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
    public $templateDir		  = './templates';

    /**
     * The root directory where Smarty should store
     * compiled files
     * @var string
     */
    public $templateCompileDir  = './templates_c';

    /**
     * The root url of our application. Used when we don't
     * know where to redirect the application flow
     * @var string
     */
    public $mainMenu            = 'http://localhost/drupal/';

    /**
     * The httpBase of our application. Used when we want to compose
     * absolute url's
     * @var string
     */
    public $httpBase            = "http://localhost/drupal/";

    /**
     * The resourceBase of our application. Used when we want to compose
     * url's for things like js/images/css
     * @var string
     */
    public $resourceBase        = "http://localhost/drupal/crm/";

    /**
     * the factory class used to instantiate our DB objects
     * @var string
     */
    public $DAOFactoryClass	  = 'CRM_Contact_DAO_Factory';

    /**
     * The directory to store uploaded files
     */
    public $uploadDir         = './upload/';

    /**
     * Are we generating clean url's and using mod_rewrite
     * @var string
     */
    public $cleanURL = false;

    /**
     * List of country codes limiting the country list.
     * @var string
     */
    public $countryLimit = '';

    /**
     * List of country codes limiting the province list.
     * @var string
     */
    public $provinceLimit = 'US';

    /**
     * Database id of default country for contact.
     * @var int
     */
    public $defaultContactCountryId = 1228;

    /**
     * Locale for the application to run with.
     * @var string
     */
    public $lcMessages = 'en_US';

    /**
     * The format of the address fields.
     * @var string
     */
    public $addressFormat = "street_address\nsupplemental_address_1\nsupplemental_address_2\ncity, state_province postal_code\ncountry";

    /**
     * The sequence of the address fields.
     * @var string
     */
    public $addressSequence = array('street_address', 'supplemental_address_1', 'supplemental_address_2', 'city', 'state_province', 'postal_code', 'country');

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
    public $gettextResourceDir = './l10n';

    /**
     * Default smtp server and port
     */
    public $smtpServer         = null;
    public $smtpPort           = 25;

    /**
     * Default user framework
     */
    public $userFramework               = 'Drupal';
    public $userFrameworkClass          = 'CRM_Utils_System_Drupal';
    public $userHookClass               = 'CRM_Utils_Hook_Drupal';
    public $userPermissionClass         = 'CRM_Core_Permission_Drupal';
    public $userFrameworkURLVar         = 'q';
    public $userFrameworkDSN            = null;
    public $userFrameworkUsersTableName = 'users';
    public $userFrameworkBaseURL        = null;

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
     * Google Map API Key if google map support needed
     *
     * @var boolean
     */
    public $googleMapAPIKey = null;

    /**
     * How should we get geo code information if google map support needed
     *
     * @var boolean
     */
    public $geocodeMethod    = '';

    /**
     * How long should we wait before checking for new outgoing mailings?
     *
     * @var int
     */
    public $mailerPeriod    = 180;

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
     * singleton function used to manage this object
     *
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
    static function &singleton($key = 'crm') {
        if (self::$_singleton === null ) {
            self::$_singleton =& new CRM_Core_Config($key);
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
    function __construct() {
        require_once 'CRM/Core/Session.php';
        $session =& CRM_Core_Session::singleton( );
        if ( defined( 'CIVICRM_DOMAIN_ID' ) ) {
            self::$_domainID = CIVICRM_DOMAIN_ID;
        } else {
            self::$_domainID = 1;
        }
        $session->set( 'domainID', self::$_domainID );

        if (defined('CIVICRM_DSN')) {
            $this->dsn = CIVICRM_DSN;
        }

        if (defined('UF_DSN')) {
            $this->ufDSN = UF_DSN;
        }

        if (defined('UF_USERTABLENAME')) {
            $this->ufUserTableName = UF_USERTABLENAME;
        }

        if (defined('CIVICRM_DEBUG') ) {
            $this->debug = CIVICRM_DEBUG;
        }

        if (defined('CIVICRM_DAO_DEBUG') ) {
            $this->daoDebug = CIVICRM_DAO_DEBUG;
        }

        if (defined('CIVICRM_DAO_FACTORY_CLASS') ) {
            $this->DAOFactoryClass = CIVICRM_DAO_FACTORY_CLASS;
        }

        if (defined('CIVICRM_SMARTYDIR')) {
            $this->smartyDir = CIVICRM_SMARTYDIR;
        }

        if (defined('CIVICRM_PLUGINSDIR')) {
            $this->pluginsDir = CIVICRM_PLUGINSDIR;
        }

        if (defined('CIVICRM_TEMPLATEDIR')) {
            $this->templateDir = CIVICRM_TEMPLATEDIR;
        }

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = CIVICRM_TEMPLATE_COMPILEDIR;

            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }

        if ( defined( 'CIVICRM_MAINMENU' ) ) {
            $this->mainMenu = CIVICRM_MAINMENU;
        }

        if ( defined( 'CIVICRM_HTTPBASE' ) ) {
            $this->httpBase = CIVICRM_HTTPBASE;
        }

        if ( defined( 'CIVICRM_RESOURCEBASE' ) ) {
            $this->resourceBase = CIVICRM_RESOURCEBASE;
        }

        if ( defined( 'CIVICRM_UPLOADDIR' ) ) {
            $this->uploadDir = CIVICRM_UPLOADDIR;
            if ( substr( $this->uploadDir, -1, 1 ) != DIRECTORY_SEPARATOR ) { 
                $this->uploadDir .= DIRECTORY_SEPARATOR;
            }

            CRM_Utils_File::createDir( $this->uploadDir );
        }

        if ( defined( 'CIVICRM_CLEANURL' ) ) {
            $this->cleanURL = CIVICRM_CLEANURL;
        }

        if ( defined( 'CIVICRM_COUNTRY_LIMIT' ) ) {
            $this->countryLimit = CIVICRM_COUNTRY_LIMIT;
        }
        
        if ( defined( 'CIVICRM_PROVINCE_LIMIT' ) ) {
            $this->provinceLimit = CIVICRM_PROVINCE_LIMIT;
        }
        
        if ( defined( 'CIVICRM_DEFAULT_CONTACT_COUNTRY_ID' ) ) {
            $this->defaultContactCountryId = CIVICRM_DEFAULT_CONTACT_COUNTRY_ID;
        }        
        
        if ( defined( 'CIVICRM_LC_MESSAGES' ) ) {
            $this->lcMessages = CIVICRM_LC_MESSAGES;
        }
        
        if ( defined( 'CIVICRM_ADDRESS_FORMAT' ) ) {

            $this->addressFormat = trim(CIVICRM_ADDRESS_FORMAT);

            // get the field sequence from the format, using the class's
            // default as the filter for allowed fields (FIXME?)
            $allowedFields = $this->addressSequence;
            $this->addressSequence = array();
            foreach($allowedFields as $field) {
                if (substr_count($this->addressFormat, $field)) {
                    $this->addressSequence[strpos($this->addressFormat, $field)] = $field;
                }
            }
            ksort($this->addressSequence);
            
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
        
        if ( defined( 'CIVICRM_GETTEXT_CODESET' ) ) {
            $this->gettextCodeset = CIVICRM_GETTEXT_CODESET;
        }
        
        if ( defined( 'CIVICRM_GETTEXT_DOMAIN' ) ) {
            $this->gettextDomain = CIVICRM_GETTEXT_DOMAIN;
        }
        
        if ( defined( 'CIVICRM_GETTEXT_RESOURCEDIR' ) ) {
            $this->gettextResourceDir = CIVICRM_GETTEXT_RESOURCEDIR;
        }

        if ( defined( 'CIVICRM_SMTP_SERVER' ) ) {
            $this->smtpServer = CIVICRM_SMTP_SERVER;
        }

        if ( defined( 'CIVICRM_SMTP_PORT' ) ) {
            $this->smtpPort = CIVICRM_SMTP_PORT;
        }

        if ( defined( 'CIVICRM_UF' ) ) {
            $this->userFramework       = CIVICRM_UF;
            $this->userFrameworkClass  = 'CRM_Utils_System_'    . $this->userFramework;
            $this->userHookClass       = 'CRM_Utils_Hook_'      . $this->userFramework;
            $this->userPermissionClass = 'CRM_Core_Permission_' . $this->userFramework;
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
            $this->userFrameworkBaseURL = CIVICRM_UF_BASEURL;
        }

        if ( defined( 'CIVICRM_MYSQL_VERSION' ) ) {
            $this->mysqlVersion = CIVICRM_MYSQL_VERSION;
        }

        if ( defined( 'CIVICRM_MYSQL_PATH' ) ) {
            $this->mysqlPath = CIVICRM_MYSQL_PATH;
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

        if ( defined( 'CIVICRM_GOOGLE_MAP_API_KEY' ) ) {
            $this->googleMapAPIKey = CIVICRM_GOOGLE_MAP_API_KEY;
        }

        if ( defined( 'CIVICRM_GEOCODE_METHOD' ) ) {
            if ( CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_ZipTable' ||
                 CIVICRM_GEOCODE_METHOD == 'CRM_Utils_Geocode_RPC' ) {
                $this->geocodeMethod = CIVICRM_GEOCODE_METHOD;
            }
        }

        if ( defined( 'CIVICRM_MAILER_SPOOL_PERIOD' ) ) {
            $this->mailerPeriod = CIVICRM_MAILER_SPOOL_PERIOD;
        }

        // initialize the framework
        $this->initialize();
    }

    /**
     * initializes the entire application. Currently we only need to initialize
     * the dataobject framework
     *
     * @return void
     * @access public
     */
    function initialize() {
        $this->initDAO();

        // also initialize the logger
        self::$_log =& Log::singleton( 'display' );

        // set the error callback
        CRM_Core_Error::setCallback();
    }

    /**
     * initialize the DataObject framework
     *
     * @return void
     * @access private
     */
    function initDAO() {
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
    static function &getLog() {
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
    static function &getMailer( ) {
        if ( ! isset( self::$_mail ) ) {
            $params['host'] = self::$_singleton->smtpServer;
            $params['port'] = self::$_singleton->smtpPort;
            $params['auth'] = false;

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
    static function domainID( ) {
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
    public function cleanup( $value ) {
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

    
} // end CRM_Core_Config

?>
