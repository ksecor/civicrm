<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * MerchantPullPaymentResponseType
 * 
 * MerchantPullPaymentResponseType - Type declaration to be used by other schemas.
 * Response data from the merchant pull.
 *
 * @package Services_PayPal
 */
class MerchantPullPaymentResponseType extends XSDType
{
    var $PayerInfo;

    var $PaymentInfo;

    var $MerchantPullInfo;

    function MerchantPullPaymentResponseType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentInfo' => 
              array (
                'required' => true,
                'type' => 'PaymentInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MerchantPullInfo' => 
              array (
                'required' => true,
                'type' => 'MerchantPullInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
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
    function getPaymentInfo()
    {
        return $this->PaymentInfo;
    }
    function setPaymentInfo($PaymentInfo, $charset = 'iso-8859-1')
    {
        $this->PaymentInfo = $PaymentInfo;
        $this->_elements['PaymentInfo']['charset'] = $charset;
    }
    function getMerchantPullInfo()
    {
        return $this->MerchantPullInfo;
    }
    function setMerchantPullInfo($MerchantPullInfo, $charset = 'iso-8859-1')
    {
        $this->MerchantPullInfo = $MerchantPullInfo;
        $this->_elements['MerchantPullInfo']['charset'] = $charset;
    }
}
