<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDSimpleType.php';

/**
 * BasicAmountType
 *
 * @package Services_PayPal
 */
class BasicAmountType extends XSDSimpleType
{
    function BasicAmountType()
    {
        parent::XSDSimpleType();
        $this->_namespace = 'urn:ebay:apis:CoreComponentTypes';
        $this->_attributes = array_merge($this->_attributes,
            array (
              'currencyID' => 
              array (
                'name' => 'currencyID',
                'type' => 'ebl:CurrencyCodeType',
                'use' => 'required',
              ),
            ));
    }

}
