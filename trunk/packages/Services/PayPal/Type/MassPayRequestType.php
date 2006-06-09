<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/AbstractRequestType.php';

/**
 * MassPayRequestType
 *
 * @package Services_PayPal
 */
class MassPayRequestType extends AbstractRequestType
{
    var $EmailSubject;

    var $MassPayItem;

    function MassPayRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'EmailSubject' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'MassPayItem' => 
              array (
                'required' => true,
                'type' => 'MassPayRequestItemType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getEmailSubject()
    {
        return $this->EmailSubject;
    }
    function setEmailSubject($EmailSubject, $charset = 'iso-8859-1')
    {
        $this->EmailSubject = $EmailSubject;
        $this->_elements['EmailSubject']['charset'] = $charset;
    }
    function getMassPayItem()
    {
        return $this->MassPayItem;
    }
    function setMassPayItem($MassPayItem, $charset = 'iso-8859-1')
    {
        $this->MassPayItem = $MassPayItem;
        $this->_elements['MassPayItem']['charset'] = $charset;
    }
}
