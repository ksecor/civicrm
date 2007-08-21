<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/XSDType.php';

/**
 * ScheduleDetailsType
 *
 * @package PayPal
 */
class ScheduleDetailsType extends XSDType
{
    /**
     * Schedule details for the Recurring Payment
     */
    var $Description;

    /**
     * Trial period of this schedule
     */
    var $TrialPeriod;

    var $PaymentPeriod;

    /**
     * The max number of payments the buyer can fail before this Recurring Payments
     * profile is cancelled
     */
    var $MaxFailedPayments;

    function ScheduleDetailsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Description' => 
              array (
                'required' => true,
                'type' => 'string',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'TrialPeriod' => 
              array (
                'required' => false,
                'type' => 'BillingPeriodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'PaymentPeriod' => 
              array (
                'required' => true,
                'type' => 'BillingPeriodDetailsType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'MaxFailedPayments' => 
              array (
                'required' => false,
                'type' => 'int',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getDescription()
    {
        return $this->Description;
    }
    function setDescription($Description, $charset = 'iso-8859-1')
    {
        $this->Description = $Description;
        $this->_elements['Description']['charset'] = $charset;
    }
    function getTrialPeriod()
    {
        return $this->TrialPeriod;
    }
    function setTrialPeriod($TrialPeriod, $charset = 'iso-8859-1')
    {
        $this->TrialPeriod = $TrialPeriod;
        $this->_elements['TrialPeriod']['charset'] = $charset;
    }
    function getPaymentPeriod()
    {
        return $this->PaymentPeriod;
    }
    function setPaymentPeriod($PaymentPeriod, $charset = 'iso-8859-1')
    {
        $this->PaymentPeriod = $PaymentPeriod;
        $this->_elements['PaymentPeriod']['charset'] = $charset;
    }
    function getMaxFailedPayments()
    {
        return $this->MaxFailedPayments;
    }
    function setMaxFailedPayments($MaxFailedPayments, $charset = 'iso-8859-1')
    {
        $this->MaxFailedPayments = $MaxFailedPayments;
        $this->_elements['MaxFailedPayments']['charset'] = $charset;
    }
}
