<div class="form-item">
<fieldset><legend>{ts}Address Formatting{/ts}</legend>
      <dl>
        <dt>{$form.addressFormat.label}</dt><dd>{$form.addressFormat.html|crmReplace:class:'listing-box'}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Format of address display.{/ts}</dd>
        <dt>{$form.maxLocationBlocks.label}</dt><dd>{$form.maxLocationBlocks.html}</dd>
        <dt>&nbsp</dt><dd class="description">{ts}Location Blocks display for Contacts.{/ts}</dd>
       </dl>
       <dl>
         <dt></dt><dd>{$form.buttons.html}</dd>
       </dl>
<div class="spacer"></div>
</fieldset>
</div>
