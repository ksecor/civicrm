<div class="form-item">
<fieldset><legend>{ts}Online Payments{/ts}</legend>
<div id="help">
{ts} If using CiviContribute for Online Contributions,must obtain a Payment Processor (merchant) account and configure  site and the settings below with that account information.Start with a Test Server (e.g. Sandbox) account, and configure both the LIVE and TEST settings below using test (sandbox) account info.Once you are ready to go live, update the LIVE settings to use live account info{/ts}
</div>
        <dl>
            <dt>{$form.enableSSL.label}</dt><dd>{$form.enableSSL.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}If Yes is selected, CiviCRM will automatically redirect requests for online contribution pages to an https (SSL secured) URL.{/ts}</dd>
            <dt>{$form.paymentProcessor.label}</dt><dd>{$form.paymentProcessor.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Valid values are 'PayPal' (Website Payments Pro), 'PayPal_Express', and 'Moneris'.{/ts}</dd>
            <dt>{$form.paymentExpressButton.label}</dt><dd>{$form.paymentExpressButton.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}URL to the button image used for "express" option checkout.{/ts}</dd>
            <dt>{$form.paymentUsername_test.label}</dt><dd>{$form.paymentUsername_test.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}API Test Username.{/ts}</dd>
            <div id="certificate_path"><dl>	
                <dt>{$form.paymentCertPath_test.label}</dt><dd>{$form.paymentCertPath_test.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}File system path where API Profile files should be created and stored.{/ts}</dd>                <dt>{$form.paymentCertPath_live.label}</dt><dd>{$form.paymentCertPath_live.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}File system path where API Profile files should be created and stored.{/ts}</dd>             </dl></div>
            <dt>{$form.paymentPayPalExpressTestUrl.label}</dt><dd>{$form.paymentPayPalExpressTestUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Hostname for "PayPal Express" button submit in test-drive mode.{/ts}</dd>       
            <dt>{$form.paymentUsername_live.label}</dt><dd>{$form.paymentUsername_live.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}API Live Username.{/ts}</dd>
            <dt>{$form.paymentPayPalExpressUrl.label}</dt><dd>{$form.paymentPayPalExpressUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Hostname for "PayPal Express" button submit in live mode.{/ts}</dd>
            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
</div> 
{literal}
    <script type="text/javascript">
      
     if ((document.getElementsByName("paymentProcessor")[0].value == "PayPal") || (document.getElementsByName("paymentProcessor")[0].value == "PayPal_Express") ||(document.getElementsByName("paymentProcessor")[0].value == "PayPal_Standard") ) {
        show('certificate_path');
     } else {
        hide('certificate_path');
	 }
    
	function showHideCertificatePath(){
	   if ((document.getElementsByName("paymentProcessor")[0].value == "PayPal") || (document.getElementsByName("paymentProcessor")[0].value == "PayPal_Express") ||(document.getElementsByName("paymentProcessor")[0].value == "PayPal_Standard") ) {

          show('certificate_path');
        }   else {
          hide('certificate_path');
	   }
	} 
     </script>
{/literal}
