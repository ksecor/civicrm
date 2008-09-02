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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * System wide utilities.
 *
 */
class CRM_Utils_System {

    const VERSION = '2.2';

    static $_callbacks = null;

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
    static function makeURL( $urlVar, $includeReset = false, $includeForce = true ) {
        $config   =& CRM_Core_Config::singleton( );

        if ( ! isset( $_GET[$config->userFrameworkURLVar] ) ) {
            return '';
        }

        return self::url( $_GET[$config->userFrameworkURLVar],
                          CRM_Utils_System::getLinksUrl( $urlVar, $includeReset, $includeForce ) );
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
    static function getLinksUrl( $urlVar, $includeReset = false, $includeForce = true ) {
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

        if ($includeForce ) {
            $qs['force'] = 1;
        }
        foreach ($qs as $name => $value) {
            if ( $name == 'snippet' ) {
                continue;
            }

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
     * @param boolean $ret  should we echo or return output
     * 
     * @return void           prints content on stdout
     * @access public
     */
    function theme( $type, &$content, $args = null, $print = false, $ret = false ) {
        if ( function_exists( 'theme' ) && ! $print ) {
            $out = theme( $type, $content, $args );
        } else {
            $out = $content;
        }
        
        if ( $ret ) {
            return $out;
        } else {
            print $out;
        }
    }

    /**
     * Generate an internal CiviCRM URL
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
    function url($path = null, $query = null, $absolute = true,
                 $fragment = null, $htmlize = true, $frontend = false ) {
        // we have a valid query and it has not yet been transformed
        if ( $htmlize && ! empty( $query ) && strpos( $query, '&amp;' ) === false ) {
            $query = htmlentities( $query );
        }

        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' .
                     $config->userFrameworkClass .
                     '::url( $path, $query, $absolute, $fragment, $htmlize, $frontend );' );

    }

    function href( $text, $path = null, $query = null, $absolute = true,
                      $fragment = null, $htmlize = true, $frontend = false ) {
        $url = self::url( $path, $query, $absolute, $fragment, $htmlize, $frontend );
        return "<a href=\"$url\">$text</a>";
    }

    function permissionDenied( ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( "return {$config->userFrameworkClass}::permissionDenied( );" );
    }

    static function logout( ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( "return {$config->userFrameworkClass}::logout( );" );
    }

    /**
     * What menu path are we currently on. Called for the primary tpl
     *
     * @return string the current menu path
     * @access public
     */
    static function currentPath( ) {
        $config =& CRM_Core_Config::singleton( );
        return trim( CRM_Utils_Array::value($config->userFrameworkURLVar,$_GET), '/' );
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
                          CRM_Utils_Array::value( 'q' , $params        ),
                          CRM_Utils_Array::value( 'a' , $params, true  ),
                          CRM_Utils_Array::value( 'f' , $params        ),
                          CRM_Utils_Array::value( 'h' , $params, true  ),
                          CRM_Utils_Array::value( 'fe', $params, false ) );
    }

    /**
     * sets the title of the page
     *
     * @param string $title
     * @param string $pageTitle
     *
     * @return void
     * @access public
     */
    function setTitle( $title, $pageTitle = null ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( $config->userFrameworkClass . '::setTitle( $title, $pageTitle );' );
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
            $session->pushUserContext( $url );
        }
    }


    /**
     * gets a class name for an object
     *
     * @param  object $object      - object whose class name is needed
     * @return string $className   - class name
     *
     * @access public
     * @static
     */
    static function getClassName($object)
    {
        return get_class($object);
    }

    /**
     * redirect to another url
     *
     * @param string $url the url to goto
     *
     * @return void
     * @access public
     * @static
     */
    static function redirect( $url = null ) {
        if ( ! $url ) {
            $url = self::url( 'civicrm/dashboard', 'reset=1' );
        }

        // replace the &amp; characters with &
        // this is kinda hackish but not sure how to do it right
        $url = str_replace( '&amp;', '&', $url );
        header( 'Location: ' . $url );
        exit( );
    }

    /**
     * Append an additional breadcrumb tag to the existing breadcrumb
     *
     * @param string $title
     * @param string $url   
     *
     * @return void
     * @access public
     * @static
     */
    static function appendBreadCrumb( $breadCrumbs ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' . $config->userFrameworkClass . '::appendBreadCrumb( $breadCrumbs );' );
    }

    /**
     * Reset an additional breadcrumb tag to the existing breadcrumb
     *
     * @return void
     * @access public
     * @static
     */
    static function resetBreadCrumb( ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' . $config->userFrameworkClass . '::resetBreadCrumb( );' );
    }

    /**
     * Append a string to the head of the html file
     *
     * @param string $head the new string to be appended
     *
     * @return void
     * @access public
     * @static
     */
    static function addHTMLHead( $bc ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' . $config->userFrameworkClass . '::addHTMLHead( $bc );' );
    }

    /**
     * figure out the post url for the form
     *
     * @param the default action if one is pre-specified
     *
     * @return string the url to post the form
     * @access public
     * @static
     */
    static function postURL( $action ) {
        $config   =& CRM_Core_Config::singleton( );
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' . $config->userFrameworkClass . '::postURL( $action  ); ' );
    }

    /**
     * rewrite various system urls to https
     *
     * @return void
     * access public 
     * @static 
     */ 
    static function mapConfigToSSL( ) {
        $config   =& CRM_Core_Config::singleton( ); 
        $config->userFrameworkResourceURL = str_replace( 'http://', 'https://', 
                                                         $config->userFrameworkResourceURL );
        $config->resourceBase = $config->userFrameworkResourceURL;
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return eval( 'return ' . $config->userFrameworkClass . '::mapConfigToSSL( ); ' );
    }

    /**
     * Get the base URL from the system
     *
     * @param
     *
     * @return string
     * @access public
     * @static
     */
    static function baseURL() {
        $config =& CRM_Core_Config::singleton( );
        return $config->userFrameworkBaseURL;
    }

    static function authenticateScript( $abort = true, $name = null, $pass = null ) {
        // auth to make sure the user has a login/password to do a shell
        // operation
        // later on we'll link this to acl's
        if ( ! $name ) {
            $name = trim( CRM_Utils_Array::value( 'name', $_REQUEST ) );
            $pass = trim( CRM_Utils_Array::value( 'pass', $_REQUEST ) );
        }

        if ( ! $name ) { // its ok to have an empty password
            if ( $abort ) {
                echo "ERROR: You need to send a valid user name and password to execute this file\n";
                exit( 0 );
            } else {
                return false;
            }
        }

        $result = CRM_Utils_System::authenticate( $name, $pass );
        if ( ! $result ) {
            if ( $abort ) {
                echo "ERROR: Invalid username and/or password\n";
                exit( 0 );
            } else {
                return false;
            }
        }

        return $result;
    }

    /** 
     * Authenticate the user against the uf db 
     * 
     * @param string $name     the user name 
     * @param string $password the password for the above user name 
     * 
     * @return mixed false if no auth 
     *               array( contactID, ufID, unique string ) if success 
     * @access public 
     * @static 
     */ 
    static function authenticate( $name, $password ) {
        $config =& CRM_Core_Config::singleton( ); 
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return  
            eval( 'return ' . $config->userFrameworkClass . '::authenticate($name, $password);' ); 

    }

    /**  
     * Set a message in the UF to display to a user
     *  
     * @param string $name     the message to set
     *  
     * @access public  
     * @static  
     */  
    static function setUFMessage( $message ) {
        $config =& CRM_Core_Config::singleton( );  
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userFrameworkClass ) . '.php' );
        return   
            eval( 'return ' . $config->userFrameworkClass . '::setMessage( $message );' );
    }

   

    static function isNull( $value ) {
        if ( ! isset( $value ) || $value === null || $value === '' ) {
            return true;
        }
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $value ) {
                if ( ! self::isNull( $value ) ) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    static function mungeCreditCard( $number, $keep = 4 ) {
        $number = trim( $number );
        if ( empty( $number ) ) {
            return null;
        }
        $replace = str_repeat( '*' , strlen( $number ) - $keep );
        return substr_replace( $number, $replace, 0, -$keep );
    }

    /** parse php modules from phpinfo */
    function parsePHPModules() {
        ob_start();
        phpinfo(INFO_MODULES);
        $s = ob_get_contents();
        ob_end_clean();
        
        $s = strip_tags($s,'<h2><th><td>');
        $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
        $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
        $vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
        $vModules = array();
        for ($i=1;$i<count($vTmp);$i++) {
            if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
                $vName = trim($vMat[1]);
                $vTmp2 = explode("\n",$vTmp[$i+1]);
                foreach ($vTmp2 AS $vOne) {
                    $vPat = '<info>([^<]+)<\/info>';
                    $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
                    $vPat2 = "/$vPat\s*$vPat/";
                    if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
                        $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
                    } elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols
                        $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
                    }
                }
            }
        }
        return $vModules;
    }

    /** get a module setting */
    function getModuleSetting($pModuleName,$pSetting) {
        $vModules = self:: parsePHPModules();
        return $vModules[$pModuleName][$pSetting];
    }
  
    static function memory( $title = null ) {
        static $pid = null;
        if ( ! $pid ) {
            $pid = posix_getpid( );
        }

        $memory = str_replace("\n", '', shell_exec("ps -p". $pid ." -o rss="));
        $memory .= ", " . time( );
        if ( $title ) {
            CRM_Core_Error::debug_var( $title, $memory );
        }
        return $memory;
    }

    static function download( $name, $mimeType, &$buffer ) {
        $now       = gmdate('D, d M Y H:i:s') . ' GMT';

        header('Content-Type: ' . $mimeType); 
        header('Expires: ' . $now);
        
        // lem9 & loic1: IE need specific headers
        $isIE = strstr( $_SERVER['HTTP_USER_AGENT'], 'MSIE' );
        if ( $isIE ) {
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Pragma: no-cache');
        }
    
        print $buffer;
        exit( );
    }

    static function xMemory( $title = null ) {
        $mem = (float ) xdebug_memory_usage( ) / (float ) ( 1024 * 1024 );
        $mem = number_format( $mem, 5 ) . ", " . time( );
        echo "$title: $mem<p>";
        flush( );
    }

    static function fixURL( $url ) {
        $components = parse_url( $url );

        if ( ! $components ) {
            return null;
        }

        // at some point we'll add code here to make sure the url is not
        // something that will mess up up, so we need to clean it up here
        return $url;
    }

    /**
     * make sure the callback is valid in the current context
     *
     * @param string $callback the name of the function
     *
     * @return boolean
     * @static
     */
    static function validCallback( $callback ) {
        if ( self::$_callbacks === null ) {
            self::$_callbacks = array( );
        }

        if ( ! array_key_exists( $callback, self::$_callbacks ) ) {
            if ( strpos( $callback, '::' ) !== false ) {
                list($className, $methodName) = explode('::', $callback);
                $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
                @include_once( $fileName ); // ignore errors if any
                if ( ! class_exists( $className ) ) {
                    self::$_callbacks[$callback] = false;
                } else {
                    // instantiate the class
                    $object =& new $className();
                    if ( ! method_exists( $object, $methodName ) ) {
                        self::$_callbacks[$callback] = false; 
                    } else {
                        self::$_callbacks[$callback] = true;
                    }
                }
            } else {
                self::$_callbacks[$callback] = function_exists( $callback );
            }
        }
        return self::$_callbacks[$callback];
    }

    /**
     * This serves as a wrapper to the php explode function
     * we expect exactly $limit arguments in return, and if we dont
     * get them, we pad it with null
     */
    static function explode( $separator, $string, $limit ) {
        $result = explode( $separator, $string, $limit );
        for ( $i = count( $result ); $i < $limit; $i++ ) {
            $result[$i] = null;
        }
        return $result;
    }

    static function checkURL( $url, $addCookie = false ) {
        CRM_Core_Error::ignoreException( );
        require_once 'HTTP/Request.php';
        $params = array( 'method' => 'HEAD' );
        $request =& new HTTP_Request( $url, $params );
        if ( $addCookie ) {
            foreach ( $_COOKIE as $name => $value ) {
                $request->addCookie( $name, $value );
            }
        }
        $request->sendRequest( );
        $result = $request->getResponseCode( ) == 200 ? true : false;
        CRM_Core_Error::setCallback( );
        return $result;
    }

    static function checkPHPVersion( $ver = 5, $abort = true ) {
        $phpVersion = substr( PHP_VERSION, 0, 1 );
        if ( $phpVersion >= $ver ) {
            return true;
        }

        if ( $abort ) {
            CRM_Core_Error::fatal( ts( 'This feature requires PHP Version %1 or greater',
                                       array( 1 => $ver ) ) );
        }
        return false;
    }

    static function formatWikiURL( $string ) {
        $items = explode( ' ', trim( $string ), 2 );
        if ( count( $items ) == 2 ) {
            $title = $items[1];
        } else {
            $title = $items[0];
        }

        $url = self::urlEncode( $items[0] );
        return "<a href=\"$url\">$title</a>";
    }

    static function urlEncode( $url ) {
        $items = parse_url( $url );
        if ( $items === false ) {
            return null;
        }

        if ( ! CRM_Utils_Array::value( 'query', $items ) ) {
            return $url;
        }

        $items['query'] = urlencode( $items['query'] );

        $url = $items['scheme'] . '://';
        if ( CRM_Utils_Array::value( 'user', $items ) ) {
            $url .= "{$items['user']}:{$items['pass']}@";
        }

        $url .= $items['host'];
        if ( CRM_Utils_Array::value( 'port', $items ) ) {
            $url .= ":{$items['port']}";
        }

        $url .= "{$items['path']}?{$items['query']}";
        if ( CRM_Utils_Array::value( 'fragment', $items ) ) {
            $url .= "#{$items['fragment']}";
        }

        return $url;
    }

    static function version( ) {
        return self::VERSION;
    }

    static function getAllHeaders( ) {
        if ( function_exists( 'getallheaders' ) ) {
            return getallheaders( );
        }

        // emulate get all headers
        // http://www.php.net/manual/en/function.getallheaders.php#66335
        $headers = array( );
        foreach ( $_SERVER as $name => $value ) {
            if ( substr( $name, 0, 5) == 'HTTP_' ) {
                $headers[str_replace( ' ',
                                      '-',
                                      ucwords( strtolower( str_replace( '_',
                                                                        ' ',
                                                                        substr( $name, 5 ) )
                                                           ) )
                                      )] = $value;
            }
        }
        return $headers;
    }

    static function redirectToSSL( $abort = true ) {
        $config = CRM_Core_Config::singleton( );
        if ( $config->enableSSL             &&
             ( ! isset( $_SERVER['HTTPS'] ) ||
               strtolower( $_SERVER['HTTPS'] )  == 'off' ) ) {
            $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            // ensure that SSL is enabled on the base url (for cookie reasons etc)
            $baseURL = str_replace( 'http://', 'https://',
                                    $config->userFrameworkBaseURL );
            if ( ! self::checkURL( $baseURL, true ) ) {
                if ( $abort ) {
                    CRM_Core_Error::fatal( 'HTTPS is not set up on this machine' );
                } else {
                    CRM_Core_Session::setStatus( 'HTTPS is not set up on this machine' );
                    // admin should be the only one following this
                    // since we dont want the user stuck in a bad place
                    return;
                }
            }
            CRM_Utils_System::redirect( $url );
        }
    }

    static function ipAddress( ) {
        $address = CRM_Utils_Array::value( 'REMOTE_ADDR', $_SERVER );
        // hack for safari
        if ( $address == '::1' ) {
            $address = '127.0.0.1';
        }
        return $address;
    }

    /**
     * Returns you the referring / previous page url
     *
     * @return string the previous page url
     * @access public
     */
    static function refererPath( ) {
        return CRM_Utils_Array::value( 'HTTP_REFERER', $_SERVER );
    }

}


