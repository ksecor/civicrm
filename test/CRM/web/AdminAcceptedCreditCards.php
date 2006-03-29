<?

require_once "CommonAPI.php";

class TestofAdminAcceptedCreditCardsForm extends WebTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }

    function testAdminAddAcceptedCreditCards()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Accepted Credit Cards')){
            $this->clickLink('Accepted Credit Cards');
        }
        
        if ($this->assertWantedText("New Credit Card")){
            $this->clickLinkById('newAcceptedCreditCard');
        }
        
        $this->assertWantedText("New Credit Card");
        
        $name      = 'AAA CreditCard';
        $title     = 'used card';
        $is_active = '1';       
        
        $this->setFieldById('name', $name);  
        $this->setFieldById('title', $title);
        $this->setField('is_active', $is_active);              
               
        $this->clickSubmitByName("_qf_AcceptCreditCard_next"); 
        
        $this->assertWantedText("The Credit Card \"$name\" has been saved.");
    }

    function testAdminEditAcceptedCreditCards()
    {
        CommonAPI::startCiviCRM($this);
 
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Accepted Credit Cards')){
            $this->clickLink('Accepted Credit Cards');
        }

        if ($this->assertWantedText("New Credit Card")){
            $this->clickLink('Edit');
        }

        $this->assertWantedText("Edit Credit Card");  

        $name      = 'BBB Credit';
        $title     = 'half used card';
        $is_active = '1';        

        $this->setField('name',$name);  
        $this->setField('title',$title);
        $this->setField('is_active',$is_active);
        
        $this->clickSubmitByName("_qf_AcceptCreditCard_next");
        $this->assertWantedText( "The Credit Card \"$name\" has been saved.");
   
    }

    function testAdminDisableAcceptedCreditCards()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Accepted Credit Cards')){
            $this->clickLink('Accepted Credit Cards');
        }

        if ($this->assertWantedText("New Credit Card")){
            $this->clickLink('Disable');
        }
    }
      
    function testAdminDeleteAcceptedCreditCards()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Accepted Credit Cards')){
            $this->clickLink('Accepted Credit Cards');
        }

        if ($this->assertWantedText("New Credit Card")){ 
            $this->clickLink('Delete');
        }
        
        $this->assertWantedText("Delete Credit Card");
        $this->clickSubmitByname("_qf_AcceptCreditCard_next");
        $this->assertWantedText(" Selected Credit Card has been deleted.");
     }
}
?>