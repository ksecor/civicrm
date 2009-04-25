  <table class="form-layout">
    <tr><td class="label">{$form.title.label}</td><td>{$form.title.html}</td></tr>
    <tr><td class="label">{$form.report_header.label}</td><td>{$form.report_header.html}</td></tr>
    <tr><td class="label">{$form.report_footer.label}</td><td>{$form.report_footer.html}</td></tr>
  </table>
<fieldset>
  <legend>{ts}Email Settings{/ts}</legend>
  <table class="form-layout">
    <tr><td class="label">{$form.email_subject.label}</td><td>{$form.email_subject.html|crmReplace:class:huge}</td></tr>
    <tr><td class="label">{$form.email_to.label}</td><td>{$form.email_to.html|crmReplace:class:huge}</td></tr>
    <tr><td class="label">{$form.email_cc.label}</td><td>{$form.email_cc.html|crmReplace:class:huge}</td></tr> 
  </table>
</fieldset>

