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
 * This interface defines methods that need to be implemented
 * for a component to introduce itself to the system.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

abstract class CRM_Core_Component_Info
{

    /*
     * Name of the class (minus component namespace path) 
     * of the component invocation class'es name. 
     */
    const COMPONENT_INVOKE_CLASS = 'Invoke';
                             
                                   
    /*
     * Name of the class (minus component namespace path) 
     * of the component menu definition class'es name.     
     */
    const COMPONENT_MENU_CLASS = 'Menu';

    /*
     * Name of the class (minus component namespace path) 
     * of the component configuration class'es name.
     */
    const COMPONENT_CONFIG_CLASS = 'Config';

    /*
     * Name of the class (minus component namespace path) 
     * of the component BAO Query class'es name.
     */
    const COMPONENT_BAO_QUERY_CLASS = 'BAO_Query';

    /*
     * Name of the class (minus component namespace path) 
     * of the component user dashboard plugin.
     */
    const COMPONENT_USERDASHBOARD_CLASS = 'Page_UserDashboard';

    /*
     * Stores component information.
     * @var array component settings as key/value pairs
     */
    public $info;

    /*
     * Class constructor, sets name and namespace (those are stored
     * in the component registry (database) and no need to duplicate
     * them here, as well as populates the info variable.
     * 
     * @param string $name name of the component
     * @param string $namespace namespace prefix for component's files
     * @access public
     * 
     */
    public function __construct( $name, $namespace, $componentID )
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->componentID = $componentID;
        $this->info = $this->getInfo();
    }                                                          

    /**
     * Provides base information about the component.
     * Needs to be implemented in component's information
     * class.
     *
     * @return array collection of required component settings
     * @access public
     *
     */
    abstract public function getInfo();

    /**
     * Provides permissions that are used by component.
     * Needs to be implemented in component's information
     * class.     
     *
     * @return array|null collection of permissions, null if none
     * @access public
     *
     */
    abstract public function getPermissions();


    /**
     * Provides information about user dashboard element
     * offered by this component.
     *
     * @return array|null collection of required dashboard settings, 
     *                    null if no element offered
     * @access public
     *
     */
    abstract public function getUserDashboardElement();

    /**
     * Provides potential activity types that this 
     * component might want to register in activity history.
     * Needs to be implemented in component's information
     * class.     
     *
     * @return array|null collection of activity types
     * @access public
     *
     */
    abstract public function getActivityTypes();


    /**
     * Provides information whether given component is currently 
     * marked as enabled in configuration.
     *
     * @return boolean true if component is enabled, false if not
     * @access public
     *
     */
    public function isEnabled( )
    {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
        if( in_array( $this->info['name'], $config->enableComponents ) ) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Provides component's configuration object.
     * 
     * @return mixed component's configuration object
     * @access public
     *
     */
    public function getConfigObject( ) 
    {
        return $this->_instantiate( self::COMPONENT_CONFIG_CLASS );
    }

    /**
     * Provides component's menu definition object.
     * 
     * @return mixed component's menu definition object
     * @access public
     *
     */
    public function getMenuObject( ) 
    {
        return $this->_instantiate( self::COMPONENT_MENU_CLASS );
    }

    /**
     * Provides component's invocation object.
     * 
     * @return mixed component's invocation object
     * @access public
     *
     */
    public function getInvokeObject( ) 
    {
        return $this->_instantiate( self::COMPONENT_INVOKE_CLASS );
    }

    /**
     * Provides component's BAO Query object.
     * 
     * @return mixed component's BAO Query object
     * @access public
     *
     */
    public function getBAOQueryObject( ) 
    {
        return $this->_instantiate( self::COMPONENT_BAO_QUERY_CLASS );
    }

    /**
     * Provides component's user dashboard page object.
     * 
     * @return boolean true if component needs search integration
     * @access public
     *
     */
    public function getUserDashboardObject( )
    {
        return $this->_instantiate( self::COMPONENT_USERDASHBOARD_CLASS );
    }

    /**
     * Provides information whether given component uses system wide search.
     * 
     * @return boolean true if component needs search integration
     * @access public
     *
     */
    public function usesSearch( )
    {
        return $this->info['search'] ? true : false;
    }

    /**
     * Provides the xml menu files
     * 
     * @return array array of menu files
     * @access public
     *
     */
    public function menuFiles( )
    {
        return $this->info['menu'] ? $this->info['menu'] : array( );
    }





    /**
     * Helper for instantiating component's elements.
     * 
     * @return mixed component's element as class instance
     * @access private
     *
     */
    private function _instantiate( $cl )
    {
        $className = $this->namespace . '_' . $cl;
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
        return new $className( );
    }

}
