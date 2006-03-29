<?

require_once "CommonAPI.php";

class TestofAdminManagePremiumsForm extends WebTestCase
{
    function setUp()
    {
    }
    
    function tearDown()
    {
    }
    
    function testAddAdminManagePremiums()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Manage Premiums')){
            $this->clickLink('Manage Premiums');
        }
        
        $this->assertWantedText("Manage Premiums");
        
        if ($this->assertLinkById('newManagePremium')){
            $this->clickLinkById('newManagePremium');
        }

        $this->assertWantedText("New Premium");

        $name               = 'AAA Tea Mug';
        $sku                = '123';         
        $min_contribution   = '123';
        $price              = '12332' ; 
        $is_active          = '1';
        $period_type        = 'fixed';
        $duration_interval  = '2';
        $duration_unit      = 'day';    
        $frequency_interval = '3';
        $frequency_unit     = 'day';
 
        $this->setFieldById('name', $name);
        $this->setFieldById('sku', $sku);
        $this->setField('imageOption', 'default_image');
        $this->setFieldById('min_contribution', $min_contribution);
        $this->setFieldById('price', $price);
        $this->setFieldById('is_active', $is_active);
        $this->setFieldById('period_type', $period_type);
        $this->setFieldById('duration_interval', $duration_interval);
        $this->setFieldById('duration_unit', $duration_unit);
        $this->setFieldById('frequency_interval', $frequency_interval);
        $this->setFieldById('frequency_unit', $frequency_unit);

        $this->clickSubmitByName('_qf_ManagePremiums_upload');
        
        $this->assertWantedText("The Premium Product \"$name\" has been saved.");
    }
    
    function testAdminEditManagePremiums()
    {
        CommonAPI::startCiviCRM($this);

        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Manage Premiums')){
            $this->clickLink('Manage Premiums');
        }
        
        $this->assertWantedText("Manage Premiums");
        
        if ($this->assertLink('Edit')){
            $this->clickLink('Edit');
        }

        $this->assertWantedText("Edit Premium");
        
        $name = 'AAA Tea Mug';
        $sku  = '00000000';
        
        $this->setFieldById('sku', $sku);
        
        $this->clickSubmitByName('_qf_ManagePremiums_upload');        
        
        $this->assertWantedText("The Premium Product \"$name\" has been saved.");        
    }

    function testAdminPreviewManagePremiums()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Manage Premiums')){
            $this->clickLink('Manage Premiums');
        }
        
        $this->assertWantedText("Manage Premiums");
        
        if ($this->assertLink('Preview')){
            $this->clickLink('Preview');
        }

        $this->assertWantedText('Preview a Premium');
        $this->clickSubmitByName('_qf_ManagePremiums_next');
        $this->assertWantedText("Manage Premiums");
    }

    function testAdminDisableManagePremiums()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Manage Premiums')){
            $this->clickLink('Manage Premiums');
        }
        
        if ($this->assertWantedText("Manage Premiums")){
            $this->clickLink('Disable');
        }
    }

    function testAdminDeleteManagePremiums()
    {
        CommonAPI::startCiviCRM($this);
        
        if ($this->assertLink('Administer CiviCRM')){
            $this->clickLink('Administer CiviCRM');
        }

        $this->assertWantedText("CiviContribute");

        if ($this->assertLink('Manage Premiums')){
            $this->clickLink('Manage Premiums');
        }
        
        if ($this->assertWantedText("Manage Premiums")){
        $this->clickLink('Delete');
        }

        $this->assertWantedText("Delete Premium Product");
        $this->clickSubmitByname("_qf_ManagePremiums_next");
        $this->assertWantedText("Selected Premium Product type has been deleted.");
    }
}
?>