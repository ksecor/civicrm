<?php

require_once "CRM/Core/DAO/PaymentProcessor.php";
class PaypalPro extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a payment processor of type Paypal Pro
     *
     * @return $paymentProcessor id of created payment processor
     */
    function create( ) 
    {

        $paymentProcessor =& new CRM_Core_DAO_PaymentProcessor( );
        $domain =  CRM_Core_Config::domainID( );
        $paymentParams = array(
                               'domain_id'              => $domain,
                               'name'                   => 'demo',
                               'payment_processor_type' => 'PayPal',
                               'is_active'              => 1,
                               'is_default'             => 0,
                               'is_test'                => 1,
                               'user_name'              => 'bharat_axnzoom_api1.yahoo.com',
                               'password'               => 'JTU844G729L2ZTJZ',
                               'signature'              => 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-Amskj10OWUIlV7zig.SePDYaN46Z',
                               'url_site'               => 'https://www.sandbox.paypal.com/',
                               'url_api'                => 'https://api-3t.sandbox.paypal.com/',
                               'url_button'             => 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',
                               'class_name'             => 'Payment_PayPalImpl',
                               'billing_mode'           => 3
                               );
        $paymentProcessor->copyValues( $paymentParams );
        $paymentProcessor->save( );
        return $paymentProcessor->id;
    }
  
    /*
     * Helper function to delete a PayPal Pro 
     * payment processor
     * @param  int $id - id of the PayPal Pro payment processor
     * to be deleted
     * @return boolean true if payment processor deleted, false otherwise
     * 
     */
    function delete( $id ) 
    {
        $pp     = & new CRM_Core_DAO_PaymentProcessor( );
        $pp->id = $id; 
        if ( $pp->find( true ) ) {
            $result = $pp->delete( );
        }
        return $result;
    }
}

?>