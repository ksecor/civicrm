<?php
/**
* This is a remote script to call from Javascript
*/

//-----------------------------------------------------------------------------------
class CRM_Contact_Server_StateCountryServer  
{
    
    function getWord($fragment='') 
    {
        
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
?>
