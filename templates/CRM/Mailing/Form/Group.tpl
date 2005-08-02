{include file="CRM/WizardHeader.tpl}

<div class="form-item">
<fieldset>
  <legend>Select Mailing Recipients</legend>
  <dl>
  <table>
  {if $groupCount > 0}
    <tr><th class="label">{$form.includeGroups.label}</th></tr>
    <tr><td>{$form.includeGroups.html}</td></tr>
    <tr><th class="label">{$form.excludeGroups.label}</th></tr>
    <tr><td>{$form.excludeGroups.html}</td></tr>
  {/if}
  {if $mailingCount > 0}
  <tr><th class="label">{$form.includeMailings.label}</th></tr>
  <tr><td>{$form.includeMailings.html}</td></tr>
  <tr><th class="label">{$form.excludeMailings.label}</th></tr>
  <tr><td>{$form.excludeMailings.html}</td></tr>
  {/if}
  </table>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
