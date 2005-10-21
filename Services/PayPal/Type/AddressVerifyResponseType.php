<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractResponseType.php';

/**
 * AddressVerifyResponseType
 *
 * @package Services_PayPal
 */
class AddressVerifyResponseType extends AbstractResponseType
{
    var $ConfirmationCode;

    var $StreetMatch;

    var $ZipMatch;

    var $CountryCode;

    var $PayPalToken;

    function AddressVerifyResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ConfirmationCode' => 
              array (
                'required' => true,
                'type' => 'AddressStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'StreetMatch' => 
              array (
                'required' => true,
                'type' => 'MatchStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'ZipMatch' => 
              array (
                'required' => false,
                'type' => 'MatchStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'CountryCode' => 
              array (
                'required' => false,
                'type' => 'CountryCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'PayPalToken' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getConfirmationCode()
    {
        return $this->ConfirmationCode;
    }
    function setConfirmationCode($ConfirmationCode, $charset = 'iso-8859-1')
    {
        $this->ConfirmationCode = $ConfirmationCode;
        $this->_elements['ConfirmationCode']['charset'] = $charset;
    }
    function getStreetMatch()
    {
        return $this->StreetMatch;
    }
    function setStreetMatch($StreetMatch, $charset = 'iso-8859-1')
    {
        $this->StreetMatch = $StreetMatch;
        $this->_elements['StreetMatch']['charset'] = $charset;
    }
    function getZipMatch()
    {
        return $this->ZipMatch;
    }
    function setZipMatch($ZipMatch, $charset = 'iso-8859-1')
    {
        $this->ZipMatch = $ZipMatch;
        $this->_elements['ZipMatch']['charset'] = $charset;
    }
    function getCountryCode()
    {
        return $this->CountryCode;
    }
    function setCountryCode($CountryCode, $charset = 'iso-8859-1')
    {
        $this->CountryCode = $CountryCode;
        $this->_elements['CountryCode']['charset'] = $charset;
    }
    function getPayPalToken()
    {
        return $this->PayPalToken;
    }
    function setPayPalToken($PayPalToken, $charset = 'iso-8859-1')
    {
        $this->PayPalToken = $PayPalToken;
        $this->_elements['PayPalToken']['charset'] = $charset;
    }
}
