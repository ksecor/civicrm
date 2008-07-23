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
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'Log.php';
require_once 'Mail.php';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/File.php';
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/Config/Variables.php';

class CRM_Core_Config extends CRM_Core_Config_Variables
{
    ///
    /// BASE SYSTEM PROPERTIES (CIVICRM.SETTINGS.PHP)
    ///

    /**
     * the dsn of the database connection
     * @var string
     */
    public $dsn;

    /**
     * the name of user framework
     * @var string
     */
    public $userFramework               = 'Drupal';

    /**
     * the name of user framework url variable name
     * @var string
     */
    public $userFrameworkURLVar         = 'q';

    /**
     * the dsn of the database connection for user framework
     * @var string
     */
    public $userFrameworkDSN            = null;

    /**
     * The root directory where Smarty should store
     * compiled files
     * @var string
     */
    public $templateCompileDir  = './templates_c/en_US/';

    // END: BASE SYSTEM PROPERTIES (CIVICRM.SETTINGS.PHP)

    ///
    /// BEGIN HELPER CLASS PROPERTIES
    ///
    
    /**
     * are we initialized and in a proper state
     * @var string
     */
    public $initialized = 0;

    /**
     * the factory class used to instantiate our DB objects
     * @var string
     */
    private $DAOFactoryClass	  = 'CRM_Contact_DAO_Factory';

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
     * component registry object (of CRM_Core_Component type)
     */
    public $componentRegistry  = null;

    ///
    /// END HELPER CLASS PROPERTIES
    ///

    ///
    /// RUNTIME SET CLASS PROPERTIES
    ///

    /**
     * to determine wether the call is from cms or civicrm 
     */
    public $inCiviCRM  = false;

    ///
    /// END: RUNTIME SET CLASS PROPERTIES
    ///


    /**
     * The constructor. Sets domain id if defined, otherwise assumes
     * single instance installation.
     *
     * @return void
     * @access private
     */
    private function __construct() 
    {
    }

