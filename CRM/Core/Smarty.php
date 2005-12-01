<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

/**
 * Fix for bug CRM-392. Not sure if this is the best fix or it will impact
 * other similar PEAR packages. doubt it
 */
if ( ! class_exists( 'Smarty' ) ) {
    require_once 'Smarty/Smarty.class.php';
}


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
     * @return CRM_Core_Smarty
     * @access private
     */
    function __construct( ) {
        parent::__construct( );

        $config =& CRM_Core_Config::singleton( );

        $this->template_dir = $config->templateDir;
        $this->compile_dir  = $config->templateCompileDir;
        $this->use_sub_dirs = true;
        $this->plugins_dir  = array ( $config->smartyDir . 'plugins', $config->pluginsDir );

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
            $config =& CRM_Core_Config::singleton( );
            self::$_singleton =& new CRM_Core_Smarty( $config->templateDir, $config->templateCompileDir );
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
        require_once 'CRM/Utils/Menu.php';

        // hack for now, we need to execute this at the end to allow the modules to
        // add new menu items etc, this CANNOT go in the smarty constructor
        $config  =& CRM_Core_Config::singleton ();
        CRM_Utils_Menu::createLocalTasks( $_GET[$config->userFrameworkURLVar] );

        return parent::fetch( $resource_name, $cache_id, $compile_id, $display );
    }
}

?>
