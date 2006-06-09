<?

require_once "CommonAPI.php";

class TestofAdminContributionForm extends WebTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }

    function testAdminAddContribution()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Contribution Types')){
            $this->clickLink('Contribution Types');
        }
        
        if ($this->assertWantedText("New Contribution Type")){
            $this->clickLinkById('newContributionType');
        }

        $this->assertWantedText("New Contribution Type");
        
        $name            = 'AAA Contribution';
        $description     = 'healthy';
        $accounting_code = '01';
        $is_deductible   = 1;
        $is_active       = 1;         
        
        $this->setField('name',$name);  
        $this->setField('description',$description);
        $this->setField('accounting_code',$accounting_code);
        $this->setField('is_deductible',$is_deductible);
        $this->setField('is_active',$is_active);        
               
        $this->clickSubmitByName("_qf_ContributionType_next");
        
        $this->assertWantedText("The contribution type \"$name\" has been saved.");
    }

    function testAdminEditContribution()
    {
        CommonAPI::startCiviCRM($this);
 
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Contribution Types')){
            $this->clickLink('Contribution Types');
        }

        if ($this->assertWantedText("New Contribution Type")){
            $this->clickLink('Edit');
        }

        $this->assertWantedText("Edit Contribution Type");  

        $name            = 'BBB Contribution';
        $description     = 'healthy';
        $accounting_code = '02';
        $is_deductible   = 1;
        $is_active       = 1; 

        $this->setFieldById('name',$name);  
        $this->setFieldById('description',$description);
        $this->setFieldById('accounting_code',$accounting_code);
        $this->setField('is_deductible',$is_deductible);
        $this->setField('is_active',$is_active);

        $this->clickSubmitByName("_qf_ContributionType_next");
        $this->assertWantedText( "The contribution type \"$name\" has been saved.");
   
    }

    function testAdminDisableContribution()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Contribution Types')){
            $this->clickLink('Contribution Types');
        }

        if ($this->assertWantedText("New Contribution Type")){
            $this->clickLink('Disable');
        }                
    }  

    function testAdminDeleteContribution()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }
        
        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Contribution Types')){
            $this->clickLink('Contribution Types');
        }

        if ($this->assertWantedText("New Contribution Type")){ 
            $this->clickLink('Delete');
        }
        
        $this->assertWantedText("Delete Contribution Type");
        $this->clickSubmitByname("_qf_ContributionType_next");
        $this->assertWantedText(" Selected contribution type has been deleted.");
     }
  }
?>