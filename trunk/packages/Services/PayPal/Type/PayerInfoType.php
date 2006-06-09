<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * PayerInfoType
 * 
 * PayerInfoType - Type declaration to be used by other schemas. Payer information
 *
 * @package Services_PayPal
 */
class PayerInfoType extends XSDType
{
    var $Payer;

    var $PayerID;

    var $PayerStatus;

    var $PayerName;

    var $PayerCountry;

    var $PayerBusiness;

    var $Address;

    function PayerInfoType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Payer' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerStatus' => 
              array (
                'required' => true,
                'type' => 'PayPalUserStatusCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerName' => 
              array (
                'required' => true,
                'type' => 'PersonNameType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerCountry' => 
              array (
                'required' => true,
                'type' => 'CountryCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerBusiness' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Address' => 
              array (
                'required' => false,
                'type' => 'AddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getPayerID()
    {
        return $this->PayerID;
    }
    function setPayerID($PayerID, $charset = 'iso-8859-1')
    {
        $this->PayerID = $PayerID;
        $this->_elements['PayerID']['charset'] = $charset;
    }
    function getPayerStatus()
    {
        return $this->PayerStatus;
    }
    function setPayerStatus($PayerStatus, $charset = 'iso-8859-1')
    {
        $this->PayerStatus = $PayerStatus;
        $this->_elements['PayerStatus']['charset'] = $charset;
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
    function getPayerCountry()
    {
        return $this->PayerCountry;
    }
    function setPayerCountry($PayerCountry, $charset = 'iso-8859-1')
    {
        $this->PayerCountry = $PayerCountry;
        $this->_elements['PayerCountry']['charset'] = $charset;
    }
    function getPayerBusiness()
    {
        return $this->PayerBusiness;
    }
    function setPayerBusiness($PayerBusiness, $charset = 'iso-8859-1')
    {
        $this->PayerBusiness = $PayerBusiness;
        $this->_elements['PayerBusiness']['charset'] = $charset;
    }
    function getAddress()
    {
        return $this->Address;
    }
    function setAddress($Address, $charset = 'iso-8859-1')
    {
        $this->Address = $Address;
        $this->_elements['Address']['charset'] = $charset;
    }
}
