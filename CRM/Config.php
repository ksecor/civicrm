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
 * Config handles all the run time configuration changes that the system
 * needs to deal with. Typically we'll have different values for a user's
 * sandbox, a qa sandbox and a production area. The default values in 
 * general, should reflect production values (minimizes chances of screwing
 * up)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'Log.php';
require_once 'CRM/DAO.php';

class CRM_Config {

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
     * the factory class used to instantiate our DB objects
     * @var string
     */
    public $DAOFactoryClass	  = 'CRM_Contact_DAO_Factory';

    /**
     * The handle to the log that we are using
     * @var object
     */
    private static $_log = null;

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
    static function singleton($key = 'crm') {
        if (self::$_singleton === null ) {
            self::$_singleton = new CRM_Config($key);
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
        if (defined('CRM_DSN')) {
            $this->dsn = CRM_DSN;
        }

        if (defined('CRM_DAO_DEBUG') ) {
            $this->daoDebug = CRM_DAO_DEBUG;
        }

        if (defined('CRM_DAO_FACTORY_CLASS') ) {
            $this->DAOFactoryClass = CRM_DAO_FACTORY_CLASS;
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

        // initialize the framework
        $this->init();
    }

    /**
     * initializes the entire application. Currently we only need to initialize
     * the dataobject framework
     *
     * @return void
     * @access public
     */
    function init() {
        $this->initDAO();

        // also initialize the logger
        self::$_log =& Log::singleton( 'display' );
    }

    /**
     * initialize the DataObject framework
     *
     * @ereturn void
     * @access private
     */
    function initDAO() {
        CRM_DAO::init(
                      $this->dsn, 
                      $this->daoDebug
                      );

        $factoryClass = $this->DAOFactoryClass;
        CRM_Utils::import($factoryClass);
        CRM_DAO::setFactory(new $factoryClass());
    }

    static function &getLog() {
        if ( ! isset( self::$_log ) ) {
            self::$_log =& Log::singleton( 'display' );
        }

        return self::$_log;
    }

} // end CRM_Config

?>