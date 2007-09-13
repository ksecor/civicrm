{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset>
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html} {help id="mailing-name"}</dd>
  </dl>
</fieldset>

<fieldset>
  <legend>{ts}Mailing Recipients{/ts}</legend>
  {strip}
   
  <table>
  {if $groupCount > 0}
    <tr><th class="label">{$form.includeGroups.label} {help id="include-groups"}</th></tr>
    <tr><td>{$form.includeGroups.html}</td></tr>
    <tr><th class="label">{$form.excludeGroups.label} {help id="exclude-groups"}</th></tr>
    <tr><td>{$form.excludeGroups.html}</td></tr>
  {/if}
  {if $mailingCount > 0}
  <tr><th class="label">{$form.includeMailings.label} {help id="include-mailings"}</th></tr>
  <tr><td>{$form.includeMailings.html}</td></tr>
  <tr><th class="label">{$form.excludeMailings.label} {help id="exclude-mailings"}</th></tr>
  <tr><td>{$form.excludeMailings.html}</td></tr>
  {/if}
  </table>
    
  {/strip}
    <dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
