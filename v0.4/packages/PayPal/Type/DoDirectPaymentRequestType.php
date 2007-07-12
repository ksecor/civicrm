<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoDirectPaymentRequestType
 *
 * @package PayPal
 */
class DoDirectPaymentRequestType extends AbstractRequestType
{
    var $DoDirectPaymentRequestDetails;

    function DoDirectPaymentRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoDirectPaymentRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoDirectPaymentRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoDirectPaymentRequestDetails()
    {
        return $this->DoDirectPaymentRequestDetails;
    }
    function setDoDirectPaymentRequestDetails($DoDirectPaymentRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoDirectPaymentRequestDetails = $DoDirectPaymentRequestDetails;
        $this->_elements['DoDirectPaymentRequestDetails']['charset'] = $charset;
    }
}
