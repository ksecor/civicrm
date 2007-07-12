<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoDirectPaymentResponseType
 *
 * @package PayPal
 */
class DoDirectPaymentResponseType extends AbstractResponseType
{
    /**
     * The amount of the payment as specified by you on DoDirectPaymentRequest.
     */
    var $Amount;

    /**
     * Address Verification System response code. Character limit: One single-byte
     * alphanumeric character
     */
    var $AVSCode;

    /**
     * Result of the CVV2 check by PayPal.
     */
    var $CVV2Code;

    /**
     * Transaction identification number.
     */
    var $TransactionID;

    function DoDirectPaymentResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'AVSCode' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CVV2Code' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TransactionID' => 
              array (
                'required' => true,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
    function getAVSCode()
    {
        return $this->AVSCode;
    }
    function setAVSCode($AVSCode, $charset = 'iso-8859-1')
    {
        $this->AVSCode = $AVSCode;
        $this->_elements['AVSCode']['charset'] = $charset;
    }
    function getCVV2Code()
    {
        return $this->CVV2Code;
    }
    function setCVV2Code($CVV2Code, $charset = 'iso-8859-1')
    {
        $this->CVV2Code = $CVV2Code;
        $this->_elements['CVV2Code']['charset'] = $charset;
    }
    function getTransactionID()
    {
        return $this->TransactionID;
    }
    function setTransactionID($TransactionID, $charset = 'iso-8859-1')
    {
        $this->TransactionID = $TransactionID;
        $this->_elements['TransactionID']['charset'] = $charset;
    }
}
