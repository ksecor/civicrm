<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * PaymentDetailsItemType
 * 
 * PaymentDetailsItemType Information about a Payment Item.
 *
 * @package PayPal
 */
class PaymentDetailsItemType extends XSDType
{
    /**
     * Item name.
     */
    var $Name;

    /**
     * Item number.
     */
    var $Number;

    /**
     * Item quantity.
     */
    var $Quantity;

    /**
     * Item sales tax.
     */
    var $Tax;

    /**
     * Cost of item You must set the currencyID attribute to one of the three-character
     * currency codes for any of the supported PayPal currencies.
     */
    var $Amount;

    /**
     * Ebay specific details.
     */
    var $EbayItemPaymentDetailsItem;

    function PaymentDetailsItemType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Name' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Number' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Quantity' => 
              array (
                'required' => false,
                'type' => 'integer',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Tax' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Amount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EbayItemPaymentDetailsItem' => 
              array (
                'required' => false,
                'type' => 'EbayItemPaymentDetailsItemType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getName()
    {
        return $this->Name;
    }
    function setName($Name, $charset = 'iso-8859-1')
    {
        $this->Name = $Name;
        $this->_elements['Name']['charset'] = $charset;
    }
    function getNumber()
    {
        return $this->Number;
    }
    function setNumber($Number, $charset = 'iso-8859-1')
    {
        $this->Number = $Number;
        $this->_elements['Number']['charset'] = $charset;
    }
    function getQuantity()
    {
        return $this->Quantity;
    }
    function setQuantity($Quantity, $charset = 'iso-8859-1')
    {
        $this->Quantity = $Quantity;
        $this->_elements['Quantity']['charset'] = $charset;
    }
    function getTax()
    {
        return $this->Tax;
    }
    function setTax($Tax, $charset = 'iso-8859-1')
    {
        $this->Tax = $Tax;
        $this->_elements['Tax']['charset'] = $charset;
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
    function getEbayItemPaymentDetailsItem()
    {
        return $this->EbayItemPaymentDetailsItem;
    }
    function setEbayItemPaymentDetailsItem($EbayItemPaymentDetailsItem, $charset = 'iso-8859-1')
    {
        $this->EbayItemPaymentDetailsItem = $EbayItemPaymentDetailsItem;
        $this->_elements['EbayItemPaymentDetailsItem']['charset'] = $charset;
    }
}
