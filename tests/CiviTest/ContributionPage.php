<?php
class ContributionPage extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a Contribution Page
     *
     * @return $contributionPage id of created Contribution Page
     */
    function create( ) 
    {
        require_once "CRM/Core/DAO/PaymentProcessor.php";
        $paymentProcessor =& new CRM_Core_DAO_PaymentProcessor( );
        $paymentParams = array(
                               'domain_id' => 1,
                               'name' => 'demo',
                               'payment_processor_type' => 'PayPal',
                               'is_active' => 1,
                               'is_default' => 0,
                               'is_test' => 1,
                               'user_name' => 'bharat_axnzoom_api1.yahoo.com',
                               'password' => 'JTU844G729L2ZTJZ',
                               'signature' => 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-Amskj10OWUIlV7zig.SePDYaN46Z',
                               'url_site' => 'https://www.sandbox.paypal.com/',
                               'url_api' => 'https://api-3t.sandbox.paypal.com/',
                               'url_button' => 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',
                               'class_name' => 'Payment_PayPalImpl',
                               'billing_mode' => 3
                               );
        $paymentProcessor->copyValues( $paymentParams );
        $paymentProcessor->save( );
        
        require_once "CRM/Contribute/BAO/ContributionPage.php";        
        $params = array('title'                    => 'Help Test CiviCRM!',
                        'intro_text'               => 'Created for Test Coverage Online Contribution Page',
                        'contribution_type_id'     => 1,
                        'payment_processor_id'     => $paymentProcessor->id,
                        'is_monetary'              => 1,
                        'is_allow_other_amount'    => 1,
                        'min_amount'               => 10,
                        'max_amount'               => 10000,
                        'goal_amount'              => 100000,
                        'thankyou_title'           => 'Thanks for Your Support!',
                        'is_email_receipt'         => 1,
                        'receipt_from_email'       => 'donations@civicrm.org',
                        'cc_receipt'               => 'receipt@example.com',
                        'bcc_receipt'              => 'bcc@example.com',
                        'is_active'                => 1
                        );
        
        $contributionPage = CRM_Contribute_BAO_ContributionPage::create( $params );
        return $contributionPage->id;
    }
  
}

?>