<div class="form-item">
<fieldset>
<legend>
{ts}Label Contact(s){/ts}
</legend>
<dl>
<dt>{$form.label_id.label}</dt><dd>{$form.label_id.html}</dd>
<dt>{$form.location_type_id.label}</dt><dd>{$form.location_type_id.html}</dd>
<dt></dt><dd>{$form.do_not_mail.html} {$form.do_not_mail.label}</dd>
<dt></dt><dd>{$form.merge_same_address.html} {$form.merge_same_address.label}</dd>
<dt></dt>
<dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
