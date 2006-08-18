<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * BuyerType
 * 
 * Information about user used by buying applications
 *
 * @package PayPal
 */
class BuyerType extends XSDType
{
    var $ShippingAddress;

    function BuyerType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'ShippingAddress' => 
              array (
                'required' => false,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getShippingAddress()
    {
        return $this->ShippingAddress;
    }
    function setShippingAddress($ShippingAddress, $charset = 'iso-8859-1')
    {
        $this->ShippingAddress = $ShippingAddress;
        $this->_elements['ShippingAddress']['charset'] = $charset;
    }
}
