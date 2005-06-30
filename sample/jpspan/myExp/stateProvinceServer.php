<?php
// $Id: autocomplete_server.php,v 1.5 2004/11/23 14:10:56 harryf Exp $
/**
* This is a remote script to call from Javascript
*/
require_once 'debug.php';
define ('JPSPAN_ERROR_DEBUG',TRUE);
require_once '../JPSpan.php';
require_once JPSPAN . 'Server/PostOffice.php';

//-----------------------------------------------------------------------------------
class Autocomplete {

    function getWord($fragment='') {
   
        /*
        $words = file('../examples/data/countrylist.vars');
   
        $fraglen = strlen($fragment);
        for ( $i = $fraglen; $i > 0; $i-- ) {
            
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i',$words);
                    
            //my_print_r($matches, 'matches');
            my_print_r($words, 'words');
            
            if ( count($matches) > 0 ) {
                return array_shift($matches);
            }
        }
        */  

        
        $fraglen = strlen($fragment);
        
        //get the list of states
        $connect = mysql_connect('localhost', 'civicrm', 'Mt!Everest');
        mysql_select_db('civicrm');
        
        $strSql = "SELECT id, name FROM crm_state_province";  
        
        if (!$rst = mysql_query($strSql)) {
            echo $strSql."<br>".mysql_error();
        } else {
            if (mysql_num_rows($rst)) {
                while ($adata = mysql_fetch_assoc($rst)) {
                    $states[$adata['id']] = $adata['name'];
                }
            }
        }
        
        
        for ( $i = $fraglen; $i > 0; $i-- ) {
            
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i',$states);
            
            if ( count($matches) > 0 ) {
                $id = key($matches);
                $value = current($matches);
                $showState[$id] = $value;
                return $showState;
            }
        }

        return '';
    }

    function getCountry($stateId) {

        unset($matches);
        
        $connect = mysql_connect('localhost', 'civicrm', 'Mt!Everest');
        mysql_select_db('civicrm');
        
        $strSql = "SELECT crm_country.id as crm_country_id, crm_country.name as crm_country_name 
                   FROM crm_country , crm_state_province 
                   WHERE crm_state_province.country_id = crm_country.id
                      AND crm_state_province.id =".$stateId;  
      
        if (!$rst = mysql_query($strSql)) {
            echo $strSql."<br>".mysql_error();
        } else {
            if (mysql_num_rows($rst)) {
                while ($adata = mysql_fetch_assoc($rst)) {
                    $matches[$adata['crm_country_id']] = $adata['crm_country_name'];
                }
                $id = key($matches);
                $value = current($matches);
                $showCountry[$id] = $value;
                return $showCountry;
            }
        }
        
        return '';

    }

}

$S = & new JPSpan_Server_PostOffice();
$S->addHandler(new Autocomplete());

//$S->displayClient();

// Include error handler - PHP errors, warnings and notices serialized to JS
//require_once JPSPAN . 'ErrorHandler.php';
//$S->serve();


//-----------------------------------------------------------------------------------
// Generates the Javascript client by adding ?client to the server URL
//-----------------------------------------------------------------------------------
if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {
    // Compress the Javascript
    // define('JPSPAN_INCLUDE_COMPRESS',TRUE);

    $S->displayClient();
     
//-----------------------------------------------------------------------------------
} else {
           
    // Include error handler - PHP errors, warnings and notices serialized to JS
    require_once JPSPAN . 'ErrorHandler.php';
    $S->serve();

}

?>
