<div class="form-item">
<fieldset><legend>{ts}Address Formatting{/ts}</legend>
      <dl>
        <dt>{$form.addressFormat.label}</dt><dd>{$form.addressFormat.html|crmReplace:class:'huge'}</dd>      
        <dt>&nbsp</dt><dd class="description">{ts}Format of address display.{/ts}</dd>
        <dt>{$form.maxLocationBlocks.label}</dt><dd>{$form.maxLocationBlocks.html}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Location Blocks display for Contacts.{/ts}</dd>
        <dt>{$form.includeCounty.label}</dt><dd>{$form.includeCounty.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts} if yes, includes county select box in address block for contact and also in profile.{/ts}</dd>
        <dt>{$form.AddressStdProvider.label}</dt><dd>{$form.AddressStdProvider.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Address Standarization Provider.{/ts}</dd>
        <dt>{$form.AddressStdUserID.label}</dt><dd>{$form.AddressStdUserID.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Address Standarization user Id.{/ts}</dd>
        <dt>{$form.AddressStdURL.label}</dt><dd>{$form.AddressStdURL.html}</dd>    
        <dt>&nbsp</dt><dd class="description">{ts}Address Standarization URL.{/ts}</dd>
        
         <dt></dt><dd>{$form.buttons.html}</dd>
       </dl>
<div class="spacer"></div>
</fieldset>
</div>
