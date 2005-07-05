{include file="CRM/WizardHeader.tpl}

<div class="form-item">
<fieldset>
  <legend>Name Mailing</legend>
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
{if $form.template}
    <dt>{$form.template.label}</dt><dd>{$form.template.html}</dd>
{/if}
    <dt>{$form.is_template.label}</dt><dd>{$form.is_template.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>