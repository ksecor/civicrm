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
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * file contains functions used in civicrm configuration
 * 
 */
class CRM_Core_BAO_Setting 
{
    /**
     * Function to add civicrm settings
     *
     * @params array $params associated array of civicrm variables
     *
     * @return null
     * @static
     */
    static function add(&$params) 
    {
        CRM_Core_BAO_Setting::fixParams($params);

        require_once "CRM/Core/DAO/Domain.php";
        $domain =& new CRM_Core_DAO_Domain();
        $domain->id = CRM_Core_Config::domainID( );

        $domain->find(true);
        if ($domain->config_backend) {
            $values = unserialize($domain->config_backend);
            CRM_Core_BAO_Setting::formatParams($params, $values);
        }
        
        $domain->config_backend = serialize($params);
        $domain->save();
    }

    /**
     * Function to fix submitted civicrm setting variables
     *
     * @params array $params associated array of civicrm variables
     *
     * @return null
     * @static
     */
    static function fixParams(&$params) 
    {
        $country = array();
        if ( $params['enableComponents'] ) {
            $params['enableComponents'] = implode(',', $params['enableComponents']);
        }
        if ( $params['countryLimit'] ) {
//             foreach( $params['countryLimit'] as $key=>$value ) {
//                 $country[] = CRM_Core_PseudoConstant::countryIsoCode($value );
//             }
            $params['countryLimit'] = implode(',',  $params['countryLimit']);
        }
        if ( $params['defaultContactCountry'] ) {
            //$defaultCountry = CRM_Core_PseudoConstant::countryIsoCode($params['defaultContactCountry']);
            $params['defaultContactCountry'] = $params['defaultContactCountry'];
        }
    }

    /**
     * Function to format the array containing before inserting in db
     *
     * @param  array $params associated array of civicrm variables(submitted)
     * @param  array $values associated array of civicrm variables stored in db
     *
     * @return null
     * @static
     */
    static function formatParams(&$params, &$values) 
    {
        foreach ($params as $key => $val) {
            if ( array_key_exists($key, $values)) {
                unset($values[$key]);
            }
        }

        $params = array_merge($params, $values);
    }

    /**
     * Function to retrieve the settings values from db
     *
     * @return array $defaults  
     * @static
     */
    static function retrieve(&$defaults) {

        require_once "CRM/Core/DAO/Domain.php";
        $domain =& new CRM_Core_DAO_Domain();
        $domain->id = CRM_Core_Config::domainID( );

        $domain->find(true);
        if ($domain->config_backend) {
            $defaults = unserialize($domain->config_backend);
        }
    }

}

?>