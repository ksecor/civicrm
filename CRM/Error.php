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
 * Start of the Error framework. We should check out and inherit from
 * PEAR_ErrorStack and use that framework
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'PEAR/ErrorStack.php';

require_once 'CRM/Config.php';

class CRM_Error extends PEAR_ErrorStack {

    /**
     * status code of various types of errors
     * @var const
     */
    const
        FATAL_ERROR = 2;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     * @var object
     * @static
     */
    private static $_singleton = null;

    /**
     * The logger object for this application
     * @var object
     * @static
     */
    private static $_log       = null;

    /**
     * singleton function used to manage this object. This function is not
     * explicity declared static to be compatible with PEAR_ErrorStack
     *  
     * @param string the key in which to record session / log information
     *
     * @return object
     * @static
     *
     */
    function singleton( $key = 'CRM' ) {
        if (self::$_singleton === null ) {
            self::$_singleton = new CRM_Error( $key );
        }
        return self::$_singleton;
    }
  
    /**
     * construcor
     */
    function __construct( $name = 'CRM' ) {
        parent::__construct( $name );

        $log =& CRM_Config::getLog();
        $this->setLogger( $log );
    }

    /**
     * display an error page with an error message describing what happened
     *
     * @param string message  the error message
     * @param string code     the error code if any
     * @param string email    the email address to notify of this situation
     *
     * @return void
     * @static
     * @acess public
     */
    static function fatal($message, $code = null, $email = null) {
        $vars = array( 'message' => $message,
                       'code'    => $code );

        theme( 'fatal_error', 'error.tpl', $vars );

        exit( CRM_Error::FATAL_ERROR );
    }

    /**
     * outputs pre-formatted debug information. Flushes the buffers
     * so we can interrupt a potential POST/redirect
     *
     * @param  string name of debug section
     * @param  mixed  reference to variables that we need a trace of
     * @param  bool   should we log or return the output
     *
     * @return string the generated output
     * @access public
     * @static
     */
    static function debug( $name, &$variable, $log = true ) {
        $error =& self::singleton( );

        $out = print_r( $variable, true );

        $out = "<p>$name</p><p><pre>$out</pre></p><p></p>";
        if ( $log ) {
            echo $out;
        }

        return $out;
    }

    static function debug_stacktrace($trace_level=0, $log = true ) {
        $error =& self::singleton( );

        $backtrace = debug_backtrace();

        if($trace_level) {
            $num_element = count($backtrace);
            $backtrace = array_slice($backtrace, 0, ($num_element>$trace_level ? $trace_level : $num_element));
        }

        $out = print_r($backtrace, true);

        $out = "<p>backtrace</p><p><pre>$out</pre></p><p></p>";
        if ( $log ) {
            echo $out;
        }

        return $out;
    }




}

PEAR_ErrorStack::singleton('CRM', false, null, 'CRM_Error');

?>