<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * PaymentInfoType
 * 
 * PaymentInfoType - Type declaration to be used by other schemas. Payment
 * information.
 *
 * @package Services_PayPal
 */
class PaymentInfoType extends XSDType
{
    var $TransactionID;

    var $ParentTransactionID;

    var $ReceiptID;

    var $TransactionType;

    var $PaymentType;

    var $PaymentDate;

    var $GrossAmount;

    var $FeeAmount;

    var $SettleAmount;

    var $TaxAmount;

    var $ExchangeRate;

    var $PaymentStatus;

    var $PendingReason;

    var $ReasonCode;

    function PaymentInfoType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'TransactionID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ParentTransactionID' => 
              array (
                'required' => false,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReceiptID' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TransactionType' => 
              array (
                'required' => true,
                'type' => 'PaymentTransactionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentType' => 
              array (
                'required' => false,
                'type' => 'PaymentCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
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
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SettleAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExchangeRate' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentStatus' => 
              array (
                'required' => true,
                'type' => 'PaymentStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PendingReason' => 
              array (
                'required' => false,
                'type' => 'PendingStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReasonCode' => 
              array (
                'required' => false,
                'type' => 'ReversalReasonCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getParentTransactionID()
    {
        return $this->ParentTransactionID;
    }
    function setParentTransactionID($ParentTransactionID, $charset = 'iso-8859-1')
    {
        $this->ParentTransactionID = $ParentTransactionID;
        $this->_elements['ParentTransactionID']['charset'] = $charset;
    }
    function getReceiptID()
    {
        return $this->ReceiptID;
    }
    function setReceiptID($ReceiptID, $charset = 'iso-8859-1')
    {
        $this->ReceiptID = $ReceiptID;
        $this->_elements['ReceiptID']['charset'] = $charset;
    }
    function getTransactionType()
    {
        return $this->TransactionType;
    }
    function setTransactionType($TransactionType, $charset = 'iso-8859-1')
    {
        $this->TransactionType = $TransactionType;
        $this->_elements['TransactionType']['charset'] = $charset;
    }
    function getPaymentType()
    {
        return $this->PaymentType;
    }
    function setPaymentType($PaymentType, $charset = 'iso-8859-1')
    {
        $this->PaymentType = $PaymentType;
        $this->_elements['PaymentType']['charset'] = $charset;
    }
    function getPaymentDate()
    {
        return $this->PaymentDate;
    }
    function setPaymentDate($PaymentDate, $charset = 'iso-8859-1')
    {
        $this->PaymentDate = $PaymentDate;
        $this->_elements['PaymentDate']['charset'] = $charset;
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
    function getSettleAmount()
    {
        return $this->SettleAmount;
    }
    function setSettleAmount($SettleAmount, $charset = 'iso-8859-1')
    {
        $this->SettleAmount = $SettleAmount;
        $this->_elements['SettleAmount']['charset'] = $charset;
    }
    function getTaxAmount()
    {
        return $this->TaxAmount;
    }
    function setTaxAmount($TaxAmount, $charset = 'iso-8859-1')
    {
        $this->TaxAmount = $TaxAmount;
        $this->_elements['TaxAmount']['charset'] = $charset;
    }
    function getExchangeRate()
    {
        return $this->ExchangeRate;
    }
    function setExchangeRate($ExchangeRate, $charset = 'iso-8859-1')
    {
        $this->ExchangeRate = $ExchangeRate;
        $this->_elements['ExchangeRate']['charset'] = $charset;
    }
    function getPaymentStatus()
    {
        return $this->PaymentStatus;
    }
    function setPaymentStatus($PaymentStatus, $charset = 'iso-8859-1')
    {
        $this->PaymentStatus = $PaymentStatus;
        $this->_elements['PaymentStatus']['charset'] = $charset;
    }
    function getPendingReason()
    {
        return $this->PendingReason;
    }
    function setPendingReason($PendingReason, $charset = 'iso-8859-1')
    {
        $this->PendingReason = $PendingReason;
        $this->_elements['PendingReason']['charset'] = $charset;
    }
    function getReasonCode()
    {
        return $this->ReasonCode;
    }
    function setReasonCode($ReasonCode, $charset = 'iso-8859-1')
    {
        $this->ReasonCode = $ReasonCode;
        $this->_elements['ReasonCode']['charset'] = $charset;
    }
}
