{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset>
  <legend></legend>
  <dl>
    <dt class="label">{$form.test.label}</dt><dd>{$form.test.html}</dd>
    <dt class="label">{$form.test_email.label}</dt><dd>{$form.test_email.html} {ts}(filled with your contact's token values){/ts}</dd>
    <dt class="label">{$form.test_group.label}</dt><dd>{$form.test_group.html}</dd>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>

<div class="data-group" id="previewMailing_show">
  <a href="#" onclick="hide('previewMailing_show'); show('previewMailing'); getElementById('previewMailing').style.visibility = 'visible'; return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Preview Mailing{/ts}</label><br />
</div>
<div id="previewMailing" style="visibility: hidden;">
  <fieldset>
    <legend><a href="#" onclick="hide('previewMailing'); show('previewMailing_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Preview Mailing{/ts}</legend>
    <dl>
      <dt class="label">{ts}Text Version:{/ts}</dt><dd><iframe height="300" src="{$preview.text_link}" width="80%"><a href="{$preview.text_link}" onclick="window.open(this.href); return false;">{ts}Text Version{/ts}</a></iframe></dd></dt>
      <dt class="label">{ts}HTML Version:{/ts}</dt><dd><iframe height="300" src="{$preview.html_link}" width="80%"><a href="{$preview.html_link}" onclick="window.open(this.href); return false;">{ts}HTML Version{/ts}</a></iframe></dd></dt>
    </dl>
  </fieldset>
</div>

</div>
