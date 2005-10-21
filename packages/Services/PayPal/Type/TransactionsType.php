<?php
/**
 * @package Services_PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'Services/PayPal/Type/XSDType.php';

/**
 * TransactionsType
 * 
 * Contains information about multiple individual transations.
 *
 * @package Services_PayPal
 */
class TransactionsType extends XSDType
{
    var $Transaction;

    function TransactionsType()
    {
        parent::XSDType();
        $this->_namespace = 'urn:ebay:apis:eBLBaseComponents';
        $this->_elements = array_merge($this->_elements,
            array (
              'Transaction' => 
              array (
                'required' => true,
                'type' => NULL,
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
            ));
    }

    function getTransaction()
    {
        return $this->Transaction;
    }
    function setTransaction($Transaction, $charset = 'iso-8859-1')
    {
        $this->Transaction = $Transaction;
        $this->_elements['Transaction']['charset'] = $charset;
    }
}
