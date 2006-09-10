<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

//require_once 'CRM/Core/Error.php'; 
//require_once 'CRM/Core/DAO.php'; 
require_once 'CRM/Core/PseudoConstant.php'; 

/**
 * This class is for state country widget using JPSpan.
 *
 */
class CRM_Contact_Server_StateCountry  
{
    /**
     * This function is to get the state name based on the search criteria
     * @param string $fragment this is the search string
     * @param integer $countryId country id 
     *
     * @return state id / state name depending on search criteria
     * @access public
     */
    function getState($fragment='', $countryId = 0) 
    {
        $fraglen = strlen($fragment);
        
        if (!$countryId) { 
            $states = CRM_Core_PseudoConstant::stateProvince();
        } else {
          
            $queryString = "SELECT civicrm_state_province.id as civicrm_state_province_id  
                            FROM civicrm_country , civicrm_state_province 
                            WHERE civicrm_state_province.country_id = civicrm_country.id
                              AND civicrm_country.id = " . CRM_Utils_Type::escape($countryId, 'Integer') . "
                              AND civicrm_state_province.name ='" . CRM_Utils_Type::escape($fragment, 'String') . "'";  

            $DAOobj =& new CRM_Core_DAO();
            
            $DAOobj->query($queryString); 
            
            while ($DAOobj->fetch()) {
              return $DAOobj->civicrm_state_province_id;
            }
        }

        for ( $i = $fraglen; $i > 0; $i-- ) {
            $matches = preg_grep('/^'.substr($fragment,0,$i).'/i', $states);
            
            if ( count($matches) > 0 ) {
                $id = key($matches);
                $value = current($matches);
                $showState[$id] = $value;
                return $showState;
            }
        }
        return '';
    }

    /**
     * This is the function to get the list of countries 
     * @param string $stateProvince stateProvince name
     *
     * @return array of countries 
     * @access public
     */
    function getCountry($stateProvince) {
        unset($matches);
        $queryString = "SELECT civicrm_country.id as civicrm_country_id, civicrm_country.name as civicrm_country_name 
                        FROM civicrm_country , civicrm_state_province 
                        WHERE civicrm_state_province.country_id = civicrm_country.id
                          AND civicrm_state_province.name ='" . CRM_Utils_Type::escape($stateProvince, 'String') . "'";  

        $DAOobj =& new CRM_Core_DAO();
        
        $DAOobj->query($queryString); 

        while ($DAOobj->fetch()) { 
            $matches[$DAOobj->civicrm_country_id] = $DAOobj->civicrm_country_name;
        }
        return $matches;
    }
}
?>
