<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 * Start of the Error framework. We should check out and inherit from
 * PEAR_ErrorStack and use that framework
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'PEAR/ErrorStack.php';

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Smarty.php';

require_once 'Log.php';

class CRM_Core_Error extends PEAR_ErrorStack {

    /**
     * status code of various types of errors
     * @var const
     */
    const
        FATAL_ERROR = 2,
        DUPLICATE_CONTACT = 8001,
        DUPLICATE_CONTRIBUTION = 8002;


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
    function &singleton( $key = 'CRM' ) {
        if (self::$_singleton === null ) {
            self::$_singleton =& new CRM_Core_Error( $key );
        }
        return self::$_singleton;
    }
  
    /**
     * construcor
     */
    function __construct( $name = 'CRM' ) {
        parent::__construct( $name );

        $log =& CRM_Core_Config::getLog();
        $this->setLogger( $log );

        // set up error handling for Pear Error Stack
        $this->setDefaultCallback(array($this, 'handlePES'));
    }

    function displaySessionError( &$error ) {
        if ( is_a( $error, 'CRM_Core_Error' ) ) { 
            $errors = $error->getErrors( ); 
            $message = array( ); 
            foreach ( $errors as $e ) { 
                $message[] = $e['code'] . ':' . $e['message']; 
            } 
            $message = implode( '<br />', $message ); 
            $status = "Payment Processor Error message:<br/> " . $message; 
            $session =& CRM_Core_Session::singleton( ); 
            $session->setStatus( $status ); 
        }
    }

    /**
     * (Re)set the default callback method
     *
     * @return void
     * @access publiic
     * @static
     */
    public static function setCallback() {
        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK, 
                                array('CRM_Core_Error', 'handle'));
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
    public static function handle($pearError)
    {
        // setup smarty with config, session and template location.
        $template =& CRM_Core_Smarty::singleton( );
        $config   =& CRM_Core_Config::singleton( );

        if ( $config->backtrace ) {
            self::backtrace( );
        }

        // create the error array
        $error = array();
        $error['callback']    = $pearError->getCallback();
        $error['code']        = $pearError->getCode();
        $error['message']     = $pearError->getMessage();
        $error['mode']        = $pearError->getMode();
        $error['debug_info']  = $pearError->getDebugInfo();
        $error['type']        = $pearError->getType();
        $error['user_info']   = $pearError->getUserInfo();
        $error['to_string']   = $pearError->toString();
        if ( mysql_error( ) ) {
            $mysql_error = mysql_error( ) . ', ' . mysql_errno( );
            $template->assign_by_ref( 'mysql_code', $mysql_error );

            // execute a dummy query to clear error stack
            mysql_query( 'select 1' );
        }

        $template->assign_by_ref('error', $error);
        
        $content  = $template->fetch( 'CRM/error.tpl' );
        $content .= CRM_Core_Error::debug( 'error', $error, false );
        echo CRM_Utils_System::theme( 'page', $content );
        exit(1);
    }


    /**
     * Handle errors raised using the PEAR Error Stack.
     *
     * currently the handler just requests the PES framework
     * to push the error to the stack (return value PEAR_ERRORSTACK_PUSH).
     *
     * Note: we can do our own error handling here and return PEAR_ERRORSTACK_IGNORE.
     *
     * Also, if we do not return any value the PEAR_ErrorStack::push() then does the
     * action of PEAR_ERRORSTACK_PUSHANDLOG which displays the errors on the screen,
     * since the logger set for this error stack is 'display' - see CRM_Core_Config::getLog();
     *
     */
    public static function handlePES($pearError)
    {
        return PEAR_ERRORSTACK_PUSH;
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

        $config =& CRM_Core_Config::singleton( );

        if ( $config->fatalErrorHandler &&
             function_exists( $config->fatalErrorHandler ) ) {
            $name = $config->fatalErrorHandler;
            $ret = $name( $vars );
            if ( $ret ) {
                // the call has been successfully handled
                // so we just exit
                exit( CRM_Core_Error::FATAL_ERROR );
            }
        }

        if ( $config->backtrace ) {
            self::backtrace( );
        }

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( $vars );

        print $template->fetch( $config->fatalErrorTemplate );
        exit( CRM_Core_Error::FATAL_ERROR );
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
    static function debug( $name, $variable, $log = true ) {
        $error =& self::singleton( );

        $out = htmlspecialchars(print_r($variable, true));

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
     * @param  bool   should we use print_r ? (else we use var_dump)
     * @param  bool   should we log or return the output
     *
     * @return string the generated output
     *
     * @access public
     *
     * @static
     *
     * @see CRM_Core_Error::debug()
     * @see CRM_Core_Error::debug_log_message()
     */
    static function debug_var($variable_name, &$variable, $print_r=true, $log=true)
    {
        // check if variable is set
        if(!isset($variable)) {
            $out = "\$$variable_name is not set";
        } else {
            if ($print_r) {
                $out = print_r($variable, true);
                $out = "\$$variable_name = $out";
            } else {
                // use var_dump
                ob_start();
                var_dump($variable);
                $dump = ob_get_contents();
                ob_end_clean();
                $out = "\n\$$variable_name = $dump";
            }
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
        $config =& CRM_Core_Config::singleton( );
        //$file_log = Log::singleton('file', $config->uploadDir . 'CRM.LOG');
        $file_log = Log::singleton('file', '/tmp/CRM.LOG');
        $file_log->log("$message\n");
        // $error =& self::singleton( );
        $out = "<p /><code>$message</code>";
        if ($log) {
            //echo $out;
        }
        return $out;
    }

    static function backtrace( $msg = 'backTrace' ) {
        $backTrace = debug_backtrace( );
        
        $msgs = array( );
        foreach ( $backTrace as $trace ) {
            $msgs[] = implode( ', ', array( $trace['file'], $trace['function'], $trace['line'] ) );
        }

        $message = implode( "\n", $msgs );
        CRM_Core_Error::debug( $msg, $message );
    }

    static function createError( $message, $code = 8000, $level = 'Fatal', $params = null ) {
        $error =& CRM_Core_Error::singleton( );
        $error->push( $code, $level, array( $params ), $message );
        return $error;
    }

    /**
     * Set a status message in the session, then bounce back to the referrer.
     *
     * @param string $status        The status message to set
     * @return void
     * @access public
     * @static
     */
    public static function statusBounce($status) {
        $session =& CRM_Core_Session::singleton();
        $redirect = $session->readUserContext();
        $session->setStatus($status);
        CRM_Utils_System::redirect($redirect);
    }

}

PEAR_ErrorStack::singleton('CRM', false, null, 'CRM_Core_Error');


     


?>
