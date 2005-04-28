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

//require_once 'PEAR.php';
require_once 'PEAR/ErrorStack.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Smarty.php';

require_once 'Log.php';

class CRM_Error extends PEAR_ErrorStack {

    /**
     * status code of various types of errors
     * @var const
     */
    const
        FATAL_ERROR = 2;


    /**
     * filename of the error template
     * @var const
     */
    const
        ERROR_TEMPLATE = 'error.tpl';
  

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
     * create the main callback method. this method centralizes error processing.
     *
     * the errors we expect are from the pear modules DB, DB_DataObject
     * which currently use PEAR::raiseError to notify of error messages.
     *
     * @param object PEAR_Error
     *
     * @return void
     * @access public
     */
    public static function handle($pear_error)
    {
        // setup smarty with config, session and template location.
        $template = CRM_Core_Smarty::singleton( );
        
        // create the error array
        $error = array();
        $error['callback']   = $pear_error->getCallback();
        $error['code']       = $pear_error->getCode();
        $error['message']    = $pear_error->getMessage();
        $error['mode']       = $pear_error->getMode();
        $error['debug_info'] = $pear_error->getDebugInfo();
        $error['type']       = $pear_error->getType();
        $error['user_info']  = $pear_error->getUserInfo();
        $error['to_string']  = $pear_error->toString();

        $template->assign_by_ref('error', $error);
        
        $template->assign( 'tplFile', "CRM/" . CRM_Error::ERROR_TEMPLATE); 
        $content = $template->fetch( 'CRM/index.tpl' );
        CRM_Error::debug( 'error', $error );

        return CRM_System::theme( 'page', $content );
        exit(1);
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

        CRM_Error::debug( $code, $message );
        CRM_Error::debug( 'BT', debug_backtrace( ) );
        $fileName = 'error.tpl';
        CRM_System::theme( 'fatal_error', $fileName, $vars );

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


    /**
     * Similar to the function debug. Only difference is 
     * in the formatting of the output.
     *
     * @param  string variable name
     * @param  mixed  reference to variables that we need a trace of
     * @param  bool   should we log or return the output
     *
     * @return string the generated output
     *
     * @access public
     *
     * @static
     *
     * @see CRM_Error::debug()
     * @see CRM_Error::debug_log_message()
     */
   static function debug_var($variable_name, &$variable, $log=true)
    {
        // check if variable is set
        if(!isset($variable)) {
            $out = "\$$variable_name is not set";
        } else {
            $out = print_r($variable, true);
            $out = "\$$variable_name = $out";
            // reset if it is an array
            if(is_array($variable)) {
                reset($variable);
            }
        }
        return self::debug_log_message($out);
    }
    
    

    /**
     * output backtrace of the program.
     *
     * @param  int  max trace level.
     * @param  bool   should we log or return the output
     *
     * @return string format of the backtrace
     *
     * @access public
     *
     * @static
     */
    static function debug_stacktrace($trace_level=0, $log=true) {
        $backtrace = debug_backtrace();

        if($trace_level) {
            // since trace level is specified use it to slice the backtrace array.
            $num_element = count($backtrace);
            $backtrace = array_slice($backtrace, 0, ($num_element>$trace_level ? $trace_level : $num_element));
        }

        $out = print_r($backtrace, true);
        $out = "<br />backtrace<br /><pre>$out</pre>";
        return self::debug_log_message($out);
    }




    /**
     * log an entry into a method
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    static function le_method()
    {
        $array1 = debug_backtrace();
        $string1 = "entering method " . $array1[1]['class'] . "::" . $array1[1]['function'] . "() in " . $array1[0]['file']; 
        self::debug_log_message($string1);
    }



    /**
     * log an exit out of a method
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    function ll_method()
    {
        $array1 = debug_backtrace();
        $string1 = "leaving  method " . $array1[1]['class'] . "::" . $array1[1]['function'] . "() in " . $array1[0]['file']; 
        self::debug_log_message($string1);
    }


    /**
     * log an entry into a function
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    function le_function()
    {
        $array1 = debug_backtrace();
        $string1 = "entering function " . $array1[1]['function'] . "() in " . basename($array1[0]['file']);
        self::debug_log_message($string1);
    }


    /**
     * log an exit out of a function
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    function ll_function()
    {
        $array1 = debug_backtrace();
        $string1 = "leaving  function " . $array1[1]['function'] . "() in " . basename($array1[0]['file']);
        self::debug_log_message($string1);
    }


    /**
     * log an entry into a file
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    function le_file()
    {
        $array1 = debug_backtrace();
        $string1 .= "entering file " . $array1[0]['file'];
        self::debug_log_message($string1);
    }


    /**
     * log an exit out of a file
     *
     * @param  none
     *
     * @return string format of the output
     *
     * @access public
     *
     * @static
     */
    function ll_file()
    {
        $array1 = debug_backtrace();
        $string1 = "leaving  file " . $array1[0]['file'];
        self::debug_log_message($string1);  
    }


    /**
     * display the error message on terminal
     *
     * @param  string message to be output
     * @param  bool   should we log or return the output
     *
     * @return string format of the backtrace
     *
     * @access public
     *
     * @static
     */
    static function debug_log_message($message="", $log=true)
    {
        // $file_log = Log::singleton('file', '/tmp/CRM.LOG');
        // $file_log->log("$message\n");
        $error =& self::singleton( );
        $out = "<p /><code>$message</code>";
        if ($log) {
            // echo $out;
        }
        return $out;
    }


}

PEAR_ErrorStack::singleton('CRM', false, null, 'CRM_Error');

?>