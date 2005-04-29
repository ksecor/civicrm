<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Choose and Group and Status you would like to add the below contacts to.
</legend>

<div class="form-item">
{$form.group_id.label} {$form.group_id.html}
</div>
{*
<div class="form-item">
{$form.status.label} {$form.status.html}
</div>
*}

{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>
