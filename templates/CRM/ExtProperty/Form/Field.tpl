<form {$form.attributes}>
<br />
<fieldset>
<div class="form-item">
  {$form.title.label} {$form.title.html}
</div>
<div class="form-item">
  {$form.description.label} {$form.description.html}
</div>
<div class="form-item">
  {$form.data_type.label} {$form.data_type.html}
</div>
<div class="form-item">
  {$form.type.label} {$form.type.html}
</div>
<div class="form-item">
  {$form.default_value.label} {$form.default_value.html}
</div>
<div class="form-item">
  {$form.is_required.label} {$form.is_required.html}
</div>
<div class="form-item">
  {$form.is_active.label} {$form.is_active.html}
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</fieldset>
</form>
