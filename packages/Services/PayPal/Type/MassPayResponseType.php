<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractResponseType.php';

/**
 * MassPayResponseType
 *
 * @package Services_PayPal
 */
class MassPayResponseType extends AbstractResponseType
{
    function MassPayResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
    }

}
