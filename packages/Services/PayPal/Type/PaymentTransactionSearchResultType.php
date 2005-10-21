<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * PaymentTransactionSearchResultType
 * 
 * PaymentTransactionSearchResultType - Type declaration to be used by other
 * schemas. Results from a PaymentTransaction search
 *
 * @package Services_PayPal
 */
class PaymentTransactionSearchResultType extends XSDType
{
    var $Timestamp;

    var $Timezone;

    var $Type;

    var $Payer;

    var $PayerDisplayName;

    var $TransactionID;

    var $Status;

    var $GrossAmount;

    var $FeeAmount;

    var $NetAmount;

    function PaymentTransactionSearchResultType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Timestamp' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Timezone' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Type' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Payer' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerDisplayName' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Status' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'GrossAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'FeeAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NetAmount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getTimestamp()
    {
        return $this->Timestamp;
    }
    function setTimestamp($Timestamp, $charset = 'iso-8859-1')
    {
        $this->Timestamp = $Timestamp;
        $this->_elements['Timestamp']['charset'] = $charset;
    }
    function getTimezone()
    {
        return $this->Timezone;
    }
    function setTimezone($Timezone, $charset = 'iso-8859-1')
    {
        $this->Timezone = $Timezone;
        $this->_elements['Timezone']['charset'] = $charset;
    }
    function getType()
    {
        return $this->Type;
    }
    function setType($Type, $charset = 'iso-8859-1')
    {
        $this->Type = $Type;
        $this->_elements['Type']['charset'] = $charset;
    }
    function getPayer()
    {
        return $this->Payer;
    }
    function setPayer($Payer, $charset = 'iso-8859-1')
    {
        $this->Payer = $Payer;
        $this->_elements['Payer']['charset'] = $charset;
    }
    function getPayerDisplayName()
    {
        return $this->PayerDisplayName;
    }
    function setPayerDisplayName($PayerDisplayName, $charset = 'iso-8859-1')
    {
        $this->PayerDisplayName = $PayerDisplayName;
        $this->_elements['PayerDisplayName']['charset'] = $charset;
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
    function getStatus()
    {
        return $this->Status;
    }
    function setStatus($Status, $charset = 'iso-8859-1')
    {
        $this->Status = $Status;
        $this->_elements['Status']['charset'] = $charset;
    }
    function getGrossAmount()
    {
        return $this->GrossAmount;
    }
    function setGrossAmount($GrossAmount, $charset = 'iso-8859-1')
    {
        $this->GrossAmount = $GrossAmount;
        $this->_elements['GrossAmount']['charset'] = $charset;
    }
    function getFeeAmount()
    {
        return $this->FeeAmount;
    }
    function setFeeAmount($FeeAmount, $charset = 'iso-8859-1')
    {
        $this->FeeAmount = $FeeAmount;
        $this->_elements['FeeAmount']['charset'] = $charset;
    }
    function getNetAmount()
    {
        return $this->NetAmount;
    }
    function setNetAmount($NetAmount, $charset = 'iso-8859-1')
    {
        $this->NetAmount = $NetAmount;
        $this->_elements['NetAmount']['charset'] = $charset;
    }
}
