<form {$form.attributes}>

{include file="CRM/formCommon.tpl"}

<fieldset>
<legend>
Save this search
</legend>

<p>
Searching for {$qill}

<div class="form-item">
{$form.name.label} {$form.name.html}
</div>
<div class="form-item">
{$form.description.label} {$form.description.html}
</div>

{*{include file="CRM/Contact/Form/Task.tpl"}*}

</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

</form>

<hr />
There are {$totalSelectedContact} contacts in the resultset currently.
<hr/>