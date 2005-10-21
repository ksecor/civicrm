<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * SubscriptionInfoType
 * 
 * SubscriptionInfoType - Type declaration to be used by other schemas. Information
 * about a PayPal Subscription.
 *
 * @package Services_PayPal
 */
class SubscriptionInfoType extends XSDType
{
    var $SubscriptionID;

    var $SubscriptionDate;

    var $EffectiveDate;

    var $RetryTime;

    var $Username;

    var $Password;

    var $Recurrences;

    var $Terms;

    function SubscriptionInfoType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'SubscriptionID' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'SubscriptionDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'EffectiveDate' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'RetryTime' => 
              array (
                'required' => false,
                'type' => 'dateTime',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Username' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Password' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Recurrences' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'Terms' => 
              array (
                'required' => false,
                'type' => 'SubscriptionTermsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
        $this->_attributes = array_merge($this->_attributes,
            array (
              'reattempt' => 
              array (
                'name' => 'reattempt',
                'type' => 'xs:string',
                'use' => 'required',
              ),
              'recurring' => 
              array (
                'name' => 'recurring',
                'type' => 'xs:string',
                'use' => 'required',
              ),
            ));
    }

    function getSubscriptionID()
    {
        return $this->SubscriptionID;
    }
    function setSubscriptionID($SubscriptionID, $charset = 'iso-8859-1')
    {
        $this->SubscriptionID = $SubscriptionID;
        $this->_elements['SubscriptionID']['charset'] = $charset;
    }
    function getSubscriptionDate()
    {
        return $this->SubscriptionDate;
    }
    function setSubscriptionDate($SubscriptionDate, $charset = 'iso-8859-1')
    {
        $this->SubscriptionDate = $SubscriptionDate;
        $this->_elements['SubscriptionDate']['charset'] = $charset;
    }
    function getEffectiveDate()
    {
        return $this->EffectiveDate;
    }
    function setEffectiveDate($EffectiveDate, $charset = 'iso-8859-1')
    {
        $this->EffectiveDate = $EffectiveDate;
        $this->_elements['EffectiveDate']['charset'] = $charset;
    }
    function getRetryTime()
    {
        return $this->RetryTime;
    }
    function setRetryTime($RetryTime, $charset = 'iso-8859-1')
    {
        $this->RetryTime = $RetryTime;
        $this->_elements['RetryTime']['charset'] = $charset;
    }
    function getUsername()
    {
        return $this->Username;
    }
    function setUsername($Username, $charset = 'iso-8859-1')
    {
        $this->Username = $Username;
        $this->_elements['Username']['charset'] = $charset;
    }
    function getPassword()
    {
        return $this->Password;
    }
    function setPassword($Password, $charset = 'iso-8859-1')
    {
        $this->Password = $Password;
        $this->_elements['Password']['charset'] = $charset;
    }
    function getRecurrences()
    {
        return $this->Recurrences;
    }
    function setRecurrences($Recurrences, $charset = 'iso-8859-1')
    {
        $this->Recurrences = $Recurrences;
        $this->_elements['Recurrences']['charset'] = $charset;
    }
    function getTerms()
    {
        return $this->Terms;
    }
    function setTerms($Terms, $charset = 'iso-8859-1')
    {
        $this->Terms = $Terms;
        $this->_elements['Terms']['charset'] = $charset;
    }
}
