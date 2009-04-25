  <table class="form-layout">
    <tr><td class="label">{$form.title.label}</td><td>{$form.title.html}</td></tr>
    <tr><td class="label">{$form.to_emails.label}</td><td>{$form.to_emails.html|crmReplace:class:huge}</td></tr>
    <tr><td class="label">{$form.cc_emails.label}</td><td>{$form.cc_emails.html|crmReplace:class:huge}</td></tr> 
    <tr><td class="label">{$form.report_header.label}</td><td>{$form.report_header.html}</td></tr>
    <tr><td class="label">{$form.report_footer.label}</td><td>{$form.report_footer.html}</td></tr>
  </table>
