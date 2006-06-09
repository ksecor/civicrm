<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * PaymentDetailsType
 * 
 * PaymentDetailsType - Type declaration to be used by other schemas. Information
 * about a payment. Used by DCC and Express Checkout.
 *
 * @package Services_PayPal
 */
class PaymentDetailsType extends XSDType
{
    var $OrderTotal;

    var $ItemTotal;

    var $ShippingTotal;

    var $HandlingTotal;

    var $TaxTotal;

    var $OrderDescription;

    var $Custom;

    var $InvoiceID;

    var $ButtonSource;

    var $NotifyURL;

    var $ShipToAddress;

    var $PaymentDetailsItem;

    function PaymentDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'OrderTotal' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ItemTotal' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShippingTotal' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'HandlingTotal' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TaxTotal' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OrderDescription' => 
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
              'InvoiceID' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ButtonSource' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'NotifyURL' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ShipToAddress' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetailsItem' => 
              array (
                'required' => false,
                'type' => 'PaymentDetailsItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getOrderTotal()
    {
        return $this->OrderTotal;
    }
    function setOrderTotal($OrderTotal, $charset = 'iso-8859-1')
    {
        $this->OrderTotal = $OrderTotal;
        $this->_elements['OrderTotal']['charset'] = $charset;
    }
    function getItemTotal()
    {
        return $this->ItemTotal;
    }
    function setItemTotal($ItemTotal, $charset = 'iso-8859-1')
    {
        $this->ItemTotal = $ItemTotal;
        $this->_elements['ItemTotal']['charset'] = $charset;
    }
    function getShippingTotal()
    {
        return $this->ShippingTotal;
    }
    function setShippingTotal($ShippingTotal, $charset = 'iso-8859-1')
    {
        $this->ShippingTotal = $ShippingTotal;
        $this->_elements['ShippingTotal']['charset'] = $charset;
    }
    function getHandlingTotal()
    {
        return $this->HandlingTotal;
    }
    function setHandlingTotal($HandlingTotal, $charset = 'iso-8859-1')
    {
        $this->HandlingTotal = $HandlingTotal;
        $this->_elements['HandlingTotal']['charset'] = $charset;
    }
    function getTaxTotal()
    {
        return $this->TaxTotal;
    }
    function setTaxTotal($TaxTotal, $charset = 'iso-8859-1')
    {
        $this->TaxTotal = $TaxTotal;
        $this->_elements['TaxTotal']['charset'] = $charset;
    }
    function getOrderDescription()
    {
        return $this->OrderDescription;
    }
    function setOrderDescription($OrderDescription, $charset = 'iso-8859-1')
    {
        $this->OrderDescription = $OrderDescription;
        $this->_elements['OrderDescription']['charset'] = $charset;
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
    function getInvoiceID()
    {
        return $this->InvoiceID;
    }
    function setInvoiceID($InvoiceID, $charset = 'iso-8859-1')
    {
        $this->InvoiceID = $InvoiceID;
        $this->_elements['InvoiceID']['charset'] = $charset;
    }
    function getButtonSource()
    {
        return $this->ButtonSource;
    }
    function setButtonSource($ButtonSource, $charset = 'iso-8859-1')
    {
        $this->ButtonSource = $ButtonSource;
        $this->_elements['ButtonSource']['charset'] = $charset;
    }
    function getNotifyURL()
    {
        return $this->NotifyURL;
    }
    function setNotifyURL($NotifyURL, $charset = 'iso-8859-1')
    {
        $this->NotifyURL = $NotifyURL;
        $this->_elements['NotifyURL']['charset'] = $charset;
    }
    function getShipToAddress()
    {
        return $this->ShipToAddress;
    }
    function setShipToAddress($ShipToAddress, $charset = 'iso-8859-1')
    {
        $this->ShipToAddress = $ShipToAddress;
        $this->_elements['ShipToAddress']['charset'] = $charset;
    }
    function getPaymentDetailsItem()
    {
        return $this->PaymentDetailsItem;
    }
    function setPaymentDetailsItem($PaymentDetailsItem, $charset = 'iso-8859-1')
    {
        $this->PaymentDetailsItem = $PaymentDetailsItem;
        $this->_elements['PaymentDetailsItem']['charset'] = $charset;
    }
}
