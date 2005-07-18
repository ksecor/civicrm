<?php
/**
* This is a remote script to call from Javascript
*/

require_once 'CRM/Core/Page.php';
require_once 'CRM/Contact/Server/StateCountryServer.php';

define ('JPSPAN_ERROR_DEBUG',TRUE);
require_once 'JPSpan.php';

//require_once JPSPAN . 'Server/PostOffice.php';
require_once 'packages/JPSpan/Server/PostOffice.php';

class CRM_Contact_Page_StateCountryServer extends CRM_Core_Page { 

    function run () 
    {
          
        CRM_Core_Error::le_function();
        
        $S = & new JPSpan_Server_PostOffice();
        $S->addHandler(new CRM_Contact_Server_StateCountryServer());
        
        //-----------------------------------------------------------------------------------
        // Generates the Javascript client by adding ?client to the server URL
        //-----------------------------------------------------------------------------------
        if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {
            // Compress the Javascript
            // define('JPSPAN_INCLUDE_COMPRESS',TRUE);
            
            $S->displayClient();
            
            //-----------------------------------------------------------------------------------
        } else {
            
            CRM_Core_Error::debug_log_message('breakpoint 20');
            
            // Include error handler - PHP errors, warnings and notices serialized to JS
            require_once 'packages/JPSpan/ErrorHandler.php';
            
            CRM_Core_Error::debug_log_message('breakpoint 30');
            
            $S->serve();
            
            CRM_Core_Error::debug_log_message('breakpoint 40');
        }

        CRM_Core_Error::le_function();

    }
}

?>
