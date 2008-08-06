<?php

/*
 * Copyright (C) 2007
 * Licensed to CiviCRM under the Academic Free License version 3.0.
 *
 * Written and contributed by Ideal Solution, LLC (http://www.idealso.com)
 *
 */

/**
 *
 * @package CRM
 * @author Marshal Newrock <marshal@idealso.com>
 * $Id$
 */

/* NOTE:
 * When looking up response codes in the Authorize.Net API, they
 * begin at one, so always delete one from the "Position in Response"
 */

require_once 'CRM/Core/Payment.php';

class CRM_Core_Payment_Dummy extends CRM_Core_Payment {
    const
        CHARSET = 'iso-8859-1';

    static protected $_mode = null;

    static protected $_params = array();
    
    /**
     * Constructor
     *
     * @param string $mode the mode of operation: live or test
     *
     * @return void
     */
    function __construct( $mode, &$paymentProcessor ) {
        $this->_mode = $mode;
        $this->_paymentProcessor = $paymentProcessor;
    }

    /**
     * Submit a payment using Advanced Integration Method
     *
     * @param  array $params assoc array of input parameters for this transaction
     * @return array the result in a nice formatted array (or an error object)
     * @public
     */
    function doDirectPayment ( &$params ) {

        // test mode always returns trxn_id = 0
        if ( $this->_mode == 'test' ) {
            $query = "SELECT MAX(trxn_id) FROM civicrm_contribution WHERE trxn_id LIKE 'test%'";
            $p = array( );
            $trxn_id = strval( CRM_Core_Dao::singleValueQuery( $query, $p ) );
            $trxn_id = str_replace( 'test', '', $trxn_id );
            $trxn_id = intval($trxn_id) + 1;
            $params['trxn_id'] = sprintf('test%08d', $trxn_id);
        }
        else {
            CRM_Core_Error::fatal( ts('Dummy payment processor cannot be used for live transactions.') );
        }
        //$params['gross_amount'] = $response_fields[9];
        return $params;

    }

    function &error ( $errorCode = null, $errorMessage = null ) {
        $e =& CRM_Core_Error::singleton( );
        if ( $errorCode ) {
            $e->push( $errorCode, 0, null, $errorMessage );
        }
        else {
            $e->push( 9001, 0, null, 'Unknown System Error.' );
        }
        return $e;
    }

    /**
     * This function checks to see if we have the right config values 
     *
     * @return string the error message if any
     * @public
     */
    function checkConfig( ) {
        return null;
    }

}         
