<?

require_once "CommonAPI.php";

class TestofAdminPaymentInstrumentForm extends WebTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testAdminAddPaymentInstrument()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");
        
        if ($this->assertLink('Payment Instruments')){
            $this->clickLink('Payment Instruments');
        }
        
        if ($this->assertWantedText("New Payment Instrument")){
            $this->clickLinkById('newPaymentInstrument');
        }
        
        $this->assertWantedText("New Payment Instrument");
        
        $name        = 'AAA Payment';
        $description = 'Full Payment';
        $is_active   = '1';
          
        $this->setField('name',$name);  
        $this->setFieldById('description',$description);
        $this->setField('is_active',$is_active); 
           
        $this->clickSubmitByName('_qf_PaymentInstrument_next');
        
        $this->assertWantedText("The Payment Instrument \"$name\" has been saved.");
    }
    
    function testAdminEditPaymentInstrument()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");
        
        if ($this->assertLink('Payment Instruments')){
            $this->clickLink('Payment Instruments');
        }
        
        if ($this->assertWantedText("New Payment Instrument")){
            $this->clickLink('Edit');
        }
        
        $this->assertWantedText("Edit Payment Instrument");  
        
        $name        = 'AAA Payment';
        $description = 'Full Payment..Edited';
        $is_active   = '1';        
        
        $this->setFieldById('description',$description);
        $this->setField('is_active',$is_active);
        
        $this->clickSubmitByName("_qf_PaymentInstrument_next");
        $this->assertWantedText( "The Payment Instrument \"$name\" has been saved.");
    }
    
    function testAdminDisablePaymentInstrument()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");
        
        if ($this->assertLink('Payment Instruments')){
            $this->clickLink('Payment Instruments');
        }
        
        if ($this->assertWantedText("New Payment Instrument")) {
            $this->clickLink('Disable');
        }
    }

    function testAdminDeletePaymentInstrument()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Payment Instruments')){
            $this->clickLink('Payment Instruments');
        }

        if ($this->assertWantedText("New Payment Instrument")){ 
            $this->clickLink('Delete');
        }
        
        $this->assertWantedText("Delete Payment Instrument");
        $this->clickSubmitByname("_qf_PaymentInstrument_next");
        $this->assertWantedText(" Selected Payment Instrument has been deleted.");
    }
  }
?>