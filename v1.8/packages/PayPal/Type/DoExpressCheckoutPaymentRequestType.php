<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoExpressCheckoutPaymentRequestType
 *
 * @package PayPal
 */
class DoExpressCheckoutPaymentRequestType extends AbstractRequestType
{
    var $DoExpressCheckoutPaymentRequestDetails;

    function DoExpressCheckoutPaymentRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoExpressCheckoutPaymentRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoExpressCheckoutPaymentRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoExpressCheckoutPaymentRequestDetails()
    {
        return $this->DoExpressCheckoutPaymentRequestDetails;
    }
    function setDoExpressCheckoutPaymentRequestDetails($DoExpressCheckoutPaymentRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoExpressCheckoutPaymentRequestDetails = $DoExpressCheckoutPaymentRequestDetails;
        $this->_elements['DoExpressCheckoutPaymentRequestDetails']['charset'] = $charset;
    }
}
