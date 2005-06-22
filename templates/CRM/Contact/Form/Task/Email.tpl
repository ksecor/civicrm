<div class="form-item">
<fieldset>
<legend>{ts}Send An Email{/ts}</legend>
<dl>
<dt>From</dt><dd>{$from|escape}</dd>
{if $single eq false}
<dt>To</dt><dd>{$to|escape}</dd>
{else}
<dt>{$form.to.label}</dt><dd>{$form.to.html}</dd>
{/if}
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
<dt>{$form.message.label}</dt><dd>{$form.message.html}</dd>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
</div>
