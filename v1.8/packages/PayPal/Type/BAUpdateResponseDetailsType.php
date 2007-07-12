<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * BAUpdateResponseDetailsType
 *
 * @package PayPal
 */
class BAUpdateResponseDetailsType extends XSDType
{
    /**
     * Preapproved Payments billing agreement identification number. Corresponds to the
     * FORM variable mp_id.
     */
    var $MpID;

    var $PayerInfo;

    var $MerchantPullInfo;

    function BAUpdateResponseDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'MpID' => 
              array (
                'required' => true,
                'type' => 'MerchantPullIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PayerInfo' => 
              array (
                'required' => true,
                'type' => 'PayerInfoType',
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

    function getMpID()
    {
        return $this->MpID;
    }
    function setMpID($MpID, $charset = 'iso-8859-1')
    {
        $this->MpID = $MpID;
        $this->_elements['MpID']['charset'] = $charset;
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
