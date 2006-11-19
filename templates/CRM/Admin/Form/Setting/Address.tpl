<div class="form-item">
<fieldset><legend>{ts}Address Formatting{/ts}</legend>
      <dl>
        <dt>{$form.addressFormat.label}</dt><dd>{$form.addressFormat.html|crmReplace:class:huge}</dd>      
        <dt>&nbsp</dt><dd class="description">{ts}Contact address display format.{/ts}</dd>
        <dt>{$form.maxLocationBlocks.label}</dt><dd>{$form.maxLocationBlocks.html|crmReplace:class:two}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Maximum number of different locations/addresses that can be entered for a contact.{/ts}</dd>
        <dt>{$form.includeCounty.label}</dt><dd>{$form.includeCounty.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Do you want a County select field to be included in contact address input forms?{/ts}</dd>
    </dl>
</fieldset>
<fieldset><legend>{ts}Address Standardization{/ts}</legend>
    <div class="description">
        {ts}CiviCRM includes an optional plugin for interfacing the the United States Postal Services (USPS) Address Standardization web service.
            You must register to use the USPS service at <a href="http://www.usps.com/webtools/address.htm">http://www.usps.com/webtools/address.htm</a>.
            If you are approved, they will provide you with a User ID and the URL for the service.{/ts}
    </div>
      <dl>
        <dt>{$form.AddressStdProvider.label}</dt><dd>{$form.AddressStdProvider.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Address Standarization Provider. Currently, only 'USPS' is supported.{/ts}</dd>
        <dt>{$form.AddressStdUserID.label}</dt><dd>{$form.AddressStdUserID.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}USPS-provided User ID.{/ts}</dd>
        <dt>{$form.AddressStdURL.label}</dt><dd>{$form.AddressStdURL.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}USPS-provided web service URL.{/ts}</dd>
    </dl>
</fieldset>
<dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
</dl>
<div class="spacer"></div>
</div>
