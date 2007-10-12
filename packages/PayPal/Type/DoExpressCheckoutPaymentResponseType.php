<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * DoExpressCheckoutPaymentResponseType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentResponseType extends AbstractResponseType
{
    var $DoExpressCheckoutPaymentResponseDetails;

    function DoExpressCheckoutPaymentResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoExpressCheckoutPaymentResponseDetails' => 
              array (
                'required' => true,
                'type' => 'DoExpressCheckoutPaymentResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoExpressCheckoutPaymentResponseDetails()
    {
        return $this->DoExpressCheckoutPaymentResponseDetails;
    }
    function setDoExpressCheckoutPaymentResponseDetails($DoExpressCheckoutPaymentResponseDetails, $charset = 'iso-8859-1')
    {
        $this->DoExpressCheckoutPaymentResponseDetails = $DoExpressCheckoutPaymentResponseDetails;
        $this->_elements['DoExpressCheckoutPaymentResponseDetails']['charset'] = $charset;
    }
}
