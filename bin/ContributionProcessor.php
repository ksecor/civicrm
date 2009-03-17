<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class CiviContributeProcessor {

    static function paypal( $start, $end ) {
        static $userName  = 'paypal_api1.openngo.org';
        static $password  = '7YJ7JYCJ4QEMWQS5';
        static $signature = 'AAivyp-lGZaDNGJEtfXyK475vPAZAgXeC-Cw1KFCkIztxWkYwI5MlnTH';
        static $url       = '';

        $keyArgs = array( 'user'      => $userName,
                          'pwd'       => $password,
                          'signature' => $signature, 
                          'version'   => 3.0,
                          );

        $args =  $keyArgs;
        $args += array( 'method'    => 'TransactionSearch',
                        'startdate' => $start,
                        'enddate'   => $end );

        require_once 'CRM/Core/Payment/PayPalImpl.php';
        $result = CRM_Core_Payment_PayPalImpl::invokeAPI( $args,
                                                          'https://api-3t.sandbox.paypal.com/nvp' );

        $keyArgs['method'] = 'GetTransactionDetails';
        foreach ( $result as $name => $value ) {
            if ( substr( $name, 0, 15 ) == 'l_transactionid' ) {
                $keyArgs['transactionid'] = $value;
                $result = CRM_Core_Payment_PayPalImpl::invokeAPI( $keyArgs,
                                                                  'https://api-3t.sandbox.paypal.com/nvp' );
                CRM_Core_Error::debug( $result );
                
            }
        }

    }

    static function process( ) {
        require_once 'CRM/Utils/Request.php';

        $type = CRM_Utils_Request::retrieve( 'type', 'String', CRM_Core_DAO::$_nullObject, false, 'csv' );
        $type = strtolower( $type );

        
        switch ( $type ) {
        case 'paypal':
        case 'google':
            $start = CRM_Utils_Request::retrieve( 'start', 'String', CRM_Core_DAO::$_nullObject, false,
                                                  date( 'Y-m-d', time( ) - 31 * 24 * 60 * 60 ) . 'T00:00:00.00Z' );
            $end   = CRM_Utils_Request::retrieve( 'end', 'String', CRM_Core_DAO::$_nullObject, false,
                                                  date( 'Y-m-d' ) . 'T23:59:00.00Z' );
            return self::$type( $start, $end );

        case 'csv':
            return self::csv( );
        }
    }

}

// bootstrap the environment and run the processor
session_start();
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton();

// CRM_Utils_System::authenticateScript(true);

require_once 'CRM/Core/Lock.php';
$lock = new CRM_Core_Lock('CiviContributeProcessor');

if ($lock->isAcquired()) {
    // try to unset any time limits
    if (!ini_get('safe_mode')) set_time_limit(0);

    CiviContributeProcessor::process( );
} else {
    throw new Exception('Could not acquire lock, another CiviMailProcessor process is running');
}

$lock->release();
