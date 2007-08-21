<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * GetBalanceRequestType
 *
 * @package PayPal
 */
class GetBalanceRequestType extends AbstractRequestType
{
    function GetBalanceRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
