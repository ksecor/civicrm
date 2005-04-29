<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Save this search
</legend>

<p>
Searching for {$qill}

<div class="form-item">
{$form.ss_name.label} {$form.ss_name.html}
</div>
<div class="form-item">
{$form.ss_description.label} {$form.ss_description.html}
</div>

{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>