<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractRequestType.php';

/**
 * TransactionSearchRequestType
 *
 * @package Services_PayPal
 */
class TransactionSearchRequestType extends AbstractRequestType
{
    var $StartDate;

    var $EndDate;

    var $Payer;

    var $Receiver;

    var $ReceiptID;

    var $TransactionID;

    var $PayerName;

    var $AuctionItemNumber;

    var $InvoiceID;

    var $TransactionClass;

    var $Amount;

    var $CurrencyCode;

    var $Status;

    function TransactionSearchRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'StartDate' => 
              array (
                'required' => true,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'EndDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Payer' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Receiver' => 
              array (
                'required' => false,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ReceiptID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TransactionID' => 
              array (
                'required' => false,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'PayerName' => 
              array (
                'required' => false,
                'type' => 'PersonNameType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'AuctionItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TransactionClass' => 
              array (
                'required' => false,
                'type' => 'PaymentTransactionClassCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CurrencyCode' => 
              array (
                'required' => false,
                'type' => 'CurrencyCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Status' => 
              array (
                'required' => false,
                'type' => 'PaymentTransactionStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getStartDate()
    {
        return $this->StartDate;
    }
    function setStartDate($StartDate, $charset = 'iso-8859-1')
    {
        $this->StartDate = $StartDate;
        $this->_elements['StartDate']['charset'] = $charset;
    }
    function getEndDate()
    {
        return $this->EndDate;
    }
    function setEndDate($EndDate, $charset = 'iso-8859-1')
    {
        $this->EndDate = $EndDate;
        $this->_elements['EndDate']['charset'] = $charset;
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
    function getReceiver()
    {
        return $this->Receiver;
    }
    function setReceiver($Receiver, $charset = 'iso-8859-1')
    {
        $this->Receiver = $Receiver;
        $this->_elements['Receiver']['charset'] = $charset;
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
    function getTransactionID()
    {
        return $this->TransactionID;
    }
    function setTransactionID($TransactionID, $charset = 'iso-8859-1')
    {
        $this->TransactionID = $TransactionID;
        $this->_elements['TransactionID']['charset'] = $charset;
    }
    function getPayerName()
    {
        return $this->PayerName;
    }
    function setPayerName($PayerName, $charset = 'iso-8859-1')
    {
        $this->PayerName = $PayerName;
        $this->_elements['PayerName']['charset'] = $charset;
    }
    function getAuctionItemNumber()
    {
        return $this->AuctionItemNumber;
    }
    function setAuctionItemNumber($AuctionItemNumber, $charset = 'iso-8859-1')
    {
        $this->AuctionItemNumber = $AuctionItemNumber;
        $this->_elements['AuctionItemNumber']['charset'] = $charset;
    }
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
    function getTransactionClass()
    {
        return $this->TransactionClass;
    }
    function setTransactionClass($TransactionClass, $charset = 'iso-8859-1')
    {
        $this->TransactionClass = $TransactionClass;
        $this->_elements['TransactionClass']['charset'] = $charset;
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
    function getCurrencyCode()
    {
        return $this->CurrencyCode;
    }
    function setCurrencyCode($CurrencyCode, $charset = 'iso-8859-1')
    {
        $this->CurrencyCode = $CurrencyCode;
        $this->_elements['CurrencyCode']['charset'] = $charset;
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
}
