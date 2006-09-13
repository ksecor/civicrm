{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset>
  <legend></legend>
  <dl>
    <dt class="label">{ts}Preview Mailing:{/ts}</dt><dd><a href="{$preview.text_link}" onclick="window.open(this.href); return false;">{ts}text version{/ts}</a> | <a href="{$preview.html_link}" onclick="window.open(this.href); return false;">{ts}HTML version{/ts}</a>
    <dt class="label">{$form.test.label}</dt><dd>{$form.test.html}</dd>
    <dt class="label">{$form.test_email.label}</dt><dd>{$form.test_email.html} {ts}(filled with your contact's token values){/ts}</dd>
    <dt class="label">{$form.test_group.label}</dt><dd>{$form.test_group.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
