<?php
/**
 * This is one of two base Type files that are not automatically
 * generated from the WSDL.
 *
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * Base Type classs that allows for conversion of types into
 * SOAP_Value objects.
 *
 * @package PayPal
 */
class XSDSimpleType extends XSDType
{
    /**
     * The simple value of this type.
     *
     * @access protected
     *
     * @var mixed $_value
     */
    var $_value;

    /**
     * The charset of this type's value.
     *
     * @access protected
     *
     * @var string $_charset
     */
    var $_charset = 'iso-8859-1';

    /**
     * Constructor.
     */
    function XSDSimpleType($value = null, $attributes = array())
    {
        $this->_value = $value;
        $this->_attributeValues = $attributes;
    }

    /**
     * Turn this type into a SOAP_Value object useable with
     * SOAP_Client.
     *
     * @param string $name  The name to use for the value.
     *
     * @return SOAP_Value  A SOAP_Value object representing this type instance.
     */
    function &getSoapValue($name)
    {
        include_once 'PayPal/SOAP/Value.php';

        $value = $this->_value;
        if (is_string($value) && $this->_charset = 'iso-8859-1' &&
            (utf8_encode(utf8_decode($value)) != $value)) {
            $value = utf8_encode($value);
        }
        if (count($this->_attributeValues)) {
            $v =& new SOAP_Value($name, '', $value, $this->_attributeValues);
        } else {
            $v =& new SOAP_Value($name, '', $value);
        }
        return $v;
    }

    /**
     * Set the value of this simple object.
     */
    function setval($value, $charset = 'iso-8859-1')
    {
        $this->_value = $value;
        $this->_charset = $charset;
    }

}
