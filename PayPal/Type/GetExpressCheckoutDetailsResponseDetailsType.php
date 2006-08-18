<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * GetExpressCheckoutDetailsResponseDetailsType
 *
 * @package PayPal
 */
class GetExpressCheckoutDetailsResponseDetailsType extends XSDType
{
    /**
     * The timestamped token value that was returned by SetExpressCheckoutResponse and
     * passed on GetExpressCheckoutDetailsRequest.
     */
    var $Token;

    /**
     * Information about the payer
     */
    var $PayerInfo;

    /**
     * A free-form field for your own use, as set by you in the Custom element of
     * SetExpressCheckoutRequest.
     */
    var $Custom;

    /**
     * Your own invoice or tracking number, as set by you in the InvoiceID element of
     * SetExpressCheckoutRequest.
     */
    var $InvoiceID;

    /**
     * Payer's contact telephone number. PayPal returns a contact telephone number only
     * if your Merchant account profile settings require that the buyer enter one.
     */
    var $ContactPhone;

    function GetExpressCheckoutDetailsResponseDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Token' => 
              array (
                'required' => true,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
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
              'ContactPhone' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getToken()
    {
        return $this->Token;
    }
    function setToken($Token, $charset = 'iso-8859-1')
    {
        $this->Token = $Token;
        $this->_elements['Token']['charset'] = $charset;
    }
    function getPayerInfo()
    {
        return $this->PayerInfo;
    }
    function setPayerInfo($PayerInfo, $charset = 'iso-8859-1')
    {
        $this->PayerInfo = $PayerInfo;
        $this->_elements['PayerInfo']['charset'] = $charset;
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
    function getContactPhone()
    {
        return $this->ContactPhone;
    }
    function setContactPhone($ContactPhone, $charset = 'iso-8859-1')
    {
        $this->ContactPhone = $ContactPhone;
        $this->_elements['ContactPhone']['charset'] = $charset;
    }
}
