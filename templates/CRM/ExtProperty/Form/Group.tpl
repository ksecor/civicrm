<form {$form.attributes}>
<fieldset>
<div class="form-item">
  {$form.title.label} {$form.title.html}
</div>
<div class="form-item">
  {$form.description.label} {$form.description.html}
</div>
<div class="form-item">
  {$form.is_active.html} {$form.is_active.label}
</div>
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</fieldset>
</form>
