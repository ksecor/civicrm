<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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
 * Component stores all the static and dynamic information of the various
 * CiviCRM components
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Component 
{
    static $_info = null;

    static $_contactSubTypes = null;

    static function &info( ) {
        if ( self::$_info == null ) { 
            self::$_info = array( );

            self::$_info['CiviContribute'] = 
                array( 'title'   => 'CiviCRM Contribution Engine',
                       'path'    => 'CRM_Contribute_',
                       'url'     => 'contribute',
                       'perm'    => array( 'access CiviContribute',
                                           'edit contributions',
                                           'make online contributions' ),
                       'search'  => 1 );

            self::$_info['CiviMember'] = 
                array( 'title'   => 'CiviCRM Membership Engine',
                       'path'    => 'CRM_Member_',
                       'url'     => 'member',
                       'perm'    => array( 'access CiviMember',
                                           'edit memberships'),
                       'search'  => 1 );

            self::$_info['CiviEvent'] = 
                array( 'title'   => 'CiviCRM Event Engine',
                       'path'    => 'CRM_Event_',
                       'url'     => 'event',
                       'perm'    => array( 'access CiviEvent',
                                           'edit event participants',
                                           'register for events' ),
                       'search'  => 1 );
            
            self::$_info['CiviMail'] = 
                array( 'title'   => 'CiviCRM Mailing Engine',
                       'path'    => 'CRM_Mailing_',
                       'url'     => 'mailing',
                       'perm'    => array( 'access CiviMail' ),
                       'search'  => 0 );

            self::$_info['Quest'] =
                array( 'title'   => 'Quest Application Process',
                       'path'    => 'CRM_Quest_',
                       'url'     => 'quest',
                       'perm'    => array( 'access Quest', 
                                           'edit Quest Application',
                                           'view Quest Application',
                                           'edit Quest Recommendation',
                                           'view Quest Recommendation',
                                           'edit Quest Partner Supplement',
                                           'view Quest Partner Supplement'
                                           ),
                       'search'  => 1,
                       'metaTpl' => 'quest',
                       'formTpl' => 'quest',
                       'task'    => array( '32' => array( 'title'  => 'Export XML',
                                                          'class'  => 'CRM_Quest_Form_Task_XML',
                                                          'result' => false ),
                                           '33' => array( 'title'  => 'Export PDF',
                                                          'class'  => 'CRM_Quest_Form_Task_PDF',
                                                          'result' => false ) ) );

            self::$_info['TMF'] =
                array( 'title'   => 'TMF Application Process',
                       'path'    => 'CRM_TMF_',
                       'url'     => 'tmf',
                       'perm'    => array( 'edit TMF Vista Application',
                                           'view TMF Vista Application',
                                           'edit TMF Scholar Application',
                                           'view TMF Scholar Application',
                                           'edit TMF Nomination',
                                           'view TMF Nomination',
                                           ),
                       'search'  => 1,
                       'metaTpl' => 'quest',
                       'formTpl' => 'quest',
                       'task'    => array( '34' => array( 'title'  => 'Export XML',
                                                          'class'  => 'CRM_TMF_Form_Task_XML',
                                                          'result' => false ),                                           
                                           
                                           '35' => array( 'title'  => 'Export PDF',
                                                          'class'  => 'CRM_TMF_Form_Task_PDF',
                                                          'result' => false )
                       ) 
                       )
                       ;

            self::$_info['Gcc'] =
                array( 'title'   => 'Gcc Application Process',
                       'path'    => 'CRM_Gcc_',
                       'url'     => 'gcc',
                       'perm'    => array( 'access Gcc' ),
                       'search'  => 0,
                       'metaTpl' => 'quest',
                       'formTpl' => 'quest',
                       'task'    => array( ) 
                       );

        }
        return self::$_info;
    }

    static function get( $name, $attribute = null) 
    {
        $info =& self::info( );

        $comp = CRM_Utils_Array::value( $name, $info );
        if ( $attribute ) {
            return CRM_Utils_Array::value( $attribute, $comp );
        }
        return $comp;
    }

    static function invoke( &$args, $type ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        $firstArg  = CRM_Utils_Array::value( 1, $args, '' ); 
        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 ( $info[$name]['url'] === $firstArg || $info[$name]['url'] === $secondArg ) ) {
                
                $className = $info[$name]['path'] . 'Invoke';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                
                if ( $type == 'main' ) {
                    // also set the smarty variables to the current component
                    $template =& CRM_Core_Smarty::singleton( );
                    $template->assign( 'activeComponent', $name );
                    if ( CRM_Utils_Array::value( 'metaTpl', $info[$name] ) ) {
                        $template->assign( 'metaTpl', $info[$name]['metaTpl'] );
                    }
                    if ( CRM_Utils_Array::value( 'formTpl', $info[$name] ) ) {
                        $template->assign( 'formTpl', $info[$name]['formTpl'] );
                    }
                    if ( CRM_Utils_Array::value( 'css', $info[$name] ) ) {
                        $styleSheets = '<style type="text/css">@import url(' . "{$config->resourceBase}css/{$info[$name]['css']});</style>";

                        CRM_Utils_System::addHTMLHead( $styleSheet );
                    }
                }
                eval( $className . '::' . $type . '( $args );' );
                return true;
            }
        }
        return false;
    }

    static function &menu( $permissioned = false, $task = null ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );
        $items = array( );
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) ) {
                $className = $info[$name]['path'] . 'Menu';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                if ( $permissioned ) {
                    eval( '$ret = ' . $className . '::permissioned( );' );

                } else {
                    eval( '$ret = ' . $className . '::main( $task );' );
                }
                $items = array_merge( $items, $ret );
            }
        }
        return $items;
    }

    static function addConfig( &$config, $oldMode = false ) 
    {
        $info =& self::info( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) ) {
                $className = $info[$name]['path'] . 'Config';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::add( $config, $oldMode );' );
            }
        }
        return;
    }

    static function &getQueryFields( ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );
        $fields = array( );
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( '$flds =& ' . $className . '::getFields( );' );
                $fields = array_merge( $fields, $flds );
            }
        }
        return $fields;
    }

    static function alterQuery( &$query, $fnName ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::' . $fnName . '( $query );' );
            }
        }
    }

    static function from( $fieldName, $mode, $side ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        $from = null;
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( '$from = ' . $className . '::from( $fieldName, $mode, $side );' );
                if ( $from ) {
                    return $from;
                }
            }
        }
        return $from;
    }

    static function &defaultReturnProperties( $mode ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );
        $properties = null;
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( '$properties =& ' . $className . '::defaultReturnProperties( $mode );' );
                if ( $properties ) {
                    return $properties;
                }
            }
        }
        return $properties;
    }

    static function &buildSearchForm( &$form ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::buildSearchForm( $form );' );
            }
        }
    }

    static function &addShowHide( &$showHide ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::addShowHide( $showHide );' );
            }
        }
    }

    static function searchAction( &$row, $id ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::searchAction( $row, $id );' );
            }
        }
    }

    static function &contactSubTypes( ) 
    {
        if ( self::$_contactSubTypes == null ) {
            self::$_contactSubTypes = array( );

            if ( CRM_Core_Permission::access( 'Quest' ) ) {
            
                // Generalize this at some point
                self::$_contactSubTypes =
                    array(
                          'Student' =>
                          array( 'View' => 
                                 array( 'file'  => 'CRM/Quest/Page/View/Student.php',
                                        'class' => 'CRM_Quest_Page_View_Student' ),
                                 )
                          );
            }
        }
        return self::$_contactSubTypes;
    }

    
    static function &contactSubTypeProperties( $subType, $op ) 
    {
        $properties =& self::contactSubTypes( );
        if ( array_key_exists( $subType, $properties ) &&
             array_key_exists( $op, $properties[$subType] ) ) {
            return $properties[$subType][$op];
        }
        return CRM_Core_DAO::$_nullObject;
    }

    static function &taskList( ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        $tasks = array( );
        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) && 
                 CRM_Utils_Array::value( 'task', $info[$name] ) ) {
                $tasks += $info[$name]['task'];
            }
        }
        return $tasks;
    }

    /**
     * Function to handle table dependencies of components
     *
     * @param array $tables  array of tables
     *
     * @return null
     * @access public
     * @static
     */
    static function tableNames( &$tables ) 
    {
        $info =& self::info( );
        $config =& CRM_Core_Config::singleton( );

        foreach ( $info as $name => $value ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 $value['search'] ) {
                $className = $info[$name]['path'] . 'BAO_Query';
                require_once(str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php');
                eval( $className . '::tableNames( $tables );' );
            }
        }
    }

}

?>
