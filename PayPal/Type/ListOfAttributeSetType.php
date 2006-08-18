<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * ListOfAttributeSetType
 *
 * @package PayPal
 */
class ListOfAttributeSetType extends XSDType
{
    var $AttributeSet;

    function ListOfAttributeSetType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'AttributeSet' => 
              array (
                'required' => true,
                'type' => 'AttributeSetType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getAttributeSet()
    {
        return $this->AttributeSet;
    }
    function setAttributeSet($AttributeSet, $charset = 'iso-8859-1')
    {
        $this->AttributeSet = $AttributeSet;
        $this->_elements['AttributeSet']['charset'] = $charset;
    }
}
