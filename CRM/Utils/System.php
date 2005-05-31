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


/**
 * System wide utilities.
 *
 */
class CRM_Utils_System {

    /**
     * special cases for php4
     * @var array
     * @static
     */
    public static $php4SpecialClassName = array(
                                                'crm'                => 'CRM',
                                                'addtogroup'         => 'AddToGroup',
                                                'addtohousehold'     => 'AddToHousehold',
                                                'addtotag'           => 'AddToTag',
                                                'api'                => 'API',
                                                'bao'                => 'BAO',
                                                'customdata'         => 'CustomData',
                                                'customfield'        => 'CustomField',
                                                'customgroup'        => 'CustomGroup',
                                                'customvalue'        => 'CustomValue',
                                                'dao'                => 'DAO',
                                                'dynamicgroup'       => 'DynamicGroup',
                                                'entitycategory'     => 'EntityCategory',
                                                'entitytag'          => 'EntityTag',
                                                'geocoord'           => 'GeoCoord',
                                                'groupcontact'       => 'GroupContact',
                                                'groupmember'        => 'GroupMember',
                                                'im'                 => 'IM',
                                                'improvider'         => 'IMProvider',
                                                'locationtype'       => 'LocationType',
                                                'mapfield'           => 'MapField',
                                                'mobileprovider'     => 'MobileProvider',
                                                'pseudoconstant'     => 'PseudoConstant',
                                                'relationshiptype'   => 'RelationshipType',
                                                'removefromgroup'    => 'RemoveFromGroup',
                                                'savedsearch'        => 'SavedSearch',
                                                'savesearch'         => 'SaveSearch',
                                                'selectvalues'       => 'SelectValues',
                                                'showhideblocks'     => 'ShowHideBlocks',
                                                'statemachine'       => 'StateMachine',
                                                'stateprovince'      => 'StateProvince',
                                                'ufformfield'        => 'UFFormField',
                                                'ufform'             => 'UFForm',
                                                'uploadfile'         => 'UploadFile',
                                                );
    
    /**
     * Compose a new url string from the current url string
     * Used by all the framework components, specifically,
     * pager, sort and qfc
     *
     * @param string $urlVar the url variable being considered (i.e. crmPageID, crmSortID etc)
     *
     * @return string the url fragment
     * @access public
     */
    static function makeURL( $urlVar ) {
        return self::url( $_GET['q'], self::getLinksUrl( $urlVar ) );
    }

    /**
     * get the query string and clean it up. Strip some variables that should not
     * be propagated, specically variable like 'reset'. Also strip any side-affect
     * actions (i.e. export)
     *
     * This function is copied mostly verbatim from Pager.php (_getLinksUrl)
     *
     * @param string  $urlVar       the url variable being considered (i.e. crmPageID, crmSortID etc)
     * @param boolean $includeReset should we include the reset var (generally this variable should be skipped)
     * @return string
     * @access public
     */
    static function getLinksUrl( $urlVar, $includeReset = false ) {
        // Sort out query string to prevent messy urls
        $querystring = array();
        $qs          = array();
        $arrays      = array();

        if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
            $qs = explode('&', str_replace( '&amp;', '&', $_SERVER['QUERY_STRING'] ) );
            for ($i = 0, $cnt = count($qs); $i < $cnt; $i++) {
                if ( strstr( $qs[$i], '=' ) !== false ) { // check first if exist a pair
                    list($name, $value) = explode( '=', $qs[$i] );
                    if ( $name != $urlVar ) {
                        $name = rawurldecode($name);
                        //check for arrays in parameters: site.php?foo[]=1&foo[]=2&foo[]=3
                        if ((strpos($name, '[') !== false) &&
                            (strpos($name, ']') !== false)
                            ) {
                            $arrays[] = $qs[$i];
                        } else {
                            $qs[$name] = $value;
                        }
                    }
                } else {
                    $qs[$qs[$i]] = '';
                }
                unset( $qs[$i] );
            }
        }

        // add force=1 to force a recompute
        $qs['force'] = 1;
        foreach ($qs as $name => $value) {
            if ( $name != 'reset' || $includeReset ) {
                $querystring[] = $name . '=' . $value;
            }
        }

        $querystring = array_merge($querystring, array_unique($arrays));
        $querystring = array_map('htmlentities', $querystring);

