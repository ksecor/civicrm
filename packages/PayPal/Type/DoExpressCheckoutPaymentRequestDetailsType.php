<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * DoExpressCheckoutPaymentRequestDetailsType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentRequestDetailsType extends XSDType
{
    /**
     * How you want to obtain payment.
     */
    var $PaymentAction;

    /**
     * The timestamped token value that was returned by SetExpressCheckoutResponse and
     * passed on GetExpressCheckoutDetailsRequest.
     */
    var $Token;

    /**
     * Encrypted PayPal customer account identification number as returned by
     * GetExpressCheckoutDetailsResponse.
     */
    var $PayerID;

    /**
     * Information about the payment
     */
    var $PaymentDetails;

    function DoExpressCheckoutPaymentRequestDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PaymentAction' => 
              array (
                'required' => true,
                'type' => 'PaymentActionCodeType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Token' => 
              array (
                'required' => true,
                'type' => 'ExpressCheckoutTokenType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentDetails' => 
              array (
                'required' => true,
                'type' => 'PaymentDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getPaymentAction()
    {
        return $this->PaymentAction;
    }
    function setPaymentAction($PaymentAction, $charset = 'iso-8859-1')
    {
        $this->PaymentAction = $PaymentAction;
        $this->_elements['PaymentAction']['charset'] = $charset;
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
    function getPayerID()
    {
        return $this->PayerID;
    }
    function setPayerID($PayerID, $charset = 'iso-8859-1')
    {
        $this->PayerID = $PayerID;
        $this->_elements['PayerID']['charset'] = $charset;
    }
    function getPaymentDetails()
    {
        return $this->PaymentDetails;
    }
    function setPaymentDetails($PaymentDetails, $charset = 'iso-8859-1')
    {
        $this->PaymentDetails = $PaymentDetails;
        $this->_elements['PaymentDetails']['charset'] = $charset;
    }
}
