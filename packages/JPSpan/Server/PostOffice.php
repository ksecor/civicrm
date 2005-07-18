<?php
/**
* @package JPSpan
* @subpackage Server
* @version $Id: PostOffice.php,v 1.11 2004/11/23 13:49:56 harryf Exp $
*/
//--------------------------------------------------------------------------------
/**
* Define
*/
if ( !defined('JPSPAN') ) {
    define ('JPSPAN',dirname(__FILE__).'/../');
}
/**
* Include
*/
//require_once JPSPAN . 'Server.php';
require_once 'packages/JPSpan/Server.php';
//--------------------------------------------------------------------------------

/**
* Class and method name passed in the URL with params passed
* as url-encoded POST data. Urls like
* http://localhost/server.php/Class/Method
* @package JPSpan
* @subpackage Server
* @public
*/
class JPSpan_Server_PostOffice extends JPSpan_Server {

    /**
    * Name of user defined handler that was called
    * @param string
    * @access private
    */
    var $calledClass = NULL;
    
    /**
    * Name of method in handler
    * @param string
    * @access private
    */
    var $calledMethod = NULL;
    
    /**
    * Request encoding to use (e.g. xml or php)
    * @var string
    * @access public
    */
    var $RequestEncoding = 'xml';

    /**
    * @access public
    */
    function JPSpan_Server_PostOffice() {
        parent::JPSpan_Server();
    }
    
    /**
    * Serve a request
    * @param boolean send headers
    * @return boolean FALSE if failed (invalid request - see errors)
    * @access public
    */
    function serve($sendHeaders = TRUE) {
        CRM_Core_Error::debug_log_message('entering  serve');
        //require_once JPSPAN . 'Monitor.php';
        require_once 'packages/JPSpan/Monitor.php';

        CRM_Core_Error::debug_log_message('========== 1 =============');

        $M = & JPSpan_Monitor::instance();
        
        $this->calledClass = NULL;
        $this->calledMethod = NULL;
        
        if ( $this->resolveCall() ) {
        
            CRM_Core_Error::debug_log_message('========== 2 =============');
        
            $M->setRequestInfo('class',$this->calledClass);
            $M->setRequestInfo('method',$this->calledMethod);
            
            if ( FALSE !== ($Handler = & $this->getHandler($this->calledClass) ) ) {

                CRM_Core_Error::debug_log_message('========== 3 =============');
            
                $args = array();
                $M->setRequestInfo('args',$args);
                
                CRM_Core_Error::debug_log_message('========== 3.1 =============');

                if ( $this->getArgs($args) ) {
                
                    CRM_Core_Error::debug_log_message('========== 4 =============');

                    $M->setRequestInfo('args',$args);
                    
                    CRM_Core_Error::debug_log_message('========'.get_class($Handler). '========='.   $this->calledMethod  .'==========');

                    $response = call_user_func_array(
                        array(
                            & $Handler,
                            $this->calledMethod
                        ),
                        $args
                    );

                } else {
                
                    CRM_Core_Error::debug_log_message('========== 5 =============');

                    $response = call_user_func(
                        array(
                            & $Handler,
                            $this->calledMethod
                        )
                    );
                }

                CRM_Core_Error::debug_var('response', $response, 1);
                
                CRM_Core_Error::debug_log_message('========== 6 =============');

                //require_once JPSPAN . 'Serializer.php';
                require_once 'packages/JPSpan/Serializer.php';

                CRM_Core_Error::debug_log_message('========== 7 =============');

                $M->setResponseInfo('payload',$response);
                $M->announceSuccess();
                
                CRM_Core_Error::debug_log_message('========== 8 =============');
                
                $response = JPSpan_Serializer::serialize($response);
                
                if ( $sendHeaders ) {
                    header('Content-Length: '.strlen($response));
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
                    header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . 'GMT'); 
                    header('Cache-Control: no-cache, must-revalidate'); 
                    header('Pragma: no-cache');
                }
                
                echo $response;
                
                return TRUE;
                
            } else {
            
                trigger_error('Invalid handle for: '.$this->calledClass,E_USER_ERROR);
                return FALSE;
                
            }
            
        }