        return implode('&amp;', $querystring) . (! empty($querystring) ? '&amp;' : '') . $urlVar .'=';
    }

    /**
     * if we are using a theming system, invoke theme, else just print the
     * content
     *
     * @param string  $type    name of theme object/file
     * @param string  $content the content that will be themed
     * @param array   $args    the args for the themeing function if any
     * @param boolean $print   are we displaying to the screen or bypassing theming?
     * 
     * @return void           prints content on stdout
     * @access public
     */
    function theme( $type, &$content, $args = null, $print = false ) {
        if ( function_exists( 'theme' ) && ! $print ) {
            print theme( $type, $content, $args );
        } else {
            print $content;
        }
    }

    /**
     * Generate an internal CiviCRM URL (copied from DRUPAL/includes/common.inc#url)
     *
     * @param $path     string   The path being linked to, such as "civicrm/add"
     * @param $query    string   A query string to append to the link.
     * @param $absolute boolean  Whether to force the output to be an absolute link (beginning with http:).
     *                           Useful for links that will be displayed outside the site, such as in an
     *                           RSS feed.
     * @param $fragment string   A fragment identifier (named anchor) to append to the link.
     *
     * @return string            an HTML string containing a link to the given path.
     * @access public
     *
     */
    function url($path = null, $query = null, $absolute = true, $fragment = null ) {
        $config        = CRM_Core_Config::singleton( );
        static $script = 'index.php';

        if (isset($fragment)) {
            $fragment = '#'. $fragment;
        }

        $base = ($absolute ? $config->httpBase : '');

        if (! $config->cleanURL ) {
            if ( isset( $path ) ) {
                if ( isset( $query ) ) {
                    return $base . $script .'?q='. $path .'&amp;'. $query . $fragment;
                } else {
                    return $base . $script .'?q='. $path . $fragment;
                }
            } else {
                if ( isset( $query ) ) {
                    return $base . $script .'?'. $query . $fragment;
                } else {
                    return $base . $fragment;
                }
            }
        } else {
            if ( isset( $path ) ) {
                if ( isset( $query ) ) {
                    return $base . $path .'?'. $query . $fragment;
                } else {
                    return $base . $path . $fragment;
                }
            } else {
                if ( isset( $query ) ) {
                    return $base . $script .'?'. $query . $fragment;
                } else {
                    return $base . $fragment;
                }
            }
        }
    }

    /**
     * What menu path are we currently on. Called for the primary tpl
     *
     * @return string the current menu path
     * @access public
     */
    static function currentPath( ) {
        return trim($_GET['q'], '/');
    }

    /**
     * this function is called from a template to compose a url
     *
     * @param array $params list of parameters
     * 
     * @return string url
     * @access public
     */
    function crmURL( $params ) {
        $p = CRM_Utils_Array::value( 'p', $params );
        if ( ! isset( $p ) ) {
            $p = self::currentPath( );
        }

        return self::url( $p,
                          CRM_Utils_Array::value( 'q', $params ),
                          CRM_Utils_Array::value( 'a', $params, true ),
                          CRM_Utils_Array::value( 'f', $params ) );
    }

    /**
     * sets the title of the page
     *
     * @param string $title
     *
     * @return void
     * @access public
     */
    function setTitle( $title ) {
        drupal_set_title( $title );
    }

    /**
     * figures and sets the userContext. Uses the referer if valid
     * else uses the default
     *
     * @param array  $names   refererer should match any str in this array
     * @param string $default the default userContext if no match found
     *
     * @return void
     * @access public
     */
    static function setUserContext( $names, $default = null ) {
        $url = $default;

        $session =& CRM_Core_Session::singleton();
        $referer = CRM_Utils_Array::value( 'HTTP_REFERER', $_SERVER );

        if ( $referer && ! empty( $names ) ) {
            foreach ( $names as $name ) {
                if ( strstr( $referer, $name ) ) {
                    $url = $referer;
                    break;
                }
            }
        }

        if ( $url ) {
            // CRM_Core_Error::debug( 'pushing: ', $url );
            $session->pushUserContext( $url );
        }
    }

    
    /**
     * Create a class name to file location within the CRM tree
     *
     * This is used primarily by the PHP4 code since the
     * get_class($this) in php4 returns the class name in lowercases.
     *
     * We need to do some conversions before we can use the lower case class names.
     *
     * @param  string $className - class name of a crm
     * @param  boolean $phpFile - whether we need a php extension or template extension
     * @return string $fileName  - full file name of the class
     *
     * @access public
     * @static
     */
    static function classNameToFile($className, $phpFile=true) {
        
        // get all components of the class name
        $classNameComponent = explode("_", $className);

        if (self::isPHP4()) {
            foreach ($classNameComponent as &$v) {
                if (array_key_exists($v, self::$php4SpecialClassName)) {
                    $v = self::$php4SpecialClassName[$v];        // special case hence replace
                } else {
                    $v = ucfirst($v);                            // regular component so just upcase first character
                }
            }
        }

        // create the file name
        $fileName = implode(DIRECTORY_SEPARATOR, $classNameComponent);
        if ($phpFile) {
            $fileName .= ".php";    // for php files
        } else {
            $fileName .= ".tpl";    // for templates
        }
        return $fileName;
    }

    /**
     * gets a class name for an object
     *
     * This is used primarily by the PHP4 code since the
     * get_class($this) in php4 returns the class name in lowercases.
     *
     * We need to do some conversions before we can use the lower case class names.
     *
     * @param  object $object     - object whose class name is needed
     * @return string $className  - class name as per PHP4
     *
     * @access public
     * @static
     */
    static function getClassName($object)
    {
        $className = get_class($object);
        if (!self::isPHP4()) {
            return $className;
        }
        
        // get all components of the class name
        $classNameComponent = explode("_", $className);
        foreach ($classNameComponent as &$v) {
            if (array_key_exists($v, self::$php4SpecialClassName)) {
                $v = self::$php4SpecialClassName[$v];                   // special case hence replace
            } else {
                $v = ucfirst($v);                            // regular component so just upcase first character
            }
        }

        // create the file name
        $className = implode('_', $classNameComponent);
        return $className;
    }

    /**
     * check if PHP4 ?
     *
     * @param none
     * @return boolean true if php4 false otherwise
     * @access public
     * @static
     */
    static function isPHP4()
    {
        return (substr(phpversion(), 0, 1) == 4) ? true:false;
    }
}


/**
 * Allow PHP5 to autoload classes automatically
 * cuts down on number of require_once statements :)
 *
 * @param string $class name of class to be autoloaded
 *
 * @return void
 * @access public
 */
function __autoload( $class ) {
    if ( substr( $class, 0, 4 ) != 'CRM_' ) {
        return;
    }

    $classPath = str_replace( '_', '/', $class ) . '.php';
    require_once($classPath);
}

?>