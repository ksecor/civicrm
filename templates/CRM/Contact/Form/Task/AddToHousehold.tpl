<fieldset>
<legend>
{ts}Choose Household and Relationship that you would like to add the below contacts to.{/ts}
</legend>

<div class="form-item">
{$form.group.label} {$form.group.html}
</div>
<div class="form-item">
{$form.status.label} {$form.status.html}
</div>

{include file="CRM/Contact/Form/Task.tpl"}

</fieldset>
<p>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>
