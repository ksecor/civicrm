<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Choose Tag you would like to add the below contacts to.
</legend>

<div class="form-item">
{$form.category_id.label} {$form.category_id.html}
</div>

{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>
