<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Delete Contacts
</legend>
Are you sure you want to Delete the below Contacts. A Delete operation cannot be undone.
{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>