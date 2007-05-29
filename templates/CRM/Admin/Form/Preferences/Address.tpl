<div class="form-item">
<fieldset><legend>{ts}Mailing Labels{/ts}</legend>
      <dl>
        <dt>{$form.mailing_format.label}</dt><dd>{$form.mailing_format.html|crmReplace:class:huge}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Address format for mailing labels.<br />Use the {literal}{state_province}{/literal} token for state/province abbreviation or {literal}{state_province_name}{/literal} for full name.{/ts}{help id='label-tokens'}</dd>
        <dt>{$form.individual_name_format.label}</dt><dd>{$form.individual_name_format.html|crmReplace:class:huge}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Formatting for individual contact names when {literal}{contact_name}{/literal} token is included in mailing labels.{/ts} {help id='name-tokens'}</dd>
    </dl>
</fieldset>
<fieldset><legend>{ts}Address Display{/ts}</legend>
      <dl>
        <dt>{$form.address_format.label}</dt><dd>{$form.address_format.html|crmReplace:class:huge}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Format for displaying addresses in the Contact Summary and Event Information screens.<br />Use {literal}{state_province}{/literal} for state/province abbreviation or {literal}{state_province_name}{/literal} for state province name.{/ts}{help id='address-tokens'}</dd>
      </dl>
</fieldset>
<fieldset><legend>{ts}Address Editing{/ts}</legend>
      <dl>
        <dt>{$form.address_options.label}</dt><dd>{$form.address_options.html}</dd>
        <dt>&nbsp;</dt><dd class="description">{ts}Select the fields to be included when editing a contact or event address.{/ts}</dd>
        <dt>{$form.location_count.label}</dt><dd>{$form.location_count.html|crmReplace:class:two}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Enter the maximum number of different locations/addresses that can be entered for a contact.{/ts}</dd>
      </dl>
</fieldset>
<fieldset><legend>{ts}Address Standardization{/ts}</legend>
    <div class="description">
        {ts}CiviCRM includes an optional plugin for interfacing the the United States Postal Services (USPS) Address Standardization web service. You must register to use the USPS service at <a href="http://www.usps.com/webtools/address.htm">http://www.usps.com/webtools/address.htm</a>. If you are approved, they will provide you with a User ID and the URL for the service.{/ts}
    </div>
      <dl>
        <dt>{$form.address_standardization_provider.label}</dt><dd>{$form.address_standardization_provider.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Address Standardization Provider. Currently, only 'USPS' is supported.{/ts}</dd>
        <dt>{$form.address_standardization_userid.label}</dt><dd>{$form.address_standardization_userid.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}USPS-provided User ID.{/ts}</dd>
        <dt>{$form.address_standardization_url.label}</dt><dd>{$form.address_standardization_url.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}USPS-provided web service URL.{/ts}</dd>
    </dl>
</fieldset>
<dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
</dl>
<div class="spacer"></div>
</div>
