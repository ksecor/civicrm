<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * GetMobileStatusRequestType
 *
 * @package PayPal
 */
class GetMobileStatusRequestType extends AbstractRequestType
{
    /**
     * Phone number for status inquiry
     */
    var $Phone;

    function GetMobileStatusRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'Phone' => 
              array (
                'required' => true,
                'type' => 'PhoneNumberType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getPhone()
    {
        return $this->Phone;
    }
    function setPhone($Phone, $charset = 'iso-8859-1')
    {
        $this->Phone = $Phone;
        $this->_elements['Phone']['charset'] = $charset;
    }
}