        CRM_Core_Error::debug_log_message('leaving  serve');
        return FALSE;


    }

    /**
    * Resolve the call - identify the handler class and method and store
    * locally
    * @return boolean FALSE if failed (invalid request - see errors)
    * @access private
    */
    function resolveCall() {
        CRM_Core_Error::debug_log_message('========== entering resolve call =============');
        
        $uriPath = explode('/',JPSpan_Server::getUriPath());
        
        //CRM_Core_Error::debug_var('uriPath', $uriPath, 1);   
        
        if ( count($uriPath) != 2 ) {
            trigger_error('Invalid call syntax',E_USER_ERROR);
            return FALSE;
        }
        
        if ( preg_match('/^[a-z]+[0-9a-z_]*$/',$uriPath[0]) != 1 ) {
            trigger_error('Invalid handler name: '.$uriPath[0],E_USER_ERROR);
            return FALSE;
        }
        
        if ( preg_match('/^[a-z]+[0-9a-z_]*$/',$uriPath[1]) != 1 ) {
            trigger_error('Invalid handler method: '.$uriPath[1],E_USER_ERROR);
            return FALSE;
        }
        
        if ( !array_key_exists($uriPath[0],$this->descriptions) ) {
            trigger_error('Unknown handler: '.$uriPath[0],E_USER_ERROR);
            return FALSE;
        }
        
        if ( !in_array($uriPath[1],$this->descriptions[$uriPath[0]]->methods) ) {
            trigger_error('Unknown handler method: '.$uriPath[1],E_USER_ERROR);
            return FALSE;
        }
        
        $this->calledClass = $uriPath[0];
        $this->calledMethod = $uriPath[1];
        
        //$this->calledClass = 'crm_contact_form_statecountryserver';
        //$this->calledMethod = 'getword';


        return TRUE;
        
    }
    
    /**
    * Populate the args array if there are any
    * @param array args (reference)
    * @return boolean TRUE if request had args
    * @access private
    */
    function getArgs(& $args) {

        CRM_Core_Error::debug_log_message(' entering getArgs');
        
        //require_once JPSPAN . 'RequestData.php';
        require_once 'packages/JPSpan/RequestData.php';

        CRM_Core_Error::debug_var('request', $this->RequestEncoding);

        if ( $this->RequestEncoding == 'php' ) {

            CRM_Core_Error::debug_log_message("============= 111 ==============");

            $args = JPSpan_RequestData_Post::fetch($this->RequestEncoding);

            CRM_Core_Error::debug_log_message("============= 11111111 ==============");

        } else {

            CRM_Core_Error::debug_log_message("============= 222 ==============");

            $args = JPSpan_RequestData_RawPost::fetch($this->RequestEncoding);

            CRM_Core_Error::debug_log_message("============= 222222222 ==============");

        }
        
        //$args[0] = 's';
        CRM_Core_Error::debug_var('args', $args, 1);


        if ( is_array($args) ) {
            return TRUE;
        }
        
        CRM_Core_Error::debug_log_message(' leaving getArgs');
        return FALSE;
    }
    
    /**
    * Get the Javascript client generator
    * @return JPSpan_Generator
    * @access public
    */
    function & getGenerator() {

        //require_once JPSPAN . 'Generator.php';
        require_once 'packages/JPSpan/Generator.php';

        $G = & new JPSpan_Generator();
        $G->init(
            new JPSpan_PostOffice_Generator(),
            $this->descriptions,
            $this->serverUrl,
            $this->RequestEncoding
            );
        return $G;
    }
}

//--------------------------------------------------------------------------------
/**
* Generator for the JPSpan_Server_PostOffice
* @todo Much refactoring need to make code generation "pluggable"
* @see JPSpan_Server_PostOffice
* @package JPSpan
* @subpackage Server
* @access public
*/
class JPSpan_PostOffice_Generator {

    /**
    * @var array list of JPSpan_HandleDescription objects
    * @access public
    */
    var $descriptions;
    
    /**
    * @var string URL or server
    * @access public
    */
    var $serverUrl;
    
    /**
    * How requests should be encoded
    * @var string request encoding
    * @access public
    */
    var $RequestEncoding;
    
    /**
    * Invokes code generator
    * @param JPSpan_CodeWriter
    * @return void
    * @access public
    */
    function generate(& $Code) {

        $this->generateScriptHeader($Code);
        foreach ( array_keys($this->descriptions) as $key ) {
            $this->generateHandleClient($Code, $this->descriptions[$key]);
        }
    }
    
    /**
    * Generate the starting includes section of the script
    * @param JPSpan_CodeWriter
    * @return void
    * @access private
    */
    function generateScriptHeader(& $Code) {
        ob_start();
?>
/**@
* include 'remoteobject.js';
<?php
if ( $this->RequestEncoding == 'xml' ) {
?>
* include 'request/rawpost.js';
* include 'encode/xml.js';
<?php
} else {
?>
* include 'request/post.js';
* include 'encode/php.js';
<?php
}
?>
*/
<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }
    
    /**
    * Generate code for a single description (a single PHP class)
    * @param JPSpan_CodeWriter
    * @param JPSpan_HandleDescription
    * @return void
    * @access private
    */
    function generateHandleClient(& $Code, & $Description) {
        ob_start();
?>

function <?php echo $Description->Class; ?>() {
    
    var oParent = new JPSpan_RemoteObject();
    
    if ( arguments[0] ) {
        oParent.Async(arguments[0]);
    }
    
    oParent.__serverurl = '<?php 
        echo $this->serverUrl . '/' . $Description->Class; ?>';
    
    oParent.__remoteClass = '<?php echo $Description->Class; ?>';
    
<?php
if ( $this->RequestEncoding == 'xml' ) {
?>
    oParent.__request = new JPSpan_Request_RawPost(new JPSpan_Encode_Xml());
<?php
} else {
?>
    oParent.__request = new JPSpan_Request_Post(new JPSpan_Encode_PHP());
<?php
}

foreach ( $Description->methods as $method ) {
?>
    
    // @access public
    oParent.<?php echo $method; ?> = function() {
        var url = this.__serverurl+'/<?php echo $method; ?>/';
        return this.__call(url,arguments,'<?php echo $method; ?>');
    };
<?php
}
?>
    
    return oParent;
}

<?php
        $Code->append(ob_get_contents());
        ob_end_clean();
    }
}


