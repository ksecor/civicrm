<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * ReceiverInfoType
 * 
 * ReceiverInfoType - Type declaration to be used by other schemas. Receiver
 * information.
 *
 * @package Services_PayPal
 */
class ReceiverInfoType extends XSDType
{
    var $Business;

    var $Receiver;

    var $ReceiverID;

    function ReceiverInfoType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Business' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Receiver' => 
              array (
                'required' => true,
                'type' => 'EmailAddressType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReceiverID' => 
              array (
                'required' => true,
                'type' => 'UserIDType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getBusiness()
    {
        return $this->Business;
    }
    function setBusiness($Business, $charset = 'iso-8859-1')
    {
        $this->Business = $Business;
        $this->_elements['Business']['charset'] = $charset;
    }
    function getReceiver()
    {
        return $this->Receiver;
    }
    function setReceiver($Receiver, $charset = 'iso-8859-1')
    {
        $this->Receiver = $Receiver;
        $this->_elements['Receiver']['charset'] = $charset;
    }
    function getReceiverID()
    {
        return $this->ReceiverID;
    }
    function setReceiverID($ReceiverID, $charset = 'iso-8859-1')
    {
        $this->ReceiverID = $ReceiverID;
        $this->_elements['ReceiverID']['charset'] = $charset;
    }
}
