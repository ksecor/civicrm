<div id="help">
{ts}If you want accept contributions or membership fees online you must select a Payment Processing Service, register for the applicable type of account with that processor, and configure
CiviCRM with your account information.{/ts} {help id='processor-overview'}
</div>
<div class="form-item">
<fieldset><legend>{ts}Online Payments{/ts}</legend>
        <dl>
            <dt>{$form.paymentProcessor.label}</dt><dd>{$form.paymentProcessor.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Select the Processing Service you will be using for online contributions.{/ts}</dd>
            <dt>{$form.enableSSL.label}</dt><dd>{$form.enableSSL.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}Redirect online contribution page requests to a secure (https) URL?{/ts} {help id='enable-ssl'}</dd>
            <dt>{$form.paymentUsername_test.label}</dt><dd>{$form.paymentUsername_test.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}PayPal TEST account - username OR merchant email.{/ts} {help id='test-username'}</dd>
            <dt>{$form.paymentUsername_live.label}</dt><dd>{$form.paymentUsername_live.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}PayPal LIVE account - username OR merchant email.{/ts} {help id='live-username'}</dd>
            <div id="certificate_path">
                <dl>	
                <dt>{$form.paymentCertPath_test.label}</dt><dd>{$form.paymentCertPath_test.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}This setting is only used for PayPal Website Payments Pro/Express with certificate-based authentication. If you are using the recommended signature-based authentication, leave this field blank.{/ts}</dd>
                <dt>{$form.paymentCertPath_live.label}</dt><dd>{$form.paymentCertPath_live.html}</dd>
                <dt>&nbsp</dt><dd class="description">{ts}This setting is only used for PayPal Website Payments Pro/Express with certificate-based authentication. If you are using the recommended signature-based authentication, leave this field blank.{/ts}</dd>
                </dl>
            </div>
            <dt>{$form.paymentExpressButton.label}</dt><dd>{$form.paymentExpressButton.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}URL to the button image used for the PayPal Express service. Leave the default value unless you want to use a different button image.{/ts}</dd>
            <dt>{$form.paymentPayPalExpressTestUrl.label}</dt><dd>{$form.paymentPayPalExpressTestUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}TEST hostname for PayPal processing service. Leave the default unless you need to connect to a specific international PayPal processing host.{/ts}</dd>
            <dt>{$form.paymentPayPalExpressUrl.label}</dt><dd>{$form.paymentPayPalExpressUrl.html}</dd>
            <dt>&nbsp</dt><dd class="description">{ts}LIVE hostname for PayPal processing service. Leave the default unless you need to connect to a specific international PayPal processing host.{/ts}</dd>
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
</div> 
