<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * EbayItemPaymentDetailsItemType
 * 
 * EbayItemPaymentDetailsItemType - Type declaration to be used by other schemas.
 * Information about an Ebay Payment Item.
 *
 * @package PayPal
 */
class EbayItemPaymentDetailsItemType extends XSDType
{
    /**
     * Auction ItemNumber.
     */
    var $ItemNumber;

    /**
     * Auction Transaction ID.
     */
    var $AuctionTransactionId;

    /**
     * Ebay Order ID.
     */
    var $OrderId;

    function EbayItemPaymentDetailsItemType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ItemNumber' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'AuctionTransactionId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'OrderId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getItemNumber()
    {
        return $this->ItemNumber;
    }
    function setItemNumber($ItemNumber, $charset = 'iso-8859-1')
    {
        $this->ItemNumber = $ItemNumber;
        $this->_elements['ItemNumber']['charset'] = $charset;
    }
    function getAuctionTransactionId()
    {
        return $this->AuctionTransactionId;
    }
    function setAuctionTransactionId($AuctionTransactionId, $charset = 'iso-8859-1')
    {
        $this->AuctionTransactionId = $AuctionTransactionId;
        $this->_elements['AuctionTransactionId']['charset'] = $charset;
    }
    function getOrderId()
    {
        return $this->OrderId;
    }
    function setOrderId($OrderId, $charset = 'iso-8859-1')
    {
        $this->OrderId = $OrderId;
        $this->_elements['OrderId']['charset'] = $charset;
    }
}
