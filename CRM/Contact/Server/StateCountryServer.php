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

//require_once 'CRM/Core/Error.php'; 
//require_once 'CRM/Core/DAO.php'; 
require_once 'CRM/Core/PseudoConstant.php'; 

class CRM_Contact_Server_StateCountryServer  
{
    
    function getWord($fragment='') 
    {
        $fraglen = strlen($fragment);
        
        for ( $i = $fraglen; $i > 0; $i-- ) {
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i', CRM_Core_PseudoConstant::stateProvince());
            
            if ( count($matches) > 0 ) {
                $id = key($matches);
                $value = current($matches);
                $showState[$id] = $value;
                return $showState;
            }
        }
        
        return '';
    }

    function getCountry($stateProvinceId) {
        unset($matches);
        $queryString = "SELECT civicrm_country.id as civicrm_country_id, civicrm_country.name as civicrm_country_name 
                        FROM civicrm_country , civicrm_state_province 
                        WHERE civicrm_state_province.country_id = civicrm_country.id
                          AND civicrm_state_province.id =".$stateProvinceId;  

        $DAOobj =& new CRM_Core_DAO();
        
        $DAOobj->query($queryString); 

        if ($DAOobj->fetch()) { 
            $matches[$DAOobj->civicrm_country_id] = $DAOobj->civicrm_country_name;
               
            $id = key($matches);
            $value = current($matches);
            $showCountry[$id] = $value;
            return $showCountry;
        }
        return '';
    }

}
?>
