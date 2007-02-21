<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2007
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

        // unset any of the variables we read from file that should not be stored in the database
        // the username and certpath are stored flat with _test and _live
        // check CRM-1470
        $skipVars = array( 'dsn', 'templateCompileDir', 'userFrameworkBaseURL',
                           'paymentUsername', 'paymentCertPath', 'qfKey',
                           'gettextResourceDir' );
        foreach ( $skipVars as $var ) {
            unset( $params[$var] );
        }
                           
        $domain->config_backend = serialize($params);
        $domain->save();
    }

    /**
     * Function to fix civicrm setting variables
     *
     * @params array $params associated array of civicrm variables
     *
     * @return null
     * @static
     */
    static function fixParams(&$params) 
    {
        // in our old civicrm.settings.php we were using ISO code for country and
        // province limit, now we have changed it to use ids

        $countryIsoCodes = CRM_Core_PseudoConstant::countryIsoCode( );
        
        $specialArray = array('countryLimit', 'provinceLimit');
        $paymentArray = array('paymentCertPath', 'paymentUsername');
        
        foreach($params as $key => $value) {
            if ( in_array($key, $specialArray) && is_array($value) ) {
                foreach( $value as $k => $val ) {
                    if ( !is_numeric($val) ) {
                        $params[$key][$k] = array_search($val, $countryIsoCodes); 
                    }
                }
            } else if ( $key == 'defaultContactCountry' ) {
                if ( !is_numeric($value) ) {
                    $params[$key] =  array_search($value, $countryIsoCodes); 
                }
            } else if (in_array($key, $paymentArray)) {
                $params[$key . "_test"] = $value['test'];
                $params[$key . "_live"] = $value['live'];
            }
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