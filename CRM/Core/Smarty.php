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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'Smarty/Smarty.class.php';

/**
 *
 */
class CRM_Core_Smarty extends Smarty {

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * class constructor
     *
     * @param string $templateDir root directory for all the templates
     * @param string $compileDir  where should all the compiled templates be stored
     *
     * @return CRM_Core_Smarty
     * @access private
     */
    function __construct( $templateDir, $compileDir ) {
        parent::__construct( );

        $this->template_dir = $templateDir;
        $this->compile_dir  = $compileDir;
        $this->use_sub_dirs = true;
        $this->plugins_dir  = array ( CRM_SMARTYDIR . 'plugins', CRM_PLUGINSDIR );

        // add the session and the config here
        $config  =& CRM_Core_Config::singleton ();
        $session =& CRM_Core_Session::singleton();
        $recent  =& CRM_Utils_Recent::get( );

        $this->assign_by_ref( 'config'        , $config  );
        $this->assign_by_ref( 'session'       , $session );
        $this->assign_by_ref( 'recentlyViewed', $recent  );

        $this->register_function ( 'crmURL', array( 'CRM_Utils_System', 'crmURL' ) );
    }

    /**
     * Static instance provider.
     *
     * Method providing static instance of SmartTemplate, as
     * in Singleton pattern.
     */
    static function &singleton( ) {
        if ( ! isset( self::$_singleton ) ) {
            self::$_singleton =& new CRM_Core_Smarty( CRM_TEMPLATEDIR, CRM_TEMPLATE_COMPILEDIR );
        }
        return self::$_singleton;
    }

    /**
     * executes & returns or displays the template results
     *
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     * @param boolean $display
     */
    function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false)
    {
        // hack for now, we need to execute this at the end to allow the modules to
        // add new menu items etc, this CANNOT go in the smarty constructor
        $config  =& CRM_Core_Config::singleton ();
        CRM_Utils_Menu::createLocalTasks( $_GET[$config->userFrameworkURLVar] );

        return parent::fetch( $resource_name, $cache_id, $compile_id, $display );
    }
}

?>
