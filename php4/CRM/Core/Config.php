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

$GLOBALS['_CRM_CORE_CONFIG']['_log'] =  null;
$GLOBALS['_CRM_CORE_CONFIG']['_singleton'] =  null;

require_once 'Log.php';
require_once 'CRM/Core/DAO.php';
//require_once 'getLog.php';
require_once 'Log.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/System.php';
require_once 'CRM/Contact/DAO/Factory.php';

class CRM_Core_Config {

    /**
     * the dsn of the database connection
     * @var string
     */
    var $dsn;

    /**
     * the debug level for DB_DataObject
     * @var int
     */
    var $daoDebug		  = 0;

    /**
     * the directory where Smarty is installed
     * @var string
     */
    var $smartyDir           = '/opt/local/lib/php/Smarty/';

    /**
     * the root directory of our template tree
     * @var string
     */
    var $templateDir		  = './templates';

    /**
     * The root directory where Smarty should store
     * compiled files
     * @var string
     */
    var $templateCompileDir  = './templates_c';

    /**
     * The root url of our application. Used when we don't
     * know where to redirect the application flow
     * @var string
     */
    var $mainMenu            = 'http://localhost/drupal/';

    /**
     * The httpBase of our application. Used when we want to compose
     * absolute url's
     * @var string
     */
    var $httpBase            = "http://localhost/drupal/";

    /**
     * The resourceBase of our application. Used when we want to compose
     * url's for things like js/images/css
     * @var string
     */
    var $resourceBase        = "http://localhost/drupal/crm/";

    /**
     * the factory class used to instantiate our DB objects
     * @var string
     */
    var $DAOFactoryClass	  = 'CRM_Contact_DAO_Factory';

    /**
     * The directory to store uploaded files
     */
    var $uploadDir         = './upload/';

    /**
     * Are we generating clean url's and using mod_rewrite
     * @var string
     */
    var $cleanURL = false;

    /**
     * Locale for the application to run with.
     * @var string
     */
    var $lcMessages = 'en_US';

    /**
     * Default encoding of strings returned by gettext
     * @var string
     */
    var $gettextCodeset = 'utf-8';


    /**
     * Default name for gettext domain.
     * @var string
     */
    var $gettextDomain = 'civicrm';

    /**
     * Default location of gettext resource files.
     */
    var $gettextResourceDir = './l10n';


    /**
     * The handle to the log that we are using
     * @var object
     */
    

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     * @var object
     * @static
     */
    

    /**
     * singleton function used to manage this object
     *
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
     function singleton($key = 'crm') {
        if ($GLOBALS['_CRM_CORE_CONFIG']['_singleton'] === null ) {
            $GLOBALS['_CRM_CORE_CONFIG']['_singleton'] = new CRM_Core_Config($key);
        }
        return $GLOBALS['_CRM_CORE_CONFIG']['_singleton'];
    }

    /**
     * The constructor. Basically redefines the class variables if
     * it finds a constant definition for that class variable
     *
     * @return object
     * @access private
     */
    function CRM_Core_Config() {
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

	if ( defined( 'CRM_GETTEXT_CODESET' ) ) {
	    $this->gettextCodeset = CRM_GETTEXT_CODESET;
	}

	if ( defined( 'CRM_GETTEXT_DOMAIN' ) ) {
	    $this->gettextDomain = CRM_GETTEXT_DOMAIN;
	}

	if ( defined( 'CRM_GETTEXT_RESOURCE_DIR' ) ) {
	    $this->gettextResourceDir = CRM_GETTEXT_RESOURCE_DIR;
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
        $GLOBALS['_CRM_CORE_CONFIG']['_log'] =& Log::singleton( 'display' );
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

     function &getLog() {
        if ( ! isset( $GLOBALS['_CRM_CORE_CONFIG']['_log'] ) ) {
            $GLOBALS['_CRM_CORE_CONFIG']['_log'] =& Log::singleton( 'display' );
        }

        return $GLOBALS['_CRM_CORE_CONFIG']['_log'];
    }

} // end CRM_Core_Config

?>