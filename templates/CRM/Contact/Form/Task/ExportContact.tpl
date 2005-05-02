<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Export contacts.
</legend>

{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>
