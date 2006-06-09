<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * CreditCardDetailsType
 * 
 * CreditCardDetailsType - Type declaration to be used by other schemas.
 * Information about a Credit Card.
 *
 * @package Services_PayPal
 */
class CreditCardDetailsType extends XSDType
{
    var $CreditCardType;

    var $CreditCardNumber;

    var $ExpMonth;

    var $ExpYear;

    var $CardOwner;

    var $CVV2;

    function CreditCardDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'CreditCardType' => 
              array (
                'required' => true,
                'type' => 'CreditCardTypeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CreditCardNumber' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpMonth' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ExpYear' => 
              array (
                'required' => true,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CardOwner' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'CVV2' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getCreditCardType()
    {
        return $this->CreditCardType;
    }
    function setCreditCardType($CreditCardType, $charset = 'iso-8859-1')
    {
        $this->CreditCardType = $CreditCardType;
        $this->_elements['CreditCardType']['charset'] = $charset;
    }
    function getCreditCardNumber()
    {
        return $this->CreditCardNumber;
    }
    function setCreditCardNumber($CreditCardNumber, $charset = 'iso-8859-1')
    {
        $this->CreditCardNumber = $CreditCardNumber;
        $this->_elements['CreditCardNumber']['charset'] = $charset;
    }
    function getExpMonth()
    {
        return $this->ExpMonth;
    }
    function setExpMonth($ExpMonth, $charset = 'iso-8859-1')
    {
        $this->ExpMonth = $ExpMonth;
        $this->_elements['ExpMonth']['charset'] = $charset;
    }
    function getExpYear()
    {
        return $this->ExpYear;
    }
    function setExpYear($ExpYear, $charset = 'iso-8859-1')
    {
        $this->ExpYear = $ExpYear;
        $this->_elements['ExpYear']['charset'] = $charset;
    }
    function getCardOwner()
    {
        return $this->CardOwner;
    }
    function setCardOwner($CardOwner, $charset = 'iso-8859-1')
    {
        $this->CardOwner = $CardOwner;
        $this->_elements['CardOwner']['charset'] = $charset;
    }
    function getCVV2()
    {
        return $this->CVV2;
    }
    function setCVV2($CVV2, $charset = 'iso-8859-1')
    {
        $this->CVV2 = $CVV2;
        $this->_elements['CVV2']['charset'] = $charset;
    }
}
