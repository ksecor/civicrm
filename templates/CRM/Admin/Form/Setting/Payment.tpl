<div id="help">
<p>{ts}If you want accept contributions or membership fees online you must select a Payment Processing Service, register for the applicable type of account with that processor, and configure
CiviCRM with your account information.{/ts} {help id='processor-overview'}</p>
<p>{ts 1="http://wiki.civicrm.org/confluence//x/ihk"}<strong>For security reasons, some of the required payment processor settings are stored in your CiviCRM settings file (civicrm.settings.php). You
must input these values before your can process TEST or LIVE transactions. Refer to the <a href="%1" target="_blank" title="Payment Processor Configuration Guide. Opens in a new window.">Payment Processor Configuration Guide</a> for more information.</strong>{/ts}</p>
</div>
<div class="form-item">
<fieldset><legend>{ts}Online Payments{/ts}</legend>
        <dl>
            <dt>{$form.paymentProcessor.label}</dt><dd>{$form.paymentProcessor.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Select the Processing Service you will be using for online contributions.{/ts}</dd>
            <dt>{$form.enableSSL.label}</dt><dd>{$form.enableSSL.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Redirect online contribution page requests to a secure (https) URL?{/ts} {help id='enable-ssl'}</dd>
            <div id="paypal">
                <dl>
                <dt>{$form.paymentUsername_test.label}</dt><dd>{$form.paymentUsername_test.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}PayPal TEST account - username OR merchant email.{/ts} {help id='test-username'}</dd>
                <dt>{$form.paymentUsername_live.label}</dt><dd>{$form.paymentUsername_live.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}PayPal LIVE account - username OR merchant email.{/ts} {help id='live-username'}</dd>
                <div id="certificate_path">
                    <dl>	
                    <dt>{$form.paymentCertPath_test.label}</dt><dd>{$form.paymentCertPath_test.html}</dd>
                    <dt>&nbsp</dt><dd class="description">{ts}This setting is only used for PayPal Website Payments Pro/Express with Certificate authentication method. If you are using the recommended Signature authentication, leave this field blank.{/ts}</dd>
                    <dt>{$form.paymentCertPath_live.label}</dt><dd>{$form.paymentCertPath_live.html}</dd>
                    <dt>&nbsp</dt><dd class="description">{ts}This setting is only used for PayPal Website Payments Pro/Express with Certificate authentication method. If you are using the recommended Signature authentication, leave this field blank.{/ts}</dd>
                    </dl>
            </div>
            <dt>{$form.paymentExpressButton.label}</dt><dd>{$form.paymentExpressButton.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}URL to the button image used for the PayPal Express service. Keep the default value, unless you want to use a different button image.{/ts}</dd>
            <dt>{$form.paymentPayPalExpressTestUrl.label}</dt><dd>{$form.paymentPayPalExpressTestUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}TEST URL for the PayPal processing service gateway. Keep the default value, unless you need to connect to a specific international PayPal processing host.{/ts}</dd>
            <dt>{$form.paymentPayPalExpressUrl.label}</dt><dd>{$form.paymentPayPalExpressUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}LIVE URL for PayPal processing service gateway. Keep the default value, unless you need to connect to a specific international PayPal processing host.{/ts}</dd>
            </dl>           
            </div>
            
            <div id="google">
                <dl>
                <dt>{$form.merchantID_test.label}</dt><dd>{$form.merchantID_test.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}Google Checkout TEST account -  merchant id.{/ts} </dd>
                <dt>{$form.merchantID_live.label}</dt><dd>{$form.merchantID_live.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}Google Checkout LIVE account -  merchant id.{/ts} </dd>
                <dt>{$form.googleCheckoutButton.label}</dt><dd>{$form.googleCheckoutButton.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}URL to the button image used for the Google Checkout service. Keep the default value, unless you want to use a different button image.{/ts}</dd>
                <dt>{$form.googleCheckoutTestUrl.label}</dt><dd>{$form.googleCheckoutTestUrl.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}TEST URL for the Google Checkout processing service gateway. Keep the default value, unless you need to connect to a specific international Google Checkout processing host.{/ts}</dd>
                <dt>{$form.googleCheckoutUrl.label}</dt><dd>{$form.googleCheckoutUrl.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}LIVE URL for Google Checkout processing service gateway. Keep the default value, unless you need to connect to a specific international Google Checkout processing host.{/ts}</dd>
                
                </dl>
            </div>


            <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
<div class="spacer"></div>
</fieldset>
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="paymentProcessor"
    trigger_value       ="PayPal|PayPal_Express"
    target_element_id   ="certificate_path" 
    target_element_type ="block"
    field_type          ="select"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="paymentProcessor"
    trigger_value       ="Google_Checkout"
    target_element_id   ="google" 
    target_element_type ="block"
    field_type          ="select"
    invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="paymentProcessor"
    trigger_value       ="PayPal|PayPal_Express|PayPal_Standard|Moneris"
    target_element_id   ="paypal" 
    target_element_type ="block"
    field_type          ="select"
    invert              = 0
}

</div> 
