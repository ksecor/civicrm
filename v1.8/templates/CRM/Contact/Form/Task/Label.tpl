<div class="form-item">
<fieldset>
<legend>
{ts}Label Contact(s){/ts}
</legend>
<dl>
<dt>{$form.label_id.label}</dt><dd>{$form.label_id.html}</dd>
<dt>{$form.location_type_id.label}</dt><dd>{$form.location_type_id.html}</dd>
<dt></dt>
<dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
