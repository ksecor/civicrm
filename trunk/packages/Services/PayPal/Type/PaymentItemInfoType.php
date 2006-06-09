<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * PaymentItemInfoType
 * 
 * PaymentItemInfoType - Type declaration to be used by other schemas. Information
 * about a PayPal item.
 *
 * @package Services_PayPal
 */
class PaymentItemInfoType extends XSDType
{
    var $InvoiceID;

    var $Custom;

    var $Memo;

    var $SalesTax;

    var $PaymentItem;

    var $Subscription;

    var $Auction;

    function PaymentItemInfoType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Custom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Memo' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SalesTax' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentItem' => 
              array (
                'required' => false,
                'type' => 'PaymentItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Subscription' => 
              array (
                'required' => false,
                'type' => 'SubscriptionInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Auction' => 
              array (
                'required' => false,
                'type' => 'AuctionInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
    }
    function getMemo()
    {
        return $this->Memo;
    }
    function setMemo($Memo, $charset = 'iso-8859-1')
    {
        $this->Memo = $Memo;
        $this->_elements['Memo']['charset'] = $charset;
    }
    function getSalesTax()
    {
        return $this->SalesTax;
    }
    function setSalesTax($SalesTax, $charset = 'iso-8859-1')
    {
        $this->SalesTax = $SalesTax;
        $this->_elements['SalesTax']['charset'] = $charset;
    }
    function getPaymentItem()
    {
        return $this->PaymentItem;
    }
    function setPaymentItem($PaymentItem, $charset = 'iso-8859-1')
    {
        $this->PaymentItem = $PaymentItem;
        $this->_elements['PaymentItem']['charset'] = $charset;
    }
    function getSubscription()
    {
        return $this->Subscription;
    }
    function setSubscription($Subscription, $charset = 'iso-8859-1')
    {
        $this->Subscription = $Subscription;
        $this->_elements['Subscription']['charset'] = $charset;
    }
    function getAuction()
    {
        return $this->Auction;
    }
    function setAuction($Auction, $charset = 'iso-8859-1')
    {
        $this->Auction = $Auction;
        $this->_elements['Auction']['charset'] = $charset;
    }
}
