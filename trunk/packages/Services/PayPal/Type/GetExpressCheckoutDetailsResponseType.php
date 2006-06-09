<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractResponseType.php';

/**
 * GetExpressCheckoutDetailsResponseType
 *
 * @package Services_PayPal
 */
class GetExpressCheckoutDetailsResponseType extends AbstractResponseType
{
    var $GetExpressCheckoutDetailsResponseDetails;

    function GetExpressCheckoutDetailsResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'GetExpressCheckoutDetailsResponseDetails' => 
              array (
                'required' => true,
                'type' => 'GetExpressCheckoutDetailsResponseDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getGetExpressCheckoutDetailsResponseDetails()
    {
        return $this->GetExpressCheckoutDetailsResponseDetails;
    }
    function setGetExpressCheckoutDetailsResponseDetails($GetExpressCheckoutDetailsResponseDetails, $charset = 'iso-8859-1')
    {
        $this->GetExpressCheckoutDetailsResponseDetails = $GetExpressCheckoutDetailsResponseDetails;
        $this->_elements['GetExpressCheckoutDetailsResponseDetails']['charset'] = $charset;
    }
}
