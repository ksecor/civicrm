<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractRequestType.php';

/**
 * BAUpdateRequestType
 *
 * @package PayPal
 */
class BAUpdateRequestType extends AbstractRequestType
{
    /**
     * Preapproved Payments billing agreement identification number. Corresponds to the
     * FORM variable mp_id.
     */
    var $MpID;

    /**
     * Custom annotation field for your exclusive use
     */
    var $Custom;

    /**
     * Description of goods or services associated with the billing agreement
     */
    var $Desc;

    /**
     * The current status of the billing agreement
     */
    var $MpStatus;

    function BAUpdateRequestType()
    {
        parent::AbstractRequestType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'MpID' => 
              array (
                'required' => true,
                'type' => 'MerchantPullIDType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Custom' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'Desc' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'MpStatus' => 
              array (
                'required' => false,
                'type' => 'MerchantPullStatusCodeType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getMpID()
    {
        return $this->MpID;
    }
    function setMpID($MpID, $charset = 'iso-8859-1')
    {
        $this->MpID = $MpID;
        $this->_elements['MpID']['charset'] = $charset;
    }
    function getCustom()
    {
        return $this->Custom;
    }
    function setCustom($Custom, $charset = 'iso-8859-1')
    {
        $this->Custom = $Custom;
        $this->_elements['Custom']['charset'] = $charset;
    }
    function getDesc()
    {
        return $this->Desc;
    }
    function setDesc($Desc, $charset = 'iso-8859-1')
    {
        $this->Desc = $Desc;
        $this->_elements['Desc']['charset'] = $charset;
    }
    function getMpStatus()
    {
        return $this->MpStatus;
    }
    function setMpStatus($MpStatus, $charset = 'iso-8859-1')
    {
        $this->MpStatus = $MpStatus;
        $this->_elements['MpStatus']['charset'] = $charset;
    }
}
