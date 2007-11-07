<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * DoReferenceTransactionRequestType
 *
 * @package PayPal
 */
class DoReferenceTransactionRequestType extends AbstractRequestType
{
    var $DoReferenceTransactionRequestDetails;

    function DoReferenceTransactionRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'DoReferenceTransactionRequestDetails' => 
              array (
                'required' => true,
                'type' => 'DoReferenceTransactionRequestDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDoReferenceTransactionRequestDetails()
    {
        return $this->DoReferenceTransactionRequestDetails;
    }
    function setDoReferenceTransactionRequestDetails($DoReferenceTransactionRequestDetails, $charset = 'iso-8859-1')
    {
        $this->DoReferenceTransactionRequestDetails = $DoReferenceTransactionRequestDetails;
        $this->_elements['DoReferenceTransactionRequestDetails']['charset'] = $charset;
    }
}
