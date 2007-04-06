{* this template is used to generate the PayPal Profile file (PPD) for certificate-based Authentication  *}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div id="help">
    <p>{ts 1="http://wiki.civicrm.org/confluence//x/Mi" 2=$docURLTitle}This form is used to create the PayPal API Profile file for use
    with PayPal's <strong>Certificate-based authentication method</strong>. If you are using the recommended
    <strong>Signature Credential</strong>, you do not need to use this form. Refer to the <a href="%1" target="_blank" title="%2">online documentation</a> for more info.{/ts}</p>
</div>

<div class="form-item">
<fieldset><legend>{ts}Create PayPal API Profile{/ts}</legend>
  
<dl>
   <dt>{$form.api_environment.label}</dt><dd>{$form.api_environment.html}</dd>
   <dt>&nbsp;</dt><dd class="description">{ts}Select <strong>sandbox</strong> to create test (sandbox) profile files in your CIVICRM_CONTRIBUTE_PAYMENT_TEST_CERT_PATH. Select <strong>live</strong> to create live profile files in your CIVICRM_CONTRIBUTE_PAYMENT_CERT_PATH.{/ts}</dd>
   <dt>{$form.api_username.label}</dt><dd>{$form.api_username.html}</dd>
   <dt>&nbsp;</dt><dd class="description">{ts}Enter the API Username you used when requesting the digital certificate for this environment.{/ts}</dd>
   <dt>{$form.uploadFile.label}</dt><dd>{$form.uploadFile.html}</dd>
   <dt>&nbsp;</dt><dd class="description">{ts}Browse to the digital certificate that you downloaded from PayPal. Choose the <strong>sandbox</strong> or <strong>live</strong> certificate based on the environment you are creating this profile for.{/ts}</dd>
   <dt>{$form.api_subject.label}</dt><dd>{$form.api_subject.html}</dd>
   <dt>&nbsp;</dt><dd class="description">{ts}Subject is left blank unless you are processing on behalf of a third party.{/ts}</dd>
</dl>
<dl>  
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
