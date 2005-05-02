<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Print the contacts below
</legend>

<p>
{include file="CRM/Contact/Form/Task.tpl"}
</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>