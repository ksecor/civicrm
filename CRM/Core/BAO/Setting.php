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
 *
 * @package CRM
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
        $domain->find(true);
        if ($domain->config_backend) {
            $values = unserialize($domain->config_backend);
            CRM_Core_BAO_Setting::formatParams($params, $values);
        }

        // unset any of the variables we read from file that should not be stored in the database
        // the username and certpath are stored flat with _test and _live
        // check CRM-1470
        $skipVars = array( 'dsn', 'templateCompileDir',
                           'userFrameworkDSN', 
                           'userFrameworkBaseURL', 'userFrameworkClass', 'userHookClass',
                           'userPermissionClass', 'userFrameworkURLVar',
                           'qfKey', 'gettextResourceDir', 'cleanURL' );
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
        if ( empty( $params ) ||
             ! is_array( $params ) ) {
            $params = $values;
        } else {
            foreach ($params as $key => $val) {
                if ( array_key_exists($key, $values)) {
                    unset($values[$key]);
                }
            }
            $params = array_merge($params, $values);
        }
    }

    /**
     * Function to retrieve the settings values from db
     *
     * @return array $defaults  
     * @static
     */
    static function retrieve(&$defaults) 
    {
        require_once "CRM/Core/DAO/Domain.php";
        $domain =& new CRM_Core_DAO_Domain();
        $domain->selectAdd( );
        if ( CRM_Utils_Array::value( 'q', $_GET ) == 'civicrm/upgrade' ) {
            $domain->selectAdd( 'config_backend' );
        } else {
            $domain->selectAdd( 'config_backend, locales' );
        }
        
        $domain->find(true);
        if ($domain->config_backend) {
            $defaults   = unserialize($domain->config_backend);

            // calculate month var
            $defaults['dateformatMonthVar'] = 
                strstr($defaults['dateformatQfDate'], '%m') ? 'm' : (strstr($defaults['dateformatQfDate'], '%b') ? 'M' : (strstr($defaults['dateformatQfDate'], '%B') ? 'F' : null)); 
            
            //calculate month var for Date Time
            $defaults['datetimeformatMonthVar'] = 
                strstr($defaults['dateformatQfDatetime'], '%m') ? 'm' : (strstr($defaults['dateformatQfDatetime'], '%b') ? 'M' : (strstr($defaults['dateformatQfDatetime'], '%B') ? 'F' : null));
            //calculate hour var for Date Time 
            $defaults['datetimeformatHourVar'] =  strstr($defaults['dateformatQfDatetime'], '%I') ?'h' : (strstr($defaults['dateformatQfDatetime'], '%l') ? 'g' : null);

            // set proper monetary formatting, falling back to en_US and C (CRM-2782)
            setlocale(LC_MONETARY, $defaults['lcMonetary'].'.utf8', $defaults['lcMonetary'], 'en_US.utf8', 'en_US', 'C');

            $skipVars = array( 'dsn', 'templateCompileDir',
                               'userFrameworkDSN', 
                               'userFrameworkBaseURL', 'userFrameworkClass', 'userHookClass',
                               'userPermissionClass', 'userFrameworkURLVar',
                               'qfKey', 'gettextResourceDir', 'cleanURL' );
            foreach ( $skipVars as $skip ) {
                if ( array_key_exists( $skip, $defaults ) ) {
                    unset( $defaults[$skip] );
                }
            }
            
            // since language field won't be present before upgrade.
            if ( CRM_Utils_Array::value( 'q', $_GET ) == 'civicrm/upgrade' ) {
                return;
            }

            // are we in a multi-language setup?
            $multiLang = $domain->locales ? true : false;

            // set the current language
            $lcMessages = null;

            // on multi-lang sites based on request and civicrm_uf_match
            if ($multiLang) {
                require_once 'CRM/Core/DAO/UFMatch.php';
                $session =& CRM_Core_Session::singleton();
                $ufm =& new CRM_Core_DAO_UFMatch();
                $ufm->contact_id = $session->get('userID');

                require_once 'CRM/Utils/Request.php';
                $lcMessages = CRM_Utils_Request::retrieve('lcMessages', 'String', $this);
                if (isset($defaults['languageLimit']) and in_array($lcMessages, $defaults['languageLimit'])) {
                    if ($ufm->find(true)) {
                        $ufm->language = $lcMessages;
                        $ufm->save();
                    }
                } else {
                    if ($ufm->find(true) and isset($defaults['languageLimit']) and in_array($ufm->language, $defaults['languageLimit'])) {
                        $lcMessages = $ufm->language;
                    }
                }
            }

            // if a single-lang site or the above didn't yield a result, use default
            if ($lcMessages === null) {
                $lcMessages = $defaults['lcMessages'];
            }

            // set suffix for table names - use views if more than one language
            global $dbLocale;
            $dbLocale = $multiLang ? "_{$lcMessages}" : '';

            // actually set the language
            $defaults['lcMessages'] = $lcMessages;
        }
    }
}


