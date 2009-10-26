<?php

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'CiviTest/Contact.php';
require_once 'CiviTest/Custom.php';
require_once 'CiviTest/PaypalPro.php';

class CRM_Contribute_BAO_ContributionPageTest extends CiviUnitTestCase 
{
    
    function get_info( ) 
    {
        return array(
                     'name'        => 'Contribution BAOs',
                     'description' => 'Test all Contribute_BAO_ContributionPage methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
        $this->_contributionTypeID = $this->contributionTypeCreate();
       
    }
    
    /**
     * create() method (create Contribution Page)
     */
    function testCreate( )
    {
        
        $params = array (
                         'qfkey'                  => '9a3ef3c08879ad4c8c109b21c583400e',
                         'title'                  => 'Test Cotribution Page',
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'intro_text'             => '',
                         'footer_text'            => 'Thanks',
                         'is_for_organization'    => 0,
                         'for_organization'       => ' I am contributing on behalf of an organization',
                         'goal_amount'            => '400',
                         'is_active'              => 1,
                         'honor_block_title'      => '',
                         'honor_block_text'       => '',
                         'start_date'             => '20091022105900',
                         'start_date_time'        => '10:59AM',
                         'end_date'               => '19700101000000',
                         'end_date_time'          => '',
                         'is_credit_card_only'    => '',
                         );
        

         require_once 'CRM/Contribute/BAO/ContributionPage.php';
         $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
         
         $this->assertNotNull( $contributionpage->id);
         $this->assertType('int', $contributionpage->id);
         
    }

    /**
     *  test setIsActive() method
     */

    function testsetIsActive( )
    {
        
        $params = array (
                         'title'                  => 'Test Cotribution Page', 
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'is_active'              => 1,
                         );
            
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
        $id = $contributionpage->id;
        $is_active = 1;
        $pageActive = CRM_Contribute_BAO_ContributionPage::setIsActive($id ,$is_active );
        $this->assertEquals( $pageActive, true, 'Verify contribution types record deletion.');
    }
    
    
    /**
     * test setValues() method
     */
    
    function testsetValues( )
    {

        $params = array (
                         'title'                  => 'Test Cotribution Page', 
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'is_active'              => 1,
                         );
            
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
                
        $id = $contributionpage->id;
        $values = array ();
        $setValues  = CRM_Contribute_BAO_ContributionPage::setValues($id , &$values );
        //CRM_Core_Error::debug( '$setValues',$values );
        $this->assertEquals( $Values->title ,'Test Cotribution Page' , 'Verify contribution title.');
        $this->assertEquals( $Values->contribution_type_id,$this->_contributionTypeID, 'Verify contribution types id.');
        $this->assertEquals( $Values->is_active, 0 , 'Verify contribution is_active value.');
       
    }

    
    /**
     * test setMail() method
     */
    
    function testsendMail( )
    {
        $this->markTestSkipped( 'throws fatals' );
        $contactId = Contact::createIndividual( );
        $params = array (
                         'title'                  => 'Test Cotribution Page', 
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'is_active'              => 1,
                         );
        
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
        $contactId = Contact::createIndividual( );
        $ids = array ('contribution' => null );
        
        $params = array (
                         'contact_id'             => $contactId,
                         'currency'               => 'USD',
                         'contribution_type_id'   => 1,
                         'contribution_status_id' => 1,
                         'payment_instrument_id'  => 1,
                         'source'                 => 'STUDENT',
                         'receive_date'           => '20080522000000',
                         'receipt_date'           => '20080522000000',
                         'id'                     => $contributionpage->id,
                         'non_deductible_amount'  => 0.00,
                         'total_amount'           => 200.00,
                         'fee_amount'             => 5,
                         'net_amount'             => 195,
                         'trxn_id'                => '22ereerwww322323',
                         'invoice_id'             => '22ed39c9e9ee6ef6031621ce0eafe6da70',
                         'thankyou_date'          => '20080522' 
                         );
       
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $contribution = CRM_Contribute_BAO_Contribution::create( $params ,$ids );
        
        $params = array (
                         'id' => 1,
                         'title' => test,
                         'contribution_type_id' =>$this->_contributionTypeID,
                         'payment_processor_id' => 1,
                         'is_credit_card_only' => 0,
                         'is_monetary' => 1,
                         'is_recur' => 0,
                         'is_pay_later' => 1,
                         'pay_later_text' => 'I will send payment by check',
                         'pay_later_receipt' => 'test',
                         'is_allow_other_amount' => 0,
                         'is_for_organization' => 0,
                         'for_organization' => 'I am contributing on behalf of an organization.',
                         'is_email_receipt' => 1,
                         'is_active' => 1,
                         'amount_block_is_active' => 1,
                         'honor_block_is_active' => 0,
                         'start_date' => '2009-10-22 11:01:00',
                         'end_date' => '1970-01-01 00:00:00',
                         'amount' => Array(),
                         'custom_post_id' => null ,
                         'custom_pre_id' => 1,
                         'accountingCode' => null,
                         'footer_text' =>'test' ,
                         'contribution_id' =>$contribution->id, 
                         );
        
        $sendmail = CRM_Contribute_BAO_ContributionPage::sendMail($contactId, &$params , $isTest = false, $returnMessageText = false);
        
    }
     
    /**
     * test recurringNofify() method
     */
    
    function testrecurringNofify( )
    {
       $this->markTestSkipped( 'throws fatals' );
        
    }
    

    /**
     * test buildCustomDisplay() method
     */
    
    function testbuildCustomDisplay( )
    {      
        $this->markTestSkipped( 'throws fatals' );  
        $fieldsParams = array (
                               array (
                                      'field_name'       => 'first_name',
                                      'field_type'       => 'Individual',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 1,
                                      'label'            => 'First Name',
                                      'is_required'      => 1,
                                      'is_active'        => 1 ),
                               array (
                                      'field_name'       => 'last_name',
                                      'field_type'       => 'Individual',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 2,
                                      'label'            => 'Last Name',
                                      'is_required'      => 1,
                                      'is_active'        => 1 ),
                               array (
                                      'field_name'       => 'email',
                                      'field_type'       => 'Contact',
                                      'visibility'       => 'Public Pages and Listings',
                                      'weight'           => 3,
                                      'label'            => 'Email',
                                      'is_required'      => 1,
                                      'is_active'        => 1 )
                               );
              
        
        foreach( $fieldsParams as $value ){
            $ufField   = civicrm_uf_field_create( $profileId , $value );
        }
        $joinParams =  array(
                             'module'       => 'Profile',
                             'entity_table' => 'civicrm_contribution_page',
                             'entity_id'    => 1,
                             'weight'       => 1,
                             'uf_group_id'  => $profileId ,
                             'is_active'    => 1
                             );
        require_once 'api/v2/UFJoin.php';
        $ufJoin = civicrm_uf_join_add( $joinParams );
        
        $params = array (
                         'title'                  => 'Test Cotribution Page', 
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'is_active'              => 1,
                         'custom_post_id'         => $profileId
                         );
        
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
        
        $id = $contributionpage->id;
        $values = array ();
      
    }
    
    /**
     * test copy() method
     */
    
    function testcopy ( )
    {   
        $params = array (
                         'qfkey'                  => '9a3ef3c08879ad4c8c109b21c583400e',
                         'title'                  => 'Test Cotribution Page',
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'intro_text'             => '',
                         'footer_text'            => 'Thanks',
                         'is_for_organization'    => 0,
                         'for_organization'       => ' I am contributing on behalf of an organization',
                         'goal_amount'            => '400',
                         'is_active'              => 1,
                         'honor_block_title'      => '',
                         'honor_block_text'       => '',
                         'start_date'             => '20091022105900',
                         'start_date_time'        => '10:59AM',
                         'end_date'               => '19700101000000',
                         'end_date_time'          => '',
                         'is_credit_card_only'    => '',
                         );
        

         require_once 'CRM/Contribute/BAO/ContributionPage.php';
         $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
         $copycontributionpage = CRM_Contribute_BAO_ContributionPage::copy( $contributionpage->id );
         $this->assertEquals( $copycontributionpage->contribution_type_id, $this->_contributionTypeID, 'Check for Contribution type id.' );
         $this->assertEquals( $copycontributionpage->goal_amount , 400, 'Check for goal amount.' );
    }
    
    
    /**
     * test checkRecurPaymentProcessor() method
     */
    
    function testcheckRecurPaymentProcessor( )
    { 
        $paymentProcessor = PaypalPro::create( );
        $params = array (
                         'title'                  => 'Test Cotribution Page', 
                         'contribution_type_id'   => $this->_contributionTypeID,
                         'is_active'              => 1,  
                         'payment_processor_id'   => $paymentProcessor
                         );
        
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        

        $contributionpage = CRM_Contribute_BAO_ContributionPage::create( $params ,$ids );
        $id = $contributionpage->id;
        $checkRecurring  = CRM_Contribute_BAO_ContributionPage::checkRecurPaymentProcessor($id);
        $this->assertEquals( $checkRecurring , false , 'Check for false return.' );
    }

}
?>