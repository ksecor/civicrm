<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * Config handles all the run time configuration changes that the system needs to deal with.
 * Typically we'll have different values for a user's sandbox, a qa sandbox and a production area.
 * The default values in general, should reflect production values (minimizes chances of screwing up)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'Log.php';
require_once 'Mail.php';

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Utils/Recent.php';
require_once 'CRM/Contact/DAO/Factory.php';


class CRM_Core_Config {

    /**
     * the dsn of the database connection
     * @var string
     */
    public $dsn;

    /**
     * the debug level for DB_DataObject
     * @var int
     */
    public $daoDebug		  = 0;

    /**
     * the directory where Smarty is installed
     * @var string
     */
    public $smartyDir           = '/opt/local/lib/php/Smarty/';

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
    public $smtpServer         = 'localhost';
    public $smtpPort           = 25;

    /**
     * Default user framework
     */
    public $userFramework       = 'Drupal';
    public $userFrameworkClass  = 'CRM_Utils_System_Drupal';
    public $userPermissionClass = 'CRM_Core_Permission_Drupal';
    public $userFrameworkURLVar = 'q';

    /**
     * The default mysql version that we are using
     */
    public $mysqlVersion = 4.1;

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
        $session =& CRM_Core_Session::singleton( );
        if ( defined( 'CRM_DOMAIN_ID' ) ) {
            self::$_domainID = CRM_DOMAIN_ID;
        } else {
            self::$_domainID = 1;
        }
        $session->set( 'domainID', self::$_domainID );

        if (defined('CRM_DSN')) {
            $this->dsn = CRM_DSN;
        }

        if (defined('CRM_Core_DAO_DEBUG') ) {
            $this->daoDebug = CRM_Core_DAO_DEBUG;
        }

        if (defined('CRM_Core_DAO_FACTORY_CLASS') ) {
            $this->DAOFactoryClass = CRM_Core_DAO_FACTORY_CLASS;
        }

        if (defined('CRM_SMARTYDIR')) {
            $this->smartyDir = CRM_SMARTYDIR;
        }

        if (defined('CRM_TEMPLATEDIR')) {
            $this->templateDir = CRM_TEMPLATEDIR;
        }

        if (defined('CRM_TEMPLATE_COMPILEDIR')) {
            $this->templateCompileDir = CRM_TEMPLATE_COMPILEDIR;
        }

        if ( defined( 'CRM_MAINMENU' ) ) {
            $this->mainMenu = CRM_MAINMENU;
        }

        if ( defined( 'CRM_HTTPBASE' ) ) {
            $this->httpBase = CRM_HTTPBASE;
        }

        if ( defined( 'CRM_RESOURCEBASE' ) ) {
            $this->resourceBase = CRM_RESOURCEBASE;
        }

        if ( defined( 'CRM_UPLOAD_DIR' ) ) {
            $this->uploadDir = CRM_UPLOAD_DIR;
        }

        if ( defined( 'CRM_CLEANURL' ) ) {
            $this->cleanURL = CRM_CLEANURL;
        }

        if ( defined( 'CRM_LC_MESSAGES' ) ) {
            $this->lcMessages = CRM_LC_MESSAGES;
        }
        
        if ( defined( 'CRM_DATEFORMAT_DATETIME' ) ) {
            $this->dateformatDatetime = CRM_DATEFORMAT_DATETIME;
        }
        
        if ( defined( 'CRM_DATEFORMAT_FULL' ) ) {
            $this->dateformatFull = CRM_DATEFORMAT_FULL;
        }
        
        if ( defined( 'CRM_DATEFORMAT_PARTIAL' ) ) {
            $this->dateformatPartial = CRM_DATEFORMAT_PARTIAL;
        }
        
        if ( defined( 'CRM_DATEFORMAT_YEAR' ) ) {
            $this->dateformatYear = CRM_DATEFORMAT_YEAR;
        }
        
        if ( defined( 'CRM_DATEFORMAT_QF_DATE' ) ) {
            $this->dateformatQfDate = CRM_DATEFORMAT_QF_DATE;
        }
        
        if ( defined( 'CRM_DATEFORMAT_QF_DATETIME' ) ) {
            $this->dateformatQfDatetime = CRM_DATEFORMAT_QF_DATETIME;
        }
        
        if ( defined( 'CRM_GETTEXT_CODESET' ) ) {
            $this->gettextCodeset = CRM_GETTEXT_CODESET;
        }
        
        if ( defined( 'CRM_GETTEXT_DOMAIN' ) ) {
            $this->gettextDomain = CRM_GETTEXT_DOMAIN;
        }
        
        if ( defined( 'CRM_GETTEXT_RESOURCE_DIR' ) ) {
            $this->gettextResourceDir = CRM_GETTEXT_RESOURCE_DIR;
        }

        if ( defined( 'CRM_SMTP_SERVER' ) ) {
            $this->smtpServer = CRM_SMTP_SERVER;
        }

        if ( defined( 'CRM_USERFRAMEWORK' ) ) {
            $this->userFramework       = CRM_USERFRAMEWORK;
            $this->userFrameworkClass  = 'CRM_Utils_System_'    . $this->userFramework;
            $this->userPermissionClass = 'CRM_Core_Permission_' . $this->userFramework;
        }

        if ( defined( 'CRM_USERFRAMEWORK_URLVAR' ) ) {
            $this->userFrameworkURLVar = CRM_USERFRAMEWORK_URLVAR;
        }

        if ( defined( 'CRM_MYSQL_VERSION' ) ) {
            $this->mysqlVersion = CRM_MYSQL_VERSION;
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

    static function &getLog() {
        if ( ! isset( self::$_log ) ) {
            self::$_log =& Log::singleton( 'display' );
        }

        return self::$_log;
    }

    static function &getMailer( ) {
        if ( ! isset( self::$_mail ) ) {
            $params['host'] = self::$_singleton->smtpServer;
            $params['port'] = self::$_singleton->smtpPort;
            $params['auth'] = false;

            self::$_mail =& Mail::factory( 'smtp', $params );
        }
        return self::$_mail;
    }

    static function domainID( ) {
        return self::$_domainID;
    }

} // end CRM_Core_Config

?>
