<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractResponseType.php';

/**
 * RefundTransactionResponseType
 *
 * @package Services_PayPal
 */
class RefundTransactionResponseType extends AbstractResponseType
{
    var $RefundTransactionID;

    var $NetRefundAmount;

    var $FeeRefundAmount;

    var $GrossRefundAmount;

    function RefundTransactionResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'RefundTransactionID' => 
              array (
                'required' => false,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'NetRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'FeeRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'GrossRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getRefundTransactionID()
    {
        return $this->RefundTransactionID;
    }
    function setRefundTransactionID($RefundTransactionID, $charset = 'iso-8859-1')
    {
        $this->RefundTransactionID = $RefundTransactionID;
        $this->_elements['RefundTransactionID']['charset'] = $charset;
    }
    function getNetRefundAmount()
    {
        return $this->NetRefundAmount;
    }
    function setNetRefundAmount($NetRefundAmount, $charset = 'iso-8859-1')
    {
        $this->NetRefundAmount = $NetRefundAmount;
        $this->_elements['NetRefundAmount']['charset'] = $charset;
    }
    function getFeeRefundAmount()
    {
        return $this->FeeRefundAmount;
    }
    function setFeeRefundAmount($FeeRefundAmount, $charset = 'iso-8859-1')
    {
        $this->FeeRefundAmount = $FeeRefundAmount;
        $this->_elements['FeeRefundAmount']['charset'] = $charset;
    }
    function getGrossRefundAmount()
    {
        return $this->GrossRefundAmount;
    }
    function setGrossRefundAmount($GrossRefundAmount, $charset = 'iso-8859-1')
    {
        $this->GrossRefundAmount = $GrossRefundAmount;
        $this->_elements['GrossRefundAmount']['charset'] = $charset;
    }
}
