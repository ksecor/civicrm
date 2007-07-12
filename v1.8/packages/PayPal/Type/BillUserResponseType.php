<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * BillUserResponseType
 *
 * @package PayPal
 */
class BillUserResponseType extends AbstractResponseType
{
    var $BillUserResponseDetails;

    function BillUserResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'BillUserResponseDetails' => 
              array (
                'required' => true,
                'type' => 'MerchantPullPaymentResponseType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBillUserResponseDetails()
    {
        return $this->BillUserResponseDetails;
    }
    function setBillUserResponseDetails($BillUserResponseDetails, $charset = 'iso-8859-1')
    {
        $this->BillUserResponseDetails = $BillUserResponseDetails;
        $this->_elements['BillUserResponseDetails']['charset'] = $charset;
    }
}
