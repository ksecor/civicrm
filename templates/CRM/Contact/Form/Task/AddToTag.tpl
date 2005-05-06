<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<div class="form-item">
<fieldset>
<legend>
Tag Contact(s)
</legend>
<dl>
<dt>{$form.category_id.label}</dt><dd>{$form.category_id.html}</dd>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
</div>

</form>