    /**
     * Singleton function used to manage this object.
     *
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
    static public function &singleton($key = 'crm', $loadFromDB = true, $force = false ) 
    {
        if ( self::$_singleton === null || $force ) {

            // first, attempt to get configuration object from cache
            require_once 'CRM/Utils/Cache.php';
            $cache =& CRM_Utils_Cache::singleton( );
            self::$_singleton = $cache->get( 'CRM_Core_Config' );

            // if not in cache, fire off config construction
            if ( ! self::$_singleton ) {
                self::$_singleton =& new CRM_Core_Config($key);
                self::$_singleton->_initialize( );
                
                //initialize variables. for gencode we cannot load from the
                //db since the db might not be initialized
                if ( $loadFromDB ) {
                    self::$_singleton->_initVariables( );
                    
                    // retrieve and overwrite stuff from the settings file
                    self::$_singleton->setCoreVariables( );
                }
                $cache->set( 'CRM_Core_Config', self::$_singleton );
            } else {
                // we retrieve the object from memcache, so we now initialize the objects
                self::$_singleton->_initialize( );
            }
            self::$_singleton->initialized = 1;

            if ( isset( self::$_singleton->customPHPPathDir ) &&
                 self::$_singleton->customPHPPathDir ) {
                $include_path = self::$_singleton->customPHPPathDir . PATH_SEPARATOR . get_include_path( );
                set_include_path( $include_path );
            }
        }

        return self::$_singleton;
    }


    /**
     * Initializes the entire application.
     * Reads constants defined in civicrm.settings.php and
     * stores them in config properties.
     *
     * @return void
     * @access public
     */
    private function _initialize() 
    {

        // following variables should be set in CiviCRM settings and
        // as crucial ones, are defined upon initialisation
        // instead of in CRM_Core_Config_Defaults
        if (defined( 'CIVICRM_DSN' )) {
            $this->dsn = CIVICRM_DSN;
        }

        if (defined('CIVICRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = CRM_Utils_File::addTrailingSlash(CIVICRM_TEMPLATE_COMPILEDIR);

            // we're automatically prefixing compiled templates directories with country/language code
            if ( ! empty( $this->lcMessages ) ) {
                $this->templateCompileDir .= CRM_Utils_File::addTrailingSlash($this->lcMessages);
            }

            // make sure this directory exists
            CRM_Utils_File::createDir( $this->templateCompileDir );
        }


        if ( defined( 'CIVICRM_UF' ) ) {
            $this->userFramework       = CIVICRM_UF;
            if ( $this->userFramework == 'Joomla' ) {
                $this->userFrameworkURLVar = 'task';
            }
            $this->userFrameworkClass  = 'CRM_Utils_System_'    . $this->userFramework;
            $this->userHookClass       = 'CRM_Utils_Hook_'      . $this->userFramework;
            $this->userPermissionClass = 'CRM_Core_Permission_' . $this->userFramework;            
        } else {
            echo 'You need to define CIVICRM_UF in civicrm.settings.php';
            exit( );
        }

        if ( defined( 'CIVICRM_UF_BASEURL' ) ) {
            $this->userFrameworkBaseURL = CRM_Utils_File::addTrailingSlash( CIVICRM_UF_BASEURL, '/' );
            if ( isset( $_SERVER['HTTPS'] ) &&
                 strtolower( $_SERVER['HTTPS'] ) != 'off' ) {
                $this->userFrameworkBaseURL     = str_replace( 'http://', 'https://', 
                                                               $this->userFrameworkBaseURL );
            }            
        }

        if ( defined( 'CIVICRM_UF_DSN' ) ) { 
            $this->userFrameworkDSN = CIVICRM_UF_DSN;
        }

        // this is dynamically figured out in the civicrm.settings.php file
        if ( defined( 'CIVICRM_CLEANURL' ) ) {        
            $this->cleanURL = CIVICRM_CLEANURL;
        } else {
            $this->cleanURL = 0;
        }

        if ( $this->userFramework == 'Joomla' ) {
            $this->userFrameworkVersion        = '1.0';
            $this->userFrameworkUsersTableName = 'jos_users';
        }

        $this->_initDAO( );

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
    private function _initDAO() 
    {
        CRM_Core_DAO::init( $this->dsn );

        $factoryClass = $this->DAOFactoryClass;
        require_once str_replace('_', DIRECTORY_SEPARATOR, $factoryClass) . '.php';
        CRM_Core_DAO::setFactory(new $factoryClass());
    }

    /**
     * returns the singleton logger for the application
     *
     * @param
     * @access private
     * @return object
     */
    static public function &getLog() 
    {
        if ( ! isset( self::$_log ) ) {
            self::$_log =& Log::singleton( 'display' );
        }

        return self::$_log;
    }

    /**
     * initialize the config variables
     *
     * @return void
     * @access private
     */
    private function _initVariables() 
    {

        // initialize component registry early to avoid "race" 
        // between CRM_Core_Config and CRM_Core_Component (they
        // are co-dependant)
        require_once 'CRM/Core/Component.php';
        $this->componentRegistry = new CRM_Core_Component();

        // retrieve serialised settings
        require_once "CRM/Core/BAO/Setting.php";
        $variables = array();
        CRM_Core_BAO_Setting::retrieve($variables);  

        // if settings are not available, go down the full path
        if ( empty( $variables ) ) {
            // Step 1. get system variables with their hardcoded defaults
            $variables = get_object_vars($this);

            // Step 2. get default values (with settings file overrides if
            // available - handled in CRM_Core_Config_Defaults)
            require_once 'CRM/Core/Config/Defaults.php';
            CRM_Core_Config_Defaults::setValues( $variables );

            // add component specific settings
            $this->componentRegistry->addConfig( $this );
            
            // serialise settings 
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
            } elseif (self::$_singleton->outbond_option == 0) {
                if ( self::$_singleton->smtpServer == '' ||
                     ! self::$_singleton->smtpServer ) {
                    CRM_Core_Error::fatal( ts( 'There is no valid smtp server setting. Click <a href=\'%1\'>Administer CiviCRM >> Global Settings</a> to set the SMTP Server.', array( 1 => CRM_Utils_System::url('civicrm/admin/setting', 'reset=1')))); 
                }
                
                $params['host'] = self::$_singleton->smtpServer ? self::$_singleton->smtpServer : 'localhost';
                $params['port'] = self::$_singleton->smtpPort ? self::$_singleton->smtpPort : 25;
                
                if (self::$_singleton->smtpAuth) {
                    $params['username'] = self::$_singleton->smtpUsername;
                    $params['password'] = self::$_singleton->smtpPassword;
                    $params['auth']     = true;
                } else {
                    $params['auth']     = false;
                }

                // set the localhost value, CRM-3153
                $params['localhost'] = $_SERVER['SERVER_NAME'];

                self::$_mail =& Mail::factory( 'smtp', $params );
            } elseif (self::$_singleton->outbond_option == 1) {
                if ( self::$_singleton->sendmail_path == '' ||
                     ! self::$_singleton->sendmail_path ) {
                    CRM_Core_Error::fatal( ts( 'There is no valid sendmail path setting. Click <a href=\'%1\'>Administer CiviCRM >> Global Settings</a> to set the Sendmail Server.', array( 1 => CRM_Utils_System::url('civicrm/admin/setting', 'reset=1')))); 
                }
                $params['sendmail_path'] = self::$_singleton->sendmail_path;
                $params['sendmail_args'] = self::$_singleton->sendmail_args;
                
                self::$_mail =& Mail::factory( 'sendmail', $params );
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
        CRM_Core_Error::backtrace( 'Aborting due to invalid call to domainID' );
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
     * reset the serialized array and recompute
     * use with care
     */
    function reset( ) {
        $query = "UPDATE civicrm_domain SET config_backend = null";
        CRM_Core_DAO::executeQuery( $query );
    }

} // end CRM_Core_Config
