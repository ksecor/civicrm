<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * MassPayRequestItemType
 * 
 * MassPayRequestItemType - Type declaration to be used by other schemas. Request
 * data from the mass pay request
 *
 * @package Services_PayPal
 */
class MassPayRequestItemType extends XSDType
{
    var $ReceiverEmail;

    var $Amount;

    var $UniqueId;

    var $Note;

    function MassPayRequestItemType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'ReceiverEmail' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Amount' => 
              array (
                'required' => true,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'UniqueId' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Note' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getReceiverEmail()
    {
        return $this->ReceiverEmail;
    }
    function setReceiverEmail($ReceiverEmail, $charset = 'iso-8859-1')
    {
        $this->ReceiverEmail = $ReceiverEmail;
        $this->_elements['ReceiverEmail']['charset'] = $charset;
    }
    function getAmount()
    {
        return $this->Amount;
    }
    function setAmount($Amount, $charset = 'iso-8859-1')
    {
        $this->Amount = $Amount;
        $this->_elements['Amount']['charset'] = $charset;
    }
    function getUniqueId()
    {
        return $this->UniqueId;
    }
    function setUniqueId($UniqueId, $charset = 'iso-8859-1')
    {
        $this->UniqueId = $UniqueId;
        $this->_elements['UniqueId']['charset'] = $charset;
    }
    function getNote()
    {
        return $this->Note;
    }
    function setNote($Note, $charset = 'iso-8859-1')
    {
        $this->Note = $Note;
        $this->_elements['Note']['charset'] = $charset;
    }
}
