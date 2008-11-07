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

require_once 'CRM/Core/Page/AJAX.php';

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX_Location extends CRM_Core_Page_AJAX 
{
    /**
     * Function to build state province combo box
     */
    function state( &$config ) 
    {
        require_once 'CRM/Utils/Type.php';
        $countryName  = $stateName = null;
        $elements = array( );
        $countryClause = " 1 ";
        if ( CRM_Utils_Array::value( 'node', $_GET ) ) {
            $countryId     = CRM_Utils_Type::escape( $_GET['node'], 'String');
            $countryClause = " civicrm_state_province.country_id = {$countryId}";
        } 

        if ( isset( $_GET['name'] ) ) {
            $stateName    = trim (CRM_Utils_Type::escape( $_GET['name']   , 'String') );
        }

        $stateId = null;
        if ( isset( $_GET['id'] ) ) {
            $stateId = CRM_Utils_Type::escape( $_GET['id'], 'Positive', false  );
        }
        
        $validValue = true;
        if ( !$stateName && !$stateId ) {
            $validValue = false;
        }

        if ( $validValue ) {
            $stateClause = " 1 ";
            if ( !$stateId ) {
                $stateName = str_replace( '*', '%', $stateName );        
                $stateClause = " civicrm_state_province.name LIKE '$stateName%' ";
            } else {
                $stateClause = " civicrm_state_province.id = {$stateId} ";
            }
            
            $query = "
SELECT civicrm_state_province.name name, civicrm_state_province.id id
  FROM civicrm_state_province
WHERE {$countryClause}
    AND {$stateClause}
ORDER BY name";

            $dao = CRM_Core_DAO::executeQuery( $query );
            
            $count = 0;
            
            while ( $dao->fetch( ) && $count < 5 ) {
                $elements[] = array( 'name'  => ts($dao->name),
                                     'value' => $dao->id );
                $count++;
            }
        }

        if ( empty( $elements) ) {
            if ( !$stateName && isset( $_GET['id'] )) {
                if ( $stateId ) {
                    $stateProvinces  = CRM_Core_PseudoConstant::stateProvince( false, false );
                    $stateName =  $stateProvinces[$stateId];
                    $stateValue = $stateId;
                } else {
                    $stateName = $stateValue = $_GET['id'];
                }
            } else if ( !is_numeric( $stateName ) )  {
                $stateValue = $stateName;
            }
            
            $elements[] = array( 'name'  => trim($stateName, "%"),
                                 'value' => trim($stateValue, "%") 
                                 );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

    /**
     * Function to build country combo box
     */
    function country( &$config ) 
    {
        //get the country limit and restrict the combo select options
        $limitCodes = $config->countryLimit( );
        if ( ! is_array( $limitCodes ) ) {
            $limitCodes = array( $config->countryLimit => 1);
        }
        
        $limitCodes = array_intersect( CRM_Core_PseudoConstant::countryIsoCode(), $limitCodes);
        if ( count($limitCodes) ) {
            $whereClause = " iso_code IN ('" . implode("', '", $limitCodes) . "')";
        } else {
            $whereClause = " 1";
        }

        $elements = array( );
        require_once 'CRM/Utils/Type.php';
        $name      = CRM_Utils_Array::value( 'name', $_GET, '' );
        $name      = CRM_Utils_Type::escape( $name, 'String'  );

        $countryId = null;
        if ( isset( $_GET['id'] ) ) {
            $countryId = CRM_Utils_Type::escape( $_GET['id'], 'Positive', false );
        }

        //temporary fix to handle locales other than default US,
        // CRM-2653
        if ( !$countryId && $name && $config->lcMessages != 'en_US') {
            $countries = CRM_Core_PseudoConstant::country();
            
            // get the country name in en_US, since db has this locale
            $countryName = array_search( $name, $countries );
            
            if ( $countryName ) {
                $countryId = $countryName;
            }
        }

        $validValue = true;
        if ( !$name && !$countryId ) {
            $validValue = false;
        }

        if ( $validValue ) {
            if ( !$countryId ) {
                $name = str_replace( '*', '%', $name );
                $countryClause = " civicrm_country.name LIKE '$name%' ";
            } else {
                $countryClause = " civicrm_country.id = {$countryId} ";
            }
            
            $query = "
SELECT id, name
  FROM civicrm_country
 WHERE {$countryClause}
   AND {$whereClause} 
ORDER BY name";

            $dao = CRM_Core_DAO::executeQuery( $query );
            
            $count = 0;
            while ( $dao->fetch( ) && $count < 5 ) {
                $elements[] = array( 'name'  => ts($dao->name),
                                     'value' => $dao->id );
                $count++;
            }
        }
        
        if ( empty( $elements) ) {
            if ( isset( $_GET['id'] ) ) {
                $name = $_GET['id'];
            }

            $elements[] = array( 'name'  => trim($name, "%"),
                                 'value' => trim($name, "%") 
                                 );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value');
    }

}
